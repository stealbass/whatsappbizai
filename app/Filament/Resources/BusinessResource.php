<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;

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
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $model = Business::class;

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

            Forms\Components\Section::make(__('app.admin.general_info'))->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('app.admin.company_name'))->required(),
                Forms\Components\TextInput::make('owner_name')
                    ->label(__('app.admin.owner_name'))->required(),
                Forms\Components\TextInput::make('email')
                    ->label(__('app.admin.email'))->email()->required(),
                Forms\Components\TextInput::make('phone')
                    ->label(__('app.admin.phone'))->placeholder('+237 6XX XXX XXX'),
                Forms\Components\TextInput::make('address')->label(__('app.admin.address')),
                Forms\Components\TextInput::make('city')->label(__('app.admin.city'))->default('Douala'),
                Forms\Components\Select::make('country')->label(__('app.admin.country'))
                    ->options([
                        'CM' => '🇨🇲 Cameroun',
                        'SN' => '🇸🇳 Sénégal',
                        'CI' => '🇨🇮 Côte d\'Ivoire',
                        'MA' => '🇲🇦 Maroc',
                        'FR' => '🇫🇷 France',
                        'BE' => '🇧🇪 Belgique',
                    ])->default('CM'),
                Forms\Components\Select::make('currency')->label(__('app.admin.currency'))
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
                Forms\Components\Select::make('timezone')->label(__('app.admin.timezone'))
                    ->options([
                        'Africa/Douala'     => 'Afrique/Douala (WAT)',
                        'Africa/Dakar'      => 'Afrique/Dakar (GMT)',
                        'Africa/Abidjan'    => 'Afrique/Abidjan (GMT)',
                        'Africa/Casablanca' => 'Afrique/Casablanca (WET)',
                        'Europe/Paris'      => 'Europe/Paris (CET)',
                    ])->default('Africa/Douala'),
            ])->columns(2),

            Forms\Components\Section::make(__('app.admin.billing'))->schema([
                Forms\Components\TextInput::make('invoice_prefix')
                    ->label(__('app.admin.invoice_prefix'))->default('FAC')->maxLength(10),
                Forms\Components\TextInput::make('quote_prefix')
                    ->label(__('app.admin.quote_prefix'))->default('DEV')->maxLength(10),
            ])->columns(2),

            Forms\Components\Section::make('🟢 WhatsApp Business')
                ->description('Identifiants depuis Meta for Developers → WhatsApp → Configuration')
                ->schema([
                    Forms\Components\TextInput::make('whatsapp_phone_number_id')
                        ->label('Phone Number ID')->placeholder('123456789012345'),
                    Forms\Components\TextInput::make('whatsapp_business_account_id')
                        ->label('Business Account ID')->placeholder('987654321098765'),
                    Forms\Components\TextInput::make('whatsapp_access_token')
                        ->label(__('app.admin.access_token'))
                        ->password()->revealable()
                        ->placeholder('EAAxxxxxxxx...'),
                ])->columns(1),

            Forms\Components\Section::make(__('app.admin.ai_instructions'))
                ->schema([
                    RichEditor::make('gemini_system_prompt')
                        ->label(__('app.admin.ai_instructions_label'))
                        
                        ->columnSpanFull(),
                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('app.admin.business'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('owner_name')->label(__('app.admin.owner_name'))->searchable(),
                Tables\Columns\TextColumn::make('city')->label(__('app.admin.city')),
                Tables\Columns\BadgeColumn::make('plan')->label(__('app.admin.plan'))
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
