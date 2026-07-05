<?php

namespace App\Filament\Resources;

use App\Actions\ConvertQuoteToInvoice;
use App\Filament\Resources\QuoteResource\Pages;
use App\Jobs\SendDocumentViaWhatsApp;
use App\Models\Quote;
use App\Services\DocumentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Devis';
    protected static ?string $modelLabel = 'Devis';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Devis')->schema([
                Forms\Components\Select::make('contact_id')->label('Client')
                    ->relationship('contact', 'name')
                    ->searchable()->preload()->required(),
                Forms\Components\TextInput::make('number')->label('Numéro')
                    ->placeholder('DEV-2026-0001')->required(),
                Forms\Components\Select::make('status')->label('Statut')
                    ->options([
                        'draft'    => 'Brouillon',
                        'sent'     => 'Envoyé',
                        'accepted' => 'Accepté',
                        'declined' => 'Refusé',
                        'expired'  => 'Expiré',
                    ])->default('draft')->required(),
                Forms\Components\DatePicker::make('issue_date')->label('Date d\'émission')
                    ->default(now())->required(),
                Forms\Components\DatePicker::make('valid_until')->label('Valide jusqu\'au')
                    ->default(now()->addDays(15))->required(),
                Forms\Components\Select::make('currency')->label('Devise')
                    ->options(['XAF' => 'XAF (FCFA)', 'EUR' => 'EUR (€)', 'USD' => 'USD ($)'])
                    ->default('XAF')->required(),
            ])->columns(2),

            Forms\Components\Section::make('Prestations')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('description')->label('Description')
                            ->required()->columnSpan(2),
                        Forms\Components\TextInput::make('quantity')->label('Qté')
                            ->numeric()->default(1)->required(),
                        Forms\Components\TextInput::make('unit_price')->label('Prix unitaire')
                            ->numeric()->required(),
                        Forms\Components\TextInput::make('total')->label('Total')
                            ->numeric()->disabled()->dehydrated(),
                    ])->columns(4)
                    ->addActionLabel('Ajouter une ligne')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Totaux')->schema([
                Forms\Components\TextInput::make('tax_rate')->label('TVA (%)')->numeric()->default(0),
                Forms\Components\TextInput::make('discount')->label('Remise')->numeric()->default(0),
                Forms\Components\Textarea::make('notes')->label('Notes / conditions de paiement'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('N°')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact.name')->label('Client')->searchable(),
                Tables\Columns\BadgeColumn::make('status')->label('Statut')
                    ->colors([
                        'gray'    => 'draft',
                        'warning' => 'sent',
                        'success' => 'accepted',
                        'danger'  => fn($state) => in_array($state, ['declined', 'expired']),
                    ]),
                Tables\Columns\TextColumn::make('total')->label('Total')->sortable()->alignRight(),
                Tables\Columns\TextColumn::make('valid_until')->label('Validité')
                    ->date('d/m/Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('generate_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Quote $record, DocumentService $docs) {
                        $docs->generateQuotePdf($record);
                        Notification::make()->title('PDF généré')->success()->send();
                    }),
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('📲 WhatsApp')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Envoyer le devis par WhatsApp')
                    ->modalDescription(fn(Quote $r) => "Envoyer le devis {$r->number} à {$r->contact?->name} ({$r->contact?->whatsapp_number}) ?")
                    ->action(function (Quote $record) {
                        SendDocumentViaWhatsApp::dispatch('quote', $record->id);
                        Notification::make()->title('Envoi WhatsApp en cours…')->success()->send();
                    }),
                Tables\Actions\Action::make('convert')
                    ->label('→ Facture')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Convertir en facture')
                    ->modalDescription(fn(Quote $r) => "Convertir le devis {$r->number} en facture ? Le devis sera marqué comme accepté.")
                    ->visible(fn(Quote $r) => !in_array($r->status, ['accepted', 'declined']))
                    ->action(function (Quote $record, ConvertQuoteToInvoice $converter) {
                        $invoice = $converter->execute($record);
                        Notification::make()->title("Facture {$invoice->number} créée")->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit'   => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
