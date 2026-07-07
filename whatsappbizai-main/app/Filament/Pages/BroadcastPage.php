<?php

namespace App\Filament\Pages;

use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class BroadcastPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view            = 'filament.pages.broadcast';
    protected static ?string $navigationIcon  = 'heroicon-m-megaphone';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int    $navigationSort  = 4;
    protected static ?string $slug            = 'broadcast';

    public ?array $data = [];

    public string $previewHtml = '';
    public bool   $showPreview = false;

    public function getHeading(): string
    {
        return __('app.admin.broadcast_title', [], app()->getLocale()) ?: '📤 Broadcast';
    }

    public function mount(): void
    {
        $this->form->fill([
            'target'  => 'all',
            'ai_goal' => null,
            'message' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('target')
                    ->label(__('app.client.broadcast.send_to'))
                    ->options([
                        'all'       => __('app.client.broadcast.all'),
                        'clients'   => __('app.client.broadcast.clients'),
                        'prospects' => __('app.client.broadcast.prospects'),
                    ])
                    ->required()
                    ->default('all'),

                Forms\Components\TextInput::make('ai_goal')
                    ->label(__('app.client.broadcast.ai_goal'))
                    ->placeholder(__('app.client.broadcast.ai_goal_placeholder'))
                    ->maxLength(500),

                RichEditor::make('message')
                    ->label(__('app.client.broadcast.message'))
                    ->required()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function previewContent(): void
    {
        $this->previewHtml = $this->data['message'] ?? '';
        $this->showPreview = true;
    }

    public function draftWithAI(): void
    {
        $data = $this->form->getState();
        $goal = $data['ai_goal'] ?? 'Promote our services to existing clients';

        $audience = match($data['target'] ?? 'all') {
            'clients'   => 'existing clients',
            'prospects' => 'prospects who have not yet purchased',
            default     => 'all contacts',
        };

        $prompt  = "Write a professional broadcast message for a SaaS called WhatsAppBizAI.\n";
        $prompt .= "Goal: {$goal}\n";
        $prompt .= "Audience: {$audience}\n";
        $prompt .= "Language: French\n";
        $prompt .= "Tone: Friendly, direct, engaging.\n";
        $prompt .= "Include a clear call-to-action. Keep it under 150 words.\n";
        $prompt .= "Available variables: {{nom}}, {{prenom}}, {{entreprise}} — use them naturally.\n";

        $response = app(\App\Services\GeminiService::class)->chat(
            auth()->user()->business,
            $prompt,
            'You are a professional copywriter for a SaaS platform.'
        );

        if ($response) {
            $this->data['message'] = $response;
            Notification::make()->title(__('app.client.retention.draft_generated'))->success()->send();
        } else {
            Notification::make()->title(__('app.notifications.error'))->danger()->send();
        }
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $q = Contact::query()->whereNotNull('email');

        match($data['target'] ?? 'all') {
            'clients'   => $q->where('status', 'client'),
            'prospects' => $q->where('status', 'prospect'),
            default     => null,
        };

        $contacts = $q->get();

        if ($contacts->isEmpty()) {
            Notification::make()
                ->title(__('app.notifications.error'))
                ->body(__('app.client.flash.no_contacts_found'))
                ->danger()->send();
            return;
        }

        $raw  = $data['message'] ?? '';
        $sent = 0;

        foreach ($contacts as $contact) {
            $personalized = str_replace(
                ['{{nom}}', '{{prenom}}', '{{entreprise}}'],
                [$contact->name ?? '', explode(' ', $contact->name ?? '')[0], $contact->company ?? ''],
                $raw
            );

            try {
                \Illuminate\Support\Facades\Mail::html(
                    $personalized,
                    function ($mail) use ($contact) {
                        $mail->to($contact->email)
                             ->subject('Message de ' . (auth()->user()->business?->name ?? 'WhatsAppBizAI'));
                    }
                );
                $sent++;
            } catch (\Exception $e) {
                // continue silently
            }
        }

        Notification::make()
            ->title(__('app.admin.broadcast_sent'))
            ->body("{$sent}/{$contacts->count()} " . __('app.admin.retention_emails_sent'))
            ->success()->send();

        $this->data['message'] = null;
    }
}
