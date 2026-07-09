<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Helpers\Country;
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
                        ->searchable()
                        ->options(Country::options())
                        ->default('CM')
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state) {
                                $timezones = Country::timezones($state);
                                $set('timezone', $timezones[0] ?? 'UTC');
                                $set('currency', Country::defaultCurrency($state));
                            }
                        }),
                    Forms\Components\Select::make('currency')
                        ->label('Devise')
                        ->options([
                            'XAF' => 'XAF',
                            'XOF' => 'XOF',
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'GBP' => 'GBP',
                            'ZAR' => 'ZAR',
                            'NGN' => 'NGN',
                            'GHS' => 'GHS',
                            'KES' => 'KES',
                            'MAD' => 'MAD',
                            'GNF' => 'GNF',
                            'EGP' => 'EGP',
                            'TND' => 'TND',
                            'DZD' => 'DZD',
                            'TZS' => 'TZS',
                            'UGX' => 'UGX',
                            'RWF' => 'RWF',
                            'ETB' => 'ETB',
                            'MGA' => 'MGA',
                            'MUR' => 'MUR',
                            'CAD' => 'CAD',
                            'AED' => 'AED',
                            'CNY' => 'CNY',
                            'INR' => 'INR',
                            'TRY' => 'TRY',
                            'AOA' => 'AOA',
                            'ZMW' => 'ZMW',
                            'MWK' => 'MWK',
                            'BWP' => 'BWP',
                            'SZL' => 'SZL',
                        ])
                        ->default('XAF'),
                    Forms\Components\Select::make('timezone')
                        ->label('Fuseau horaire')
                        ->options([
                            'Africa/Douala'        => 'Douala (WAT)',
                            'Africa/Brazzaville'   => 'Brazzaville (WAT)',
                            'Africa/Kinshasa'      => 'Kinshasa (WAT)',
                            'Africa/Lubumbashi'    => 'Lubumbashi (CAT)',
                            'Africa/Libreville'    => 'Libreville (WAT)',
                            'Africa/Malabo'        => 'Malabo (WAT)',
                            'Africa/Bangui'        => 'Bangui (WAT)',
                            'Africa/Ndjamena'      => 'N\'Djamena (WAT)',
                            'Africa/Dakar'         => 'Dakar (GMT)',
                            'Africa/Abidjan'       => 'Abidjan (GMT)',
                            'Africa/Bamako'        => 'Bamako (GMT)',
                            'Africa/Ouagadougou'   => 'Ouagadougou (GMT)',
                            'Africa/Niamey'        => 'Niamey (GMT)',
                            'Africa/Lome'          => 'Lomé (GMT)',
                            'Africa/Porto-Novo'    => 'Porto-Novo (WAT)',
                            'Africa/Conakry'       => 'Conakry (GMT)',
                            'Africa/Lagos'         => 'Lagos (WAT)',
                            'Africa/Accra'         => 'Accra (GMT)',
                            'Africa/Casablanca'    => 'Casablanca (WET)',
                            'Africa/Tunis'         => 'Tunis (CET)',
                            'Africa/Algiers'       => 'Alger (CET)',
                            'Africa/Cairo'         => 'Le Caire (EET)',
                            'Africa/Nairobi'       => 'Nairobi (EAT)',
                            'Africa/Dar_es_Salaam' => 'Dar es Salaam (EAT)',
                            'Africa/Kampala'       => 'Kampala (EAT)',
                            'Africa/Kigali'        => 'Kigali (CAT)',
                            'Africa/Addis_Ababa'   => 'Addis-Abeba (EAT)',
                            'Africa/Johannesburg'  => 'Johannesburg (SAST)',
                            'Africa/Maputo'        => 'Maputo (CAT)',
                            'Africa/Lusaka'        => 'Lusaka (CAT)',
                            'Africa/Harare'        => 'Harare (CAT)',
                            'Africa/Blantyre'      => 'Blantyre (CAT)',
                            'Africa/Luanda'        => 'Luanda (WAT)',
                            'Indian/Antananarivo'  => 'Antananarivo (EAT)',
                            'Indian/Mauritius'     => 'Port Louis (MUT)',
                            'Europe/Paris'         => 'Paris (CET)',
                            'Europe/Brussels'      => 'Bruxelles (CET)',
                            'Europe/London'        => 'Londres (GMT)',
                            'America/New_York'     => 'New York (EST)',
                            'America/Chicago'      => 'Chicago (CST)',
                            'America/Los_Angeles'  => 'Los Angeles (PST)',
                            'America/Toronto'      => 'Toronto (EST)',
                            'America/Vancouver'    => 'Vancouver (PST)',
                            'Asia/Dubai'           => 'Dubaï (GST)',
                            'Asia/Shanghai'        => 'Shanghai (CST)',
                            'Asia/Kolkata'         => 'Kolkata (IST)',
                            'Europe/Istanbul'      => 'Istanbul (TRT)',
                            'UTC'                  => 'UTC',
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

                Tables\Columns\IconColumn::make('sandbox_mode')
                    ->label('Sandbox')
                    ->boolean()
                    ->trueIcon('heroicon-o-beaker')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('contacts_count')
                    ->label('Contacts')
                    ->counts('contacts')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('sandbox_messages_count')
                    ->label('Sandbox Msgs')
                    ->counts('sandboxMessages')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    }),

                Tables\Actions\Action::make('viewSandboxMessages')
                    ->label('Sandbox')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('info')
                    ->modalHeading('Messages Sandbox')
                    ->modalContent(function ($record) {
                        $messages = $record->sandboxMessages()
                            ->orderByDesc('created_at')
                            ->limit(50)
                            ->get();

                        if ($messages->isEmpty()) {
                            return view('filament.resources.business-resource.sandbox-messages-empty');
                        }

                        return view('filament.resources.business-resource.sandbox-messages', [
                            'messages' => $messages,
                        ]);
                    }),

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
