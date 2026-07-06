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

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.site_settings');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.site_settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.site_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_settings');
    }

    protected static ?string $recordTitleAttribute = 'site_name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('settings')->schema([
                Forms\Components\Tabs\Tab::make('Branding')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\Section::make(__('app.admin.visual_identity'))->schema([
                            Forms\Components\TextInput::make('site_name')
                                ->label(__('app.admin.site_name'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('site_tagline')
                                ->label(__('app.admin.tagline'))
                                ->maxLength(500),
                        ])->columns(2),

                        Forms\Components\Section::make(__('app.admin.logo_favicon'))->schema([
                            Forms\Components\FileUpload::make('logo_path')
                                ->label(__('app.admin.logo'))
                                ->image()
                                ->imageEditor()
                                ->directory('site')
                                ->nullable(),
                            Forms\Components\FileUpload::make('favicon_path')
                                ->label(__('app.admin.favicon'))
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
                                ->label(__('app.admin.meta_title'))
                                ->maxLength(255),
                            Forms\Components\Textarea::make('meta_description')
                                ->label(__('app.admin.meta_description'))
                                ->rows(3)
                                ->maxLength(500),
                            Forms\Components\Textarea::make('meta_keywords')
                                ->label(__('app.admin.meta_keywords'))
                                ->rows(2)
                                ->maxLength(500),
                            Forms\Components\TextInput::make('canonical_url')
                                ->label(__('app.admin.canonical_url'))
                                ->url()
                                ->nullable()
                                ->maxLength(255),
                        ])->columns(2),

                        Forms\Components\Section::make('Open Graph')->schema([
                            Forms\Components\FileUpload::make('og_image_path')
                                ->label(__('app.admin.og_image'))
                                ->image()
                                ->imageEditor()
                                ->directory('site')
                                ->nullable(),
                        ]),
                    ]),

                Forms\Components\Tabs\Tab::make('Contact & Social')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\Section::make(__('app.admin.contact'))->schema([
                            Forms\Components\TextInput::make('contact_email')
                                ->label(__('app.admin.contact_email'))
                                ->email()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('contact_phone')
                                ->label(__('app.admin.phone'))
                                ->tel()
                                ->placeholder('+237 6XX XXX XXX')
                                ->maxLength(50),
                            Forms\Components\TextInput::make('whatsapp_number')
                                ->label(__('app.admin.whatsapp_number'))
                                ->tel()
                                ->placeholder('+237 6XX XXX XXX')
                                ->maxLength(50),
                        ])->columns(3),

                        Forms\Components\Section::make(__('app.admin.social_networks'))->schema([
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
                        Forms\Components\Textarea::make('privacy_policy')
                            ->label(__('app.admin.privacy_policy'))
                            ->rows(10)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('terms_conditions')
                            ->label(__('app.admin.terms_conditions'))
                            ->rows(10)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('cookie_policy')
                            ->label(__('app.admin.cookie_policy'))
                            ->rows(10)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Tabs\Tab::make(__('app.admin.footer'))
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->schema([
                        Forms\Components\Section::make(__('app.admin.footer'))->schema([
                            Forms\Components\Textarea::make('footer_description')
                                ->label(__('app.admin.footer_description'))
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
                        Forms\Components\Section::make(__('app.admin.business_info'))->schema([
                            Forms\Components\TextInput::make('business_name')
                                ->label(__('app.admin.company_name'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('business_city')
                                ->label(__('app.admin.city'))
                                ->maxLength(255),
                            Forms\Components\TextInput::make('business_country')
                                ->label(__('app.admin.country'))
                                ->maxLength(2),
                            Forms\Components\DatePicker::make('business_founding_date')
                                ->label(__('app.admin.founding_date')),
                        ])->columns(2),
                    ]),

                Forms\Components\Tabs\Tab::make('Social Proof')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Section::make(__('app.admin.social_proof'))->schema([
                            Forms\Components\TextInput::make('stats_users')
                                ->label(__('app.admin.stats_users'))
                                ->numeric()
                                ->default(0),
                            Forms\Components\TextInput::make('stats_invoices')
                                ->label(__('app.admin.stats_invoices'))
                                ->numeric()
                                ->default(0),
                            Forms\Components\TextInput::make('stats_messages')
                                ->label(__('app.admin.stats_messages'))
                                ->numeric()
                                ->default(0),
                            Forms\Components\TextInput::make('stats_countries')
                                ->label(__('app.admin.stats_countries'))
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
