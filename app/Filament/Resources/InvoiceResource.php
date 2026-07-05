<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Jobs\SendDocumentViaWhatsApp;
use App\Models\Contact;
use App\Models\Invoice;
use App\Services\DocumentService;
use App\Services\ReminderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Factures';
    protected static ?string $modelLabel = 'Facture';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Facture')->schema([
                Forms\Components\Select::make('contact_id')->label('Client')
                    ->relationship('contact', 'name')
                    ->searchable()->preload()->required(),
                Forms\Components\TextInput::make('number')->label('Numéro')
                    ->placeholder('FAC-2026-0001')->required(),
                Forms\Components\Select::make('status')->label('Statut')
                    ->options([
                        'draft'     => 'Brouillon',
                        'sent'      => 'Envoyée',
                        'paid'      => 'Payée',
                        'overdue'   => 'En retard',
                        'cancelled' => 'Annulée',
                    ])->default('draft')->required(),
                Forms\Components\DatePicker::make('issue_date')->label('Date d\'émission')
                    ->default(now())->required(),
                Forms\Components\DatePicker::make('due_date')->label('Date d\'échéance')
                    ->default(now()->addDays(30))->required(),
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
                Forms\Components\TextInput::make('tax_rate')->label('TVA (%)')
                    ->numeric()->default(0),
                Forms\Components\TextInput::make('discount')->label('Remise')
                    ->numeric()->default(0),
                Forms\Components\TextInput::make('notes')->label('Notes / conditions'),
            ])->columns(3),
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
                        'success' => 'paid',
                        'danger'  => 'overdue',
                        'secondary' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('total')->label('Total')
                    ->sortable()->alignRight(),
                Tables\Columns\TextColumn::make('due_date')->label('Échéance')
                    ->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Brouillon', 'sent' => 'Envoyée',
                        'paid' => 'Payée', 'overdue' => 'En retard',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label('✅ Marquer payée')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Invoice $r) => !in_array($r->status, ['paid', 'cancelled']))
                    ->action(function (Invoice $record) {
                        $record->update([
                            'status'      => 'paid',
                            'paid_amount' => $record->total,
                            'paid_at'     => now(),
                        ]);
                        Notification::make()->title('Facture marquée comme payée')->success()->send();
                    }),
                Tables\Actions\Action::make('generate_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Invoice $record, DocumentService $docs) {
                        $path = $docs->generateInvoicePdf($record);
                        Notification::make()->title('PDF généré')->success()->send();
                    }),
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('📲 WhatsApp')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Envoyer la facture par WhatsApp')
                    ->modalDescription(fn(Invoice $r) => "Envoyer la facture {$r->number} à {$r->contact?->name} ({$r->contact?->whatsapp_number}) ?")
                    ->action(function (Invoice $record) {
                        SendDocumentViaWhatsApp::dispatch('invoice', $record->id);
                        Notification::make()->title('Envoi WhatsApp en cours…')->success()->send();
                    }),
                Tables\Actions\Action::make('send_reminder')
                    ->label('Relancer')
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record, ReminderService $reminder) {
                        $sent = $reminder->sendManualReminder($record);
                        $sent
                            ? Notification::make()->title('Relance envoyée')->success()->send()
                            : Notification::make()->title('Échec de l\'envoi')->danger()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_paid_bulk')
                        ->label('Marquer payées')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $records->each(fn($r) => $r->update([
                                'status'      => 'paid',
                                'paid_amount' => $r->total,
                                'paid_at'     => now(),
                            ]));
                            Notification::make()->title($records->count() . ' facture(s) marquées payées')->success()->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
