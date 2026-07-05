<?php

namespace App\Filament\Pages;

use App\Models\Business;
use App\Models\Contact;
use App\Services\GeminiService;
use App\Services\MarketingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class RetentionPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Rétention & Acquisition';
    protected static ?string $title = 'Rétention & Acquisition clients';
    protected static ?string $navigationGroup = 'Messagerie';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.retention';

    public ?string $message = null;
    public ?string $objective = 'retention';
    public ?string $target = 'inactive_clients';
    public ?string $aiGoal = null;

    public function draftWithAI(): void
    {
        $business = auth()->user()?->business;
        if (!$business) return;

        $goal = $this->aiGoal ?: match($this->objective) {
            'retention' => 'Write a retention message to re-engage inactive clients with a special offer',
            'upsell'    => 'Write an upsell message to offer premium services to existing clients',
            'winback'   => 'Write a win-back message for clients who haven\'t purchased in 30+ days',
            'referral'  => 'Write a referral invitation message offering rewards for bringing new clients',
            default     => 'Write a professional marketing message',
        };

        $audience = match($this->target) {
            'inactive_clients' => 'clients inactive depuis plus de 30 jours',
            'all_clients'      => 'tous les clients actifs',
            'prospects'        => 'prospects qui n\'ont pas encore acheté',
            'high_value'       => 'clients à forte valeur (> 100 000 XAF)',
            default            => 'tous les contacts',
        };

        $gemini = app(GeminiService::class);
        $draft = $gemini->draftBroadcast($business, $goal, $audience);

        if ($draft) {
            $this->message = $draft;
            Notification::make()->title('✅ Message généré par l\'IA')->success()->send();
        } else {
            Notification::make()->title('Impossible de générer le message')->warning()->send();
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Objectif')->schema([
                Forms\Components\Select::make('objective')
                    ->label('Type de campagne')
                    ->options([
                        'retention' => '🔒 Rétention — Fidéliser les clients existants',
                        'upsell'    => '📈 Upsell — Proposer des services premium',
                        'winback'   => '🔄 Win-back — Réactiver les clients inactifs',
                        'referral'  => '👥 Parrainage — Obtenir de nouveaux clients',
                    ])
                    ->default('retention')
                    ->live(),

                Forms\Components\Select::make('target')
                    ->label('Destinataires')
                    ->options([
                        'inactive_clients' => 'Clients inactifs (30+ jours)',
                        'all_clients'      => 'Tous les clients actifs',
                        'prospects'        => 'Prospects (pas encore d\'achat)',
                        'high_value'       => 'Clients à forte valeur',
                    ])
                    ->default('inactive_clients')
                    ->live(),

                Forms\Components\Placeholder::make('count')
                    ->label('Nombre de destinataires')
                    ->content(function () {
                        $business = auth()->user()?->business;
                        if (!$business) return '0';

                        $q = Contact::where('business_id', $business->id)
                                    ->whereNotNull('whatsapp_number');

                        return match($this->target) {
                            'inactive_clients' => $q->where('status', 'client')
                                ->where('last_seen_at', '<', now()->subDays(30))->count(),
                            'prospects'        => $q->where('status', 'prospect')->count(),
                            'high_value'       => $q->where('status', 'client')
                                ->where('total_invoiced', '>', 100000)->count(),
                            default            => $q->where('status', 'client')->count(),
                        } . ' contact(s)';
                    }),
            ])->columns(2),

            Forms\Components\Section::make('Message')->schema([
                Forms\Components\TextInput::make('aiGoal')
                    ->label('🎯 Objectif personnalisé (optionnel)')
                    ->placeholder('Ex: -20% sur tous les services premium cette semaine')
                    ->helperText('Décrivez votre objectif — l\'IA rédigera le message pour vous')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('message')
                    ->label('Contenu du message')
                    ->required()
                    ->rows(5)
                    ->maxLength(1024)
                    ->placeholder("Bonjour {{prenom}},\n\nNous avons une offre spéciale pour vous...")
                    ->helperText('Variables disponibles : {{nom}}, {{prenom}}, {{entreprise}}')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public function send(MarketingService $marketing): void
    {
        $this->validate([
            'message'  => 'required|string|max:1024',
            'target'   => 'required|in:inactive_clients,all_clients,prospects,high_value',
            'objective'=> 'required|in:retention,upsell,winback,referral',
        ]);

        $business = auth()->user()?->business;

        if (!$business?->whatsapp_phone_number_id) {
            Notification::make()
                ->title('Configuration WhatsApp manquante')
                ->body('Configurez votre compte WhatsApp dans Paramètres → Mon Entreprise.')
                ->danger()->send();
            return;
        }

        $q = Contact::where('business_id', $business->id)->whereNotNull('whatsapp_number');

        match($this->target) {
            'inactive_clients' => $q->where('status', 'client')
                ->where('last_seen_at', '<', now()->subDays(30)),
            'prospects'        => $q->where('status', 'prospect'),
            'high_value'       => $q->where('status', 'client')
                ->where('total_invoiced', '>', 100000),
            default            => $q->where('status', 'client'),
        };

        $contacts = $q->get();

        if ($contacts->isEmpty()) {
            Notification::make()->title('Aucun contact trouvé')->warning()->send();
            return;
        }

        $result = $marketing->sendBroadcast($business, $contacts, $this->message);

        Notification::make()
            ->title("Campagne terminée — {$result['sent']} envoi(s) réussi(s)")
            ->body($result['failed'] > 0 ? "{$result['failed']} échec(s)" : null)
            ->success()
            ->send();

        $this->message = null;
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
