<?php

namespace App\Filament\Resources;

use App\Actions\ConvertQuoteToInvoice;
use App\Filament\Resources\QuoteResource\Pages;
use App\Jobs\SendDocumentViaWhatsApp;
use App\Models\Quote;
use App\Services\DocumentService;
use Filament\Forms;
use App\Filament\Forms\Components\TinyMce;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.quotes');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.quote');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.quotes');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_documents');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('app.admin.quote'))->schema([
                Forms\Components\Select::make('contact_id')->label(__('app.admin.client'))
                    ->relationship('contact', 'name')
                    ->searchable()->preload()->required(),
                Forms\Components\TextInput::make('number')->label(__('app.admin.number'))
                    ->placeholder('DEV-2026-0001')->required(),
                Forms\Components\Select::make('status')->label(__('app.admin.status'))
                    ->options([
                        'draft'    => __('app.admin.draft'),
                        'sent'     => __('app.admin.sent'),
                        'accepted' => __('app.admin.accepted'),
                        'declined' => __('app.admin.declined'),
                        'expired'  => __('app.admin.expired'),
                    ])->default('draft')->required(),
                Forms\Components\DatePicker::make('issue_date')->label(__('app.admin.issue_date'))
                    ->default(now())->required(),
                Forms\Components\DatePicker::make('valid_until')->label(__('app.admin.valid_until'))
                    ->default(now()->addDays(15))->required(),
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
                Forms\Components\TextInput::make('tax_rate')->label(__('app.admin.tax_rate'))->numeric()->default(0),
                Forms\Components\TextInput::make('discount')->label(__('app.admin.discount'))->numeric()->default(0),
                TinyMce::make('notes')->label(__('app.admin.notes_conditions')),
            ])->columns(2),
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
                        'success' => 'accepted',
                        'danger'  => fn($state) => in_array($state, ['declined', 'expired']),
                    ]),
                Tables\Columns\TextColumn::make('total')->label(__('app.admin.total'))->sortable()->alignRight(),
                Tables\Columns\TextColumn::make('valid_until')->label(__('app.admin.validity'))
                    ->date('d/m/Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('generate_pdf')
                    ->label(__('app.admin.generate_pdf'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Quote $record, DocumentService $docs) {
                        $docs->generateQuotePdf($record);
                        Notification::make()->title(__('app.admin.pdf_generated'))->success()->send();
                    }),
                Tables\Actions\Action::make('send_whatsapp')
                    ->label(__('app.admin.send_whatsapp'))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('app.admin.send_quote_whatsapp'))
                    ->modalDescription(fn(Quote $r) => __('app.admin.send_quote_whatsapp') . " : {$r->number} → {$r->contact?->name} ({$r->contact?->whatsapp_number}) ?")
                    ->action(function (Quote $record) {
                        SendDocumentViaWhatsApp::dispatch('quote', $record->id);
                        Notification::make()->title(__('app.admin.sending_whatsapp'))->success()->send();
                    }),
                Tables\Actions\Action::make('convert')
                    ->label(__('app.admin.convert_to_invoice'))
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading(__('app.admin.convert_invoice_heading'))
                    ->modalDescription(fn(Quote $r) => __('app.admin.convert_invoice_heading') . " : {$r->number} ?")
                    ->visible(fn(Quote $r) => !in_array($r->status, ['accepted', 'declined']))
                    ->action(function (Quote $record, ConvertQuoteToInvoice $converter) {
                        $invoice = $converter->execute($record);
                        Notification::make()->title(__('app.admin.quote_created') . " {$invoice->number}")->success()->send();
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
