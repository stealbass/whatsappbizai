<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Contacts';
    protected static ?string $modelLabel = 'Contact';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations')->schema([
                Forms\Components\TextInput::make('name')->label('Nom')->required(),
                Forms\Components\TextInput::make('whatsapp_number')->label('Numéro WhatsApp')
                    ->placeholder('+237 6XX XXX XXX')->required(),
                Forms\Components\TextInput::make('email')->email()->label('Email'),
                Forms\Components\TextInput::make('company')->label('Entreprise'),
            ])->columns(2),

            Forms\Components\Section::make('Statut & Notes')->schema([
                Forms\Components\Select::make('status')->label('Statut')
                    ->options(['prospect' => 'Prospect', 'client' => 'Client', 'inactif' => 'Inactif'])
                    ->default('prospect')->required(),
                Forms\Components\TagsInput::make('tags')->label('Tags'),
                Forms\Components\Textarea::make('notes')->label('Notes')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('whatsapp_number')->label('WhatsApp')->searchable(),
                Tables\Columns\TextColumn::make('company')->label('Entreprise')->searchable()->toggleable(),
                Tables\Columns\BadgeColumn::make('status')->label('Statut')
                    ->colors(['warning' => 'prospect', 'success' => 'client', 'danger' => 'inactif']),
                Tables\Columns\TextColumn::make('total_invoiced')->label('Facturé')
                    ->money(fn($record) => strtolower($record->business?->currency ?? 'xaf')),
                Tables\Columns\TextColumn::make('last_seen_at')->label('Dernière activité')
                    ->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Statut')
                    ->options(['prospect' => 'Prospect', 'client' => 'Client', 'inactif' => 'Inactif']),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import_csv')
                    ->label('📤 Importer CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->url(route('import.contacts.form')),
                Tables\Actions\Action::make('export_csv')
                    ->label('📥 Exporter CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(route('export.contacts')),
            ])
            ->actions([
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('📱 Message')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('message')
                            ->label('Message WhatsApp')
                            ->required()
                            ->rows(3)
                            ->placeholder('Bonjour {{prenom}}, nous voulions prendre de vos nouvelles...'),
                    ])
                    ->action(function (Contact $record, array $data, WhatsAppService $whatsapp) {
                        $business = auth()->user()->business;

                        if (!$business?->whatsapp_phone_number_id) {
                            Notification::make()
                                ->title('WhatsApp non configuré')
                                ->body('Configurez votre compte WhatsApp dans Paramètres → Mon Entreprise.')
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
                            ? Notification::make()->title('Message envoyé')->success()->send()
                            : Notification::make()->title("Échec de l'envoi")->danger()->send();
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
