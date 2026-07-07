<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\RichEditor;

class SiteSettingResource extends Resource
{
    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $model = SiteSetting::class;

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

                // ─── Branding ──────────────────────────────────────────────
                Forms\Components\Tabs\Tab::make('Branding')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\Section::make('Identité visuelle')->schema([
                            Forms\Components\TextInput::make('site_name_fr')
                                ->label('Nom du site (FR)')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('site_name_en')
                                ->label('Site name (EN)')
                                ->required()
                                ->maxLength(255),
                        ])->columns(2),

                        Forms\Components\Section::make('Slogan / Tagline')->schema([
                            Forms\Components\TextInput::make('site_tagline_fr')
                                ->label('Slogan (FR)')
                                ->maxLength(500),
                            Forms\Components\TextInput::make('site_tagline_en')
                                ->label('Tagline (EN)')
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

                // ─── SEO ───────────────────────────────────────────────────
                Forms\Components\Tabs\Tab::make('SEO')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        Forms\Components\Section::make('Meta Tags FR')->schema([
                            Forms\Components\TextInput::make('meta_title_fr')
                                ->label('Meta Title (FR)')
                                ->maxLength(255)
                                ->helperText('50-60 caractères recommandés'),
                            Forms\Components\Textarea::make('meta_description_fr')
                                ->label('Meta Description (FR)')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('120-160 caractères recommandés'),
                            Forms\Components\Textarea::make('meta_keywords_fr')
                                ->label('Meta Keywords (FR)')
                                ->rows(2)
                                ->maxLength(500),
                        ])->columns(1),

                        Forms\Components\Section::make('Meta Tags EN')->schema([
                            Forms\Components\TextInput::make('meta_title_en')
                                ->label('Meta Title (EN)')
                                ->maxLength(255)
                                ->helperText('Recommended: 50-60 characters'),
                            Forms\Components\Textarea::make('meta_description_en')
                                ->label('Meta Description (EN)')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Recommended: 120-160 characters'),
                            Forms\Components\Textarea::make('meta_keywords_en')
                                ->label('Meta Keywords (EN)')
                                ->rows(2)
                                ->maxLength(500),
                        ])->columns(1),

                        Forms\Components\Section::make('Open Graph & URL')->schema([
                            Forms\Components\TextInput::make('canonical_url')
                                ->label(__('app.admin.canonical_url'))
                                ->url()
                                ->nullable()
                                ->maxLength(255),
                            Forms\Components\FileUpload::make('og_image_path')
                                ->label(__('app.admin.og_image'))
                                ->image()
                                ->imageEditor()
                                ->directory('site')
                                ->nullable(),
                        ])->columns(2),
                    ]),

                // ─── Contact & Social ──────────────────────────────────────
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

                // ─── Legal ─────────────────────────────────────────────────
                Forms\Components\Tabs\Tab::make('Legal')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Section::make('Politique de confidentialité')->schema([
                            RichEditor::make('privacy_policy_fr')
                                ->label('Confidentialité (FR)')
                                ->columnSpanFull(),
                            RichEditor::make('privacy_policy_en')
                                ->label('Privacy Policy (EN)')
                                ->columnSpanFull(),
                        ]),

                        Forms\Components\Section::make('Conditions d\'utilisation')->schema([
                            RichEditor::make('terms_conditions_fr')
                                ->label('Conditions (FR)')
                                ->columnSpanFull(),
                            RichEditor::make('terms_conditions_en')
                                ->label('Terms of Service (EN)')
                                ->columnSpanFull(),
                        ]),

                        Forms\Components\Section::make('Politique de cookies')->schema([
                            RichEditor::make('cookie_policy_fr')
                                ->label('Cookies (FR)')
                                ->columnSpanFull(),
                            RichEditor::make('cookie_policy_en')
                                ->label('Cookie Policy (EN)')
                                ->columnSpanFull(),
                        ]),
                    ]),

                // ─── Footer ────────────────────────────────────────────────
                Forms\Components\Tabs\Tab::make(__('app.admin.footer'))
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->schema([
                        Forms\Components\Section::make('Description du footer')->schema([
                            RichEditor::make('footer_description_fr')
                                ->label('Description footer (FR)')
                                ->columnSpanFull(),
                            RichEditor::make('footer_description_en')
                                ->label('Footer description (EN)')
                                ->columnSpanFull(),
                        ]),

                        Forms\Components\Section::make('Copyright')->schema([
                            Forms\Components\TextInput::make('footer_copyright_fr')
                                ->label('Copyright (FR)')
                                ->maxLength(255)
                                ->default('© ' . date('Y') . ' WhatsAppBizAI. Tous droits réservés.'),
                            Forms\Components\TextInput::make('footer_copyright_en')
                                ->label('Copyright (EN)')
                                ->maxLength(255)
                                ->default('© ' . date('Y') . ' WhatsAppBizAI. All rights reserved.'),
                        ])->columns(2),
                    ]),

                // ─── Business Info ─────────────────────────────────────────
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

                // ─── Social Proof ──────────────────────────────────────────
                Forms\Components\Tabs\Tab::make('Social Proof')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Section::make(__('app.admin.social_proof'))->schema([
                            Forms\Components\TextInput::make('stats_users')
                                ->label(__('app.admin.stat_response'))
                                ->default('< 30s'),
                            Forms\Components\TextInput::make('stats_invoices')
                                ->label(__('app.admin.stat_avail'))
                                ->default('24/7'),
                            Forms\Components\TextInput::make('stats_messages')
                                ->label(__('app.admin.stat_langs'))
                                ->default('FR + EN'),
                            Forms\Components\TextInput::make('stats_countries')
                                ->label(__('app.admin.stat_secure'))
                                ->default('100%'),
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
