<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * BusinessResource — Visible uniquement par les super-admins.
 * Les utilisateurs normaux gèrent leur profil via Settings.
 */
class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;
    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Mon Entreprise';
    protected static ?string $modelLabel      = 'Entreprise';
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?int    $navigationSort  = 10;

    /**
     * Filtre automatiquement pour ne montrer que le business de l'utilisateur connecté
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->business_id) {
            $query->where('id', auth()->user()->business_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Informations générales')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom de l\'entreprise')->required(),
                Forms\Components\TextInput::make('owner_name')
                    ->label('Nom du responsable')->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')->email()->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Téléphone')->placeholder('+237 6XX XXX XXX'),
                Forms\Components\TextInput::make('address')->label('Adresse'),
                Forms\Components\TextInput::make('city')->label('Ville')->default('Douala'),
                Forms\Components\Select::make('country')->label('Pays')
                    ->options([
                        'CM' => '🇨🇲 Cameroun',
                        'SN' => '🇸🇳 Sénégal',
                        'CI' => '🇨🇮 Côte d\'Ivoire',
                        'MA' => '🇲🇦 Maroc',
                        'FR' => '🇫🇷 France',
                        'BE' => '🇧🇪 Belgique',
                    ])->default('CM'),
                Forms\Components\Select::make('currency')->label('Devise')
                    ->options([
                        'XAF' => 'XAF (FCFA BEAC)',
                        'XOF' => 'XOF (FCFA BCEAO)',
                        'EUR' => 'EUR (Euro)',
                        'USD' => 'USD (Dollar)',
                        'GBP' => 'GBP (Livre Sterling)',
                        'ZAR' => 'ZAR (Rand Sud-Africain)',
                        'MAD' => 'MAD (Dirham)',
                        'NGN' => 'NGN (Naira)',
                        'GHS' => 'GHS (Cedi)',
                        'KES' => 'KES (Shilling)',
                    ])->default('XAF'),
                Forms\Components\Select::make('timezone')->label('Fuseau horaire')
                    ->options([
                        'Africa/Douala'     => 'Afrique/Douala (WAT)',
                        'Africa/Dakar'      => 'Afrique/Dakar (GMT)',
                        'Africa/Abidjan'    => 'Afrique/Abidjan (GMT)',
                        'Africa/Casablanca' => 'Afrique/Casablanca (WET)',
                        'Europe/Paris'      => 'Europe/Paris (CET)',
                    ])->default('Africa/Douala'),
            ])->columns(2),

            Forms\Components\Section::make('Facturation')->schema([
                Forms\Components\TextInput::make('invoice_prefix')
                    ->label('Préfixe factures')->default('FAC')->maxLength(10),
                Forms\Components\TextInput::make('quote_prefix')
                    ->label('Préfixe devis')->default('DEV')->maxLength(10),
            ])->columns(2),

            Forms\Components\Section::make('🟢 WhatsApp Business')
                ->description('Identifiants depuis Meta for Developers → WhatsApp → Configuration')
                ->schema([
                    Forms\Components\TextInput::make('whatsapp_phone_number_id')
                        ->label('Phone Number ID')->placeholder('123456789012345'),
                    Forms\Components\TextInput::make('whatsapp_business_account_id')
                        ->label('Business Account ID')->placeholder('987654321098765'),
                    Forms\Components\TextInput::make('whatsapp_access_token')
                        ->label('Access Token permanent')
                        ->password()->revealable()
                        ->placeholder('EAAxxxxxxxx...'),
                ])->columns(1),

            Forms\Components\Section::make('🤖 Instructions personnalisées pour l\'IA')
                ->schema([
                    Forms\Components\Textarea::make('gemini_system_prompt')
                        ->label('Instructions pour l\'agent IA')
                        ->rows(5)
                        ->placeholder("Ex: Ne jamais donner de prix sans confirmation du responsable...\nToujours proposer un devis pour les projets > 100 000 XAF.")
                        ->columnSpanFull(),
                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Entreprise')->sortable(),
                Tables\Columns\TextColumn::make('owner_name')->label('Responsable'),
                Tables\Columns\TextColumn::make('city')->label('Ville'),
                Tables\Columns\BadgeColumn::make('plan')->label('Plan')
                    ->colors(['gray' => 'free', 'warning' => 'starter', 'primary' => 'business', 'success' => 'pro']),
                Tables\Columns\IconColumn::make('whatsapp_phone_number_id')
                    ->label('WhatsApp ✓')->boolean()
                    ->getStateUsing(fn($record) => !empty($record->whatsapp_phone_number_id)),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
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
