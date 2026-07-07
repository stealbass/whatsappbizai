<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\BusinessResource\Pages;
use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Gestion des Entreprises';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Entreprise';
    protected static ?string $modelLabelPlural = 'Entreprises';
    protected static ?string $slug = 'businesses';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations générales')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom de l\'entreprise')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('owner_name')
                        ->label('Nom du propriétaire')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(50),

                    Forms\Components\TextInput::make('address')
                        ->label('Adresse')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('city')
                        ->label('Ville')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('country')
                        ->label('Pays')
                        ->default('CM')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('currency')
                        ->label('Devise')
                        ->default('XAF')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('timezone')
                        ->label('Fuseau horaire')
                        ->default('Africa/Douala')
                        ->maxLength(50),
                ])->columns(3),

            Forms\Components\Section::make('WhatsApp & IA')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    Forms\Components\TextInput::make('whatsapp_phone_number_id')
                        ->label('WhatsApp Phone Number ID')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('whatsapp_business_account_id')
                        ->label('WhatsApp Business Account ID')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('whatsapp_access_token')
                        ->label('WhatsApp Access Token')
                        ->rows(3),

                    Forms\Components\Textarea::make('gemini_system_prompt')
                        ->label('Prompt IA Gemini')
                        ->rows(4),
                ])->columns(2),

            Forms\Components\Section::make('Facturation')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('invoice_prefix')
                        ->label('Préfixe factures')
                        ->default('FAC')
                        ->maxLength(20),

                    Forms\Components\TextInput::make('quote_prefix')
                        ->label('Préfixe devis')
                        ->default('DEV')
                        ->maxLength(20),
                ])->columns(2),

            Forms\Components\Section::make('Statut & Plan')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Entreprise active')
                        ->default(true),

                    Forms\Components\Select::make('plan')
                        ->label('Plan actuel')
                        ->options([
                            'free'     => 'Free',
                            'starter'  => 'Starter',
                            'business' => 'Business',
                            'pro'      => 'Pro',
                        ])
                        ->default('free'),

                    Forms\Components\DateTimePicker::make('plan_expires_at')
                        ->label('Expiration du plan')
                        ->helperText('Laisser vide pour le plan free'),
                ])->columns(3),

            Forms\Components\Section::make('Logo')
                ->icon('heroicon-o-photo')
                ->schema([
                    Forms\Components\FileUpload::make('logo_path')
                        ->label('Logo')
                        ->image()
                        ->directory('logos')
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->owner_name),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'free'     => 'gray',
                        'starter'  => 'info',
                        'business' => 'success',
                        'pro'      => 'warning',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('plan_expires_at')
                    ->label('Expire le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->plan_expires_at && $record->plan_expires_at->isPast() ? 'danger' : null),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('contacts_count')
                    ->label('Contacts')
                    ->counts('contacts')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('invoices_count')
                    ->label('Factures')
                    ->counts('invoices')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->label('Plan')
                    ->options([
                        'free'     => 'Free',
                        'starter'  => 'Starter',
                        'business' => 'Business',
                        'pro'      => 'Pro',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->boolean(label: 'Actif', oppositeLabel: 'Inactif'),

                Tables\Filters\Filter::make('expired')
                    ->label('Plan expiré')
                    ->query(fn ($query) => $query->whereNotNull('plan_expires_at')->where('plan_expires_at', '<', now()))
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Créées cette semaine')
                    ->query(fn ($query) => $query->where('created_at', '>=', now()->subWeek()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Voir en tant que')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Se connecter en tant que cette entreprise ?')
                    ->modalDescription(fn ($record) => "Vous allez accéder au panneau admin de {$record->name}. Votre session super-admin sera conservée.")
                    ->action(function ($record) {
                        $owner = $record->users()->where('role', 'admin')->first();

                        if (!$owner) {
                            Notification::make()
                                ->title('Aucun admin trouvé')
                                ->body('Cette entreprise n\'a pas d\'administrateur.')
                                ->danger()
                                ->send();
                            return;
                        }

                        if (!$owner->is_active) {
                            Notification::make()
                                ->title('Compte désactivé')
                                ->body('L\'administrateur de cette entreprise est désactivé.')
                                ->danger()
                                ->send();
                            return;
                        }

                        auth()->login($owner);

                        return redirect()->route('filament.admin.pages.dashboard');
                    }),

                Tables\Actions\Action::make('toggle_active')
                    ->label(fn ($record) => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_active' => !$record->is_active]);

                        Notification::make()
                            ->title($record->is_active ? 'Entreprise activée' : 'Entreprise désactivée')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBusinesses::route('/'),
            'create' => Pages\CreateBusiness::route('/create'),
            'edit'   => Pages\EditBusiness::route('/{record}/edit'),
        ];
    }
}
