<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BusinessResource extends Resource
{
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $model = Business::class;
    protected static ?string $slug = 'businesses';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->is_super_admin ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('app.admin.businesses');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.business');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.businesses');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_administration');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user() && !auth()->user()->is_super_admin && auth()->user()->business_id) {
            $query->where('id', auth()->user()->business_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations générales')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required(),
                    Forms\Components\TextInput::make('owner_name')
                        ->label('Propriétaire')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),
                    Forms\Components\TextInput::make('phone')
                        ->label('Téléphone'),
                    Forms\Components\TextInput::make('address')
                        ->label('Adresse'),
                    Forms\Components\TextInput::make('city')
                        ->label('Ville')
                        ->default('Douala'),
                    Forms\Components\Select::make('country')
                        ->label('Pays')
                        ->options([
                            'CM' => 'Cameroun',
                            'SN' => 'Sénégal',
                            'CI' => 'Côte d\'Ivoire',
                            'MA' => 'Maroc',
                            'FR' => 'France',
                        ])
                        ->default('CM'),
                    Forms\Components\Select::make('currency')
                        ->label('Devise')
                        ->options([
                            'XAF' => 'XAF',
                            'XOF' => 'XOF',
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                        ])
                        ->default('XAF'),
                    Forms\Components\Select::make('timezone')
                        ->label('Fuseau horaire')
                        ->options([
                            'Africa/Douala'  => 'Douala (WAT)',
                            'Africa/Dakar'   => 'Dakar (GMT)',
                            'Europe/Paris'   => 'Paris (CET)',
                        ])
                        ->default('Africa/Douala'),
                ])->columns(2),

            Forms\Components\Section::make('Facturation')
                ->schema([
                    Forms\Components\TextInput::make('invoice_prefix')
                        ->label('Préfixe factures')
                        ->default('FAC')
                        ->maxLength(10),
                    Forms\Components\TextInput::make('quote_prefix')
                        ->label('Préfixe devis')
                        ->default('DEV')
                        ->maxLength(10),
                ])->columns(2),

            Forms\Components\Section::make('Intelligence Artificielle')
                ->schema([
                    RichEditor::make('gemini_system_prompt')
                        ->label('Prompt IA Gemini')
                        ->columnSpanFull(),
                ])->columns(1),

            Forms\Components\Section::make('Statut & Plan')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    Forms\Components\Select::make('plan')
                        ->label('Plan')
                        ->options([
                            'free'     => 'Free',
                            'starter'  => 'Starter',
                            'business' => 'Business',
                            'pro'      => 'Pro',
                        ])
                        ->default('free'),
                    Forms\Components\DateTimePicker::make('plan_expires_at')
                        ->label('Expiration du plan'),
                ])->columns(3),

            Forms\Components\Section::make('Logo')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->owner_name),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

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

                Tables\Columns\TextColumn::make('whatsapp_phone_number_id')
                    ->label('WhatsApp Phone ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Voir en tant que')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $owner = $record->users()->first();
                        if (!$owner) {
                            Notification::make()->title('Aucun utilisateur')->danger()->send();
                            return;
                        }
                        return redirect(url("impersonate/{$owner->id}?save_current=true"));
                    })
                    ->visible(fn () => auth()->user()?->is_super_admin ?? false),

                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
