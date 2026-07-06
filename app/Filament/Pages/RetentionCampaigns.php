<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class RetentionCampaigns extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.retention';
    protected static ?string $navigationIcon = 'heroicon-m-user-group';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function getHeading(): string
    {
        return __('app.admin.retention_title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'target'    => 'expired_subscribers',
            'objective' => 'renewal',
            'message'   => null,
        ]);
    }

    /**
     * Filament 3 form API — replaces getFormSchema() from Filament 2.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('target')
                    ->label(__('app.admin.retention_target'))
                    ->options([
                        'expired_subscribers' => __('app.admin.expired_subscribers'),
                        'trial_users'         => __('app.admin.trial_users'),
                        'inactive_users'      => __('app.admin.inactive_users_30d'),
                        'all_users'           => __('app.admin.all_users'),
                    ])
                    ->required(),

                Forms\Components\Select::make('objective')
                    ->label(__('app.admin.retention_objective'))
                    ->options([
                        'renewal'  => __('app.admin.obj_renewal'),
                        'upgrade'  => __('app.admin.obj_upgrade'),
                        'winback'  => __('app.admin.obj_winback'),
                        'feedback' => __('app.admin.obj_feedback'),
                    ])
                    ->required(),

                Forms\Components\Textarea::make('message')
                    ->label(__('app.admin.retention_message'))
                    ->rows(15)
                    ->extraFieldWrapperAttributes(['wire:ignore' => ''])
                    ->required(),
            ])
            ->statePath('data');
    }

    public function draftWithAI(): void
    {
        $data = $this->form->getState();

        $goal = match($data['objective']) {
            'renewal'  => 'Write a renewal reminder for expired subscriptions with a special offer',
            'upgrade'  => 'Write an upgrade offer message for free/trial users',
            'winback'  => 'Write a win-back message for users inactive for 30+ days',
            'feedback' => 'Write a feedback request message for churned users',
            default    => 'Write a professional retention message',
        };

        $audience = match($data['target']) {
            'expired_subscribers' => 'users with expired subscriptions',
            'trial_users'         => "trial users who haven't subscribed yet",
            'inactive_users'      => 'users inactive for 30+ days',
            'all_users'           => 'all platform users',
            default               => 'all users',
        };

        $prompt  = "Write a professional retention email/message for a SaaS platform called WhatsAppBizAI.\n";
        $prompt .= "Goal: {$goal}\n";
        $prompt .= "Audience: {$audience}\n";
        $prompt .= "Language: French\n";
        $prompt .= "Tone: Friendly, professional, not pushy.\n";
        $prompt .= "Include a clear call-to-action.\n";
        $prompt .= "Keep it under 200 words.\n";

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

    public function sendCampaign(): void
    {
        $data = $this->form->getState();

        $q = User::query();

        match($data['target']) {
            'expired_subscribers' => $q->whereHas('business',
                fn($b) => $b->where('plan', '!=', 'free')->where('plan_expires_at', '<', now())),
            'trial_users'   => $q->whereDoesntHave('business',
                fn($b) => $b->where('plan', '!=', 'free')),
            'inactive_users' => $q->where('last_login_at', '<', now()->subDays(30)),
            default => null,
        };

        $users = $q->whereNotNull('email')->get();

        if ($users->isEmpty()) {
            Notification::make()
                ->title(__('app.notifications.error'))
                ->body(__('app.admin.retention_no_users'))
                ->danger()->send();
            return;
        }

        $sent = 0;
        foreach ($users as $user) {
            try {
                \Illuminate\Support\Facades\Mail::html(
                    $data['message'],
                    function ($mail) use ($user) {
                        $mail->to($user->email)->subject(__('app.admin.retention_subject'));
                    }
                );
                $sent++;
            } catch (\Exception $e) {
                // continue silently
            }
        }

        Notification::make()
            ->title(__('app.client.retention.campaign_completed'))
            ->body("{$sent}/{$users->count()} " . __('app.admin.retention_emails_sent'))
            ->success()->send();

        $this->data['message'] = null;
    }
}
