<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Jobs\SendDocumentViaWhatsApp;
use App\Models\Contact;
use App\Models\Invoice;
use App\Services\DocumentService;
use App\Services\ReminderService;
use Filament\Forms;
use App\Filament\Forms\Components\TinyMce;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.invoices');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.invoices');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_documents');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('app.admin.invoice'))->schema([
                Forms\Components\Select::make('contact_id')->label(__('app.admin.client'))
                    ->relationship('contact', 'name')
                    ->searchable()->preload()->required(),
                Forms\Components\TextInput::make('number')->label(__('app.admin.number'))
                    ->placeholder('FAC-2026-0001')->required(),
                Forms\Components\Select::make('status')->label(__('app.admin.status'))
                    ->options([
                        'draft'     => __('app.admin.draft'),
                        'sent'      => __('app.admin.sent'),
                        'paid'      => __('app.admin.paid'),
                        'overdue'   => __('app.admin.overdue'),
                        'cancelled' => __('app.admin.cancelled'),
                    ])->default('draft')->required(),
                Forms\Components\DatePicker::make('issue_date')->label(__('app.admin.issue_date'))
                    ->default(now())->required(),
                Forms\Components\DatePicker::make('due_date')->label(__('app.admin.due_date'))
                    ->default(now()->addDays(30))->required(),
                Forms\Components\Select::make('currency')->label(__('app.admin.currency'))
                    ->options(['XAF' => 'XAF (FCFA)', 'EUR' => 'EUR (€)', 'USD' => 'USD ($)'])
                    ->default('XAF')->required(),
            ])->columns(2),

            Forms\Components\Section::make(__('app.admin.services_section'))->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('description')->label(__('app.admin.description'))
                            ->required()->columnSpan(2),
                        Forms\Components\TextInput::make('quantity')->label(__('app.admin.quantity'))
                            ->numeric()->default(1)->required(),
                        Forms\Components\TextInput::make('unit_price')->label(__('app.admin.unit_price'))
                            ->numeric()->required(),
                        Forms\Components\TextInput::make('total')->label(__('app.admin.total'))
                            ->numeric()->disabled()->dehydrated(),
                    ])->columns(4)
                    ->addActionLabel(__('app.admin.add_line'))
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make(__('app.admin.totals'))->schema([
                Forms\Components\TextInput::make('tax_rate')->label(__('app.admin.tax_rate'))
                    ->numeric()->default(0),
                Forms\Components\TextInput::make('discount')->label(__('app.admin.discount'))
                    ->numeric()->default(0),
                TinyMce::make('notes')->height(200)->label(__('app.admin.notes_conditions')),
                Forms\Components\TextInput::make('payment_method')->label(__('app.admin.payment_method'))->nullable(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label(__('app.admin.number'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact.name')->label(__('app.admin.client'))->searchable(),
                Tables\Columns\BadgeColumn::make('status')->label(__('app.admin.status'))
                    ->colors([
                        'gray'    => 'draft',
                        'warning' => 'sent',
                        'success' => 'paid',
                        'danger'  => 'overdue',
                        'secondary' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('total')->label(__('app.admin.total'))
                    ->sortable()->alignRight(),
                Tables\Columns\TextColumn::make('due_date')->label(__('app.admin.expiry'))
                    ->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => __('app.admin.draft'), 'sent' => __('app.admin.sent'),
                        'paid' => __('app.admin.paid'), 'overdue' => __('app.admin.overdue'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label(__('app.admin.mark_paid'))
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
                        Notification::make()->title(__('app.admin.invoice_marked_paid'))->success()->send();
                    }),
                Tables\Actions\Action::make('generate_pdf')
                    ->label(__('app.admin.generate_pdf'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Invoice $record, DocumentService $docs) {
                        $path = $docs->generateInvoicePdf($record);
                        Notification::make()->title(__('app.admin.pdf_generated'))->success()->send();
                    }),
                Tables\Actions\Action::make('send_whatsapp')
                    ->label(__('app.admin.send_whatsapp'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('app.admin.send_invoice_whatsapp'))
                    ->modalDescription(fn(Invoice $r) => __('app.admin.send_invoice_whatsapp') . " : {$r->number} → {$r->contact?->name} ({$r->contact?->whatsapp_number}) ?")
                    ->action(function (Invoice $record) {
                        SendDocumentViaWhatsApp::dispatch('invoice', $record->id);
                        Notification::make()->title(__('app.admin.sending_whatsapp'))->success()->send();
                    }),
                Tables\Actions\Action::make('send_reminder')
                    ->label(__('app.admin.send_reminder'))
                    ->icon('heroicon-o-bell')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record, ReminderService $reminder) {
                        $sent = $reminder->sendManualReminder($record);
                        $sent
                            ? Notification::make()->title(__('app.admin.reminder_sent'))->success()->send()
                            : Notification::make()->title(__('app.admin.send_failed'))->danger()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_paid_bulk')
                        ->label(__('app.admin.mark_paid_bulk'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $records->each(fn($r) => $r->update([
                                'status'      => 'paid',
                                'paid_amount' => $r->total,
                                'paid_at'     => now(),
                            ]));
                            Notification::make()->title($records->count() . ' ' . __('app.admin.marked_paid_count'))->success()->send();
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
