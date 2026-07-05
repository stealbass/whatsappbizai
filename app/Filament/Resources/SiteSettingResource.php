<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Pages\EditRecord;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Paramètres du site';

    protected static ?string $modelLabel = 'Paramètres du site';

    protected static ?string $recordTitleAttribute = 'site_name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('settings')->schema([
                Forms\Components\Tabs\Tab::make('Branding')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\Section::make('Identité visuelle')->schema([
                            Forms\Components\TextInput::make('site_name')
                                ->label('Nom du site')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('site_tagline')
                                ->label('Slogan')
                                ->maxLength(500),
                        ])->columns(2),

                        Forms\Components\Section::make('Logo & Favicon')->schema([
                            Forms\Components\FileUpload::make('logo_path')
                                ->label('Logo')
                                ->image()
                                ->imageEditor()
                                ->directory('site')
                                ->nullable(),
                            Forms\Components\FileUpload::make('favicon_path')
                                ->label('Favicon')
                                ->image()
                                ->imageEditor()
                                ->directory('site')
                                ->nullable(),
                        ])->columns(2),
                    ]),

                Forms\Components\Tabs\Tab::make('SEO')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        Forms\Components\Section::make('SEO')->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->label('Meta Title')
                                ->maxLength(255),
                            Forms\Components\Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3)
                                ->maxLength(500),
                            Forms\Components\Textarea::make('meta_keywords')
                                ->label('Meta Keywords')
                                ->rows(2)
                                ->maxLength(500),
                            Forms\Components\TextInput::make('canonical_url')
                                ->label('URL Canonique')
                                ->url()
                                ->nullable()
                                ->maxLength(255),
                        ])->columns(2),

                        Forms\Components\Section::make('Open Graph')->schema([
                            Forms\Components\FileUpload::make('og_image_path')
                                ->label('Image OG')
                                ->image()
                                ->imageEditor()
                                ->directory('site')
                                ->nullable(),
                        ]),
                    ]),

                Forms\Components\Tabs\Tab::make('Contact & Social')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\Section::make('Contact')->schema([
                            Forms\Components\TextInput::make('contact_email')
                                ->label('Email de contact')
                                ->email()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('contact_phone')
                                ->label('Téléphone')
                                ->tel()
                                ->placeholder('+237 6XX XXX XXX')
                                ->maxLength(50),
                            Forms\Components\TextInput::make('whatsapp_number')
                                ->label('Numéro WhatsApp')
                                ->tel()
                                ->placeholder('+237 6XX XXX XXX')
                                ->maxLength(50),
                        ])->columns(3),

                        Forms\Components\Section::make('Réseaux sociaux')->schema([
                            Forms\Components\TextInput::make('facebook_url')
                                ->label('Facebook')
                                ->url()
                                ->nullable()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('twitter_url')
                                ->label('Twitter / X')
                                ->url()
                                ->nullable()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('linkedin_url')
                                ->label('LinkedIn')
                                ->url()
                                ->nullable()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('instagram_url')
                                ->label('Instagram')
                                ->url()
                                ->nullable()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('youtube_url')
                                ->label('YouTube')
                                ->url()
                                ->nullable()
                                ->maxLength(255),
                        ])->columns(3),
                    ]),

                Forms\Components\Tabs\Tab::make('Legal')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\RichEditor::make('privacy_policy')
                            ->label('Politique de confidentialité')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('terms_conditions')
                            ->label('Conditions d\'utilisation')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('cookie_policy')
                            ->label('Politique de cookies')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Tabs\Tab::make('Footer')
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->schema([
                        Forms\Components\Section::make('Footer')->schema([
                            Forms\Components\Textarea::make('footer_description')
                                ->label('Description du footer')
                                ->rows(4)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('footer_copyright')
                                ->label('Copyright')
                                ->maxLength(255),
                        ]),
                    ]),

                Forms\Components\Tabs\Tab::make('Business Info')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\Section::make('Informations de l\'entreprise')->schema([
                            Forms\Components\TextInput::make('business_name')
                                ->label('Nom de l\'entreprise')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('business_city')
                                ->label('Ville')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('business_country')
                                ->label('Pays')
                                ->maxLength(2),
                            Forms\Components\DatePicker::make('business_founding_date')
                                ->label('Date de création'),
                        ])->columns(2),
                    ]),

                Forms\Components\Tabs\Tab::make('Social Proof')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Section::make('Statistiques affichées')->schema([
                            Forms\Components\TextInput::make('stats_users')
                                ->label('Nombre d\'utilisateurs')
                                ->numeric()
                                ->default(0),
                            Forms\Components\TextInput::make('stats_invoices')
                                ->label('Nombre de factures')
                                ->numeric()
                                ->default(0),
                            Forms\Components\TextInput::make('stats_messages')
                                ->label('Nombre de messages')
                                ->numeric()
                                ->default(0),
                            Forms\Components\TextInput::make('stats_countries')
                                ->label('Nombre de pays')
                                ->numeric()
                                ->default(0),
                        ])->columns(4),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSiteSettings::route('/'),
            'edit'   => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
