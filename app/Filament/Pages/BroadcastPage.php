<?php

namespace App\Filament\Pages;

use App\Models\Business;
use App\Models\Contact;
use App\Services\GeminiService;
use App\Services\MarketingService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class BroadcastPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Broadcast WhatsApp';
    protected static ?string $title           = 'Envoyer un message broadcast';
    protected static ?string $navigationGroup = 'Messagerie';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.pages.broadcast';

    public ?string $message    = null;
    public ?string $target     = 'all';
    public ?string $status_filter = null;
    public ?string $aiGoal     = null;

    /**
     * Ask Gemini to draft a broadcast message
     */
    public function draftWithAI(): void
    {
        $business = auth()->user()?->business;
        if (!$business) return;

        $goal     = $this->aiGoal ?: 'Promote our services and invite clients to contact us';
        $audience = match($this->target) {
            'clients'   => 'existing clients',
            'prospects' => 'new prospects',
            default     => 'all contacts (clients and prospects)',
        };

        $gemini = app(GeminiService::class);
        $draft  = $gemini->draftBroadcast($business, $goal, $audience);

        if ($draft) {
            $this->message = $draft;
            Notification::make()->title('✅ Message drafted by AI')->success()->send();
        } else {
            Notification::make()->title('Could not generate draft')->warning()->send();
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Destinataires')->schema([
                Forms\Components\Select::make('target')
                    ->label('Envoyer à')
                    ->options([
                        'all'      => 'Tous les contacts',
                        'clients'  => 'Clients uniquement',
                        'prospects'=> 'Prospects uniquement',
                    ])
                    ->default('all')
                    ->live(),

                Forms\Components\Placeholder::make('count')
                    ->label('Nombre de destinataires')
                    ->content(function () {
                        $business = auth()->user()?->business;
                        if (!$business) return '0';

                        $q = Contact::where('business_id', $business->id)
                                    ->whereNotNull('whatsapp_number');

                        if ($this->target === 'clients')   $q->where('status', 'client');
                        if ($this->target === 'prospects') $q->where('status', 'prospect');

                        return $q->count() . ' contact(s)';
                    }),
            ])->columns(2),

            Forms\Components\Section::make('Message')->schema([
                Forms\Components\TextInput::make('aiGoal')
                    ->label('🤖 AI goal (optional)')
                    ->placeholder('Ex: Announce 20% discount on web design this week')
                    ->helperText('Describe what you want to say — AI will write the message for you')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('message')
                    ->label('Message content')
                    ->required()
                    ->rows(5)
                    ->maxLength(1024)
                    ->placeholder("Hello {{nom}},\n\nWe are pleased to announce...\n\nBest regards,\nYour team")
                    ->helperText('Available variables: {{nom}}, {{prenom}}, {{entreprise}}')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public function send(MarketingService $marketing): void
    {
        $this->validate([
            'message' => 'required|string|max:1024',
            'target'  => 'required|in:all,clients,prospects',
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
        if ($this->target === 'clients')   $q->where('status', 'client');
        if ($this->target === 'prospects') $q->where('status', 'prospect');

        $contacts = $q->get();

        if ($contacts->isEmpty()) {
            Notification::make()->title('Aucun contact trouvé')->warning()->send();
            return;
        }

        $result = $marketing->sendBroadcast($business, $contacts, $this->message);

        Notification::make()
            ->title("Broadcast terminé — {$result['sent']} envoi(s) réussi(s)")
            ->body($result['failed'] > 0 ? "{$result['failed']} échec(s)" : null)
            ->success()
            ->send();

        $this->message = null;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('draft_ai')
                ->label('🤖 Draft with AI')
                ->color('info')
                ->action('draftWithAI'),
            Action::make('send')
                ->label('📤 Send broadcast')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirm broadcast')
                ->modalDescription('This message will be sent to all selected recipients. This action cannot be undone.')
                ->action('send'),
        ];
    }
}
