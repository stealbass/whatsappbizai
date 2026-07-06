<?php

namespace App\Filament\Pages;

use App\Models\Contact;
use App\Services\GeminiService;
use App\Services\MarketingService;
use Filament\Forms;
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
    protected static ?string $title = 'Campagnes de Rétention';

    public ?string $message = null;
    public ?string $target = 'inactive_clients';
    public ?string $objective = 'retention';

    public function mount(): void
    {
        $this->form->fill([
            'target' => 'inactive_clients',
            'objective' => 'retention',
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('target')
                ->label('Cible')
                ->options([
                    'inactive_clients' => 'Clients inactifs (30+ jours)',
                    'all_clients' => 'Tous les clients',
                    'prospects' => 'Prospects',
                    'high_value' => 'Clients à forte valeur (> 100 000 XAF)',
                ])
                ->required(),

            Forms\Components\Select::make('objective')
                ->label('Objectif')
                ->options([
                    'retention' => 'Rétention',
                    'upsell' => 'Vente additionnelle',
                    'winback' => 'Réactivation',
                    'referral' => 'Parrainage',
                ])
                ->required(),

            Forms\Components\Textarea::make('message')
                ->label('Message')
                ->rows(5)
                ->required()
                ->maxLength(1024),

            Forms\Components\Button::make('draft_ai')
                ->label('Rédiger avec l\'IA')
                ->icon('heroicon-m-sparkles')
                ->color('info')
                ->action('draftWithAI'),

            Forms\Components\Button::make('send')
                ->label('Envoyer la campagne')
                ->icon('heroicon-m-paper-airplane')
                ->color('primary')
                ->action('sendCampaign'),
        ];
    }

    public function draftWithAI(): void
    {
        $data = $this->form->getState();

        $business = auth()->user()->business ?? null;
        if (!$business) {
            Notification::make()->title(__('app.notifications.error'))->body(__('app.notifications.no_business'))->danger()->send();
            return;
        }

        $goal = match($data['objective']) {
            'retention' => 'Write a retention message to re-engage inactive clients with a special offer',
            'upsell' => 'Write an upsell message to offer premium services to existing clients',
            'winback' => 'Write a win-back message for clients who haven\'t purchased in 30+ days',
            'referral' => 'Write a referral invitation message offering rewards for bringing new clients',
            default => 'Write a professional marketing message',
        };

        $audience = match($data['target']) {
            'inactive_clients' => 'inactive clients for 30+ days',
            'all_clients' => 'all active clients',
            'prospects' => 'prospects who haven\'t purchased yet',
            'high_value' => 'high-value clients (> 100,000 XAF)',
            default => 'all contacts',
        };

        $gemini = app(GeminiService::class);
        $draft = $gemini->draftBroadcast($business, $goal, $audience);

        if ($draft) {
            $this->form->fill(['message' => $draft]);
            Notification::make()->title(__('app.client.retention.draft_generated'))->body(__('app.client.retention.draft_generated_body'))->success()->send();
        } else {
            Notification::make()->title(__('app.notifications.error'))->body(__('app.client.retention.draft_error'))->danger()->send();
        }
    }

    public function sendCampaign(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();
        $business = $user->business ?? null;

        if (!$business) {
            Notification::make()->title(__('app.notifications.error'))->body(__('app.notifications.no_business'))->danger()->send();
            return;
        }

        if (!$business->whatsapp_phone_number_id || !$business->whatsapp_access_token) {
            Notification::make()->title(__('app.notifications.error'))->body(__('app.notifications.whatsapp_not_configured'))->danger()->send();
            return;
        }

        $q = Contact::where('business_id', $business->id)->whereNotNull('whatsapp_number');

        match($data['target']) {
            'inactive_clients' => $q->where('status', 'client')->where('last_seen_at', '<', now()->subDays(30)),
            'prospects' => $q->where('status', 'prospect'),
            'high_value' => $q->where('status', 'client')->where('total_invoiced', '>', 100000),
            default => $q->where('status', 'client'),
        };

        $contacts = $q->get();

        if ($contacts->isEmpty()) {
            Notification::make()->title(__('app.notifications.error'))->body(__('app.notifications.no_contacts'))->danger()->send();
            return;
        }

        $marketing = app(MarketingService::class);
        $result = $marketing->sendBroadcast($business, $contacts, $data['message']);

        Notification::make()
            ->title(__('app.client.retention.campaign_completed'))
            ->body("{$result['sent']} " . __('app.client.retention.campaign_sent') . ($result['failed'] > 0 ? ", {$result['failed']} " . __('app.client.retention.campaign_failed') : ''))
            ->success()
            ->send();

        $this->form->fill(['message' => null]);
    }
}
