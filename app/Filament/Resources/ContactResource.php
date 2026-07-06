<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.contacts');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.contact');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.contacts');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_messaging');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('app.admin.general_info'))->schema([
                Forms\Components\TextInput::make('name')->label(__('app.admin.name'))->required(),
                Forms\Components\TextInput::make('whatsapp_number')->label(__('app.admin.whatsapp_number'))
                    ->placeholder('+237 6XX XXX XXX')->required(),
                Forms\Components\TextInput::make('email')->email()->label(__('app.admin.email')),
                Forms\Components\TextInput::make('company')->label(__('app.admin.business')),
            ])->columns(2),

            Forms\Components\Section::make(__('app.admin.status_notes'))->schema([
                Forms\Components\Select::make('status')->label(__('app.admin.status'))
                    ->options(['prospect' => __('app.admin.prospect'), 'client' => __('app.admin.client'), 'inactif' => __('app.admin.inactive')])
                    ->default('prospect')->required(),
                Forms\Components\TagsInput::make('tags')->label(__('app.admin.tags')),
                RichEditor::make('notes')->label(__('app.admin.notes'))->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('app.admin.name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('whatsapp_number')->label(__('app.admin.whatsapp_number'))->searchable(),
                Tables\Columns\TextColumn::make('company')->label(__('app.admin.business'))->searchable()->toggleable(),
                Tables\Columns\BadgeColumn::make('status')->label(__('app.admin.status'))
                    ->colors(['warning' => 'prospect', 'success' => 'client', 'danger' => 'inactif']),
                Tables\Columns\TextColumn::make('total_invoiced')->label(__('app.admin.invoiced'))
                    ->money(fn($record) => strtolower($record->business?->currency ?? 'xaf')),
                Tables\Columns\TextColumn::make('last_seen_at')->label(__('app.admin.last_activity'))
                    ->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('app.admin.status'))
                    ->options(['prospect' => __('app.admin.prospect'), 'client' => __('app.admin.client'), 'inactif' => __('app.admin.inactive')]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import_csv')
                    ->label(__('app.admin.import_csv'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->url(route('import.contacts.form')),
                Tables\Actions\Action::make('export_csv')
                    ->label(__('app.admin.export_csv'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(route('export.contacts')),
            ])
            ->actions([
                Tables\Actions\Action::make('send_whatsapp')
                    ->label(__('app.admin.send_message'))
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->form([
                        RichEditor::make('message')
                            ->label(__('app.admin.whatsapp_message'))
                            ->required()
                            
                            ->placeholder('Bonjour {{prenom}}, nous voulions prendre de vos nouvelles...'),
                    ])
                    ->action(function (Contact $record, array $data, WhatsAppService $whatsapp) {
                        $business = auth()->user()->business;

                        if (!$business?->whatsapp_phone_number_id) {
                            Notification::make()
                                ->title(__('app.admin.whatsapp_not_configured'))
                                ->body(__('app.admin.whatsapp_config_desc2'))
                                ->warning()->send();
                            return;
                        }

                        $sent = $whatsapp->sendText(
                            $record->whatsapp_number,
                            $data['message'],
                            $business->whatsapp_phone_number_id,
                            $business->whatsapp_access_token
                        );

                        $sent
                            ? Notification::make()->title(__('app.admin.message_sent'))->success()->send()
                            : Notification::make()->title(__('app.admin.message_send_failed'))->danger()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_seen_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit'   => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
