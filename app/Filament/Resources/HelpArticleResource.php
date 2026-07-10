<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HelpArticleResource\Pages;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class HelpArticleResource extends Resource
{
    protected static ?string $model = HelpArticle::class;
    protected static ?string $navigationIcon   = 'heroicon-o-book-open';
    protected static ?string $navigationGroup  = 'Help Center';
    protected static ?string $navigationLabel  = 'Articles / Tutoriels / Guides';
    protected static ?string $modelLabel       = 'Article Help';
    protected static ?string $pluralModelLabel = 'Articles Help';
    protected static ?int    $navigationSort   = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_help_center') ?? 'Help Center';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Paramètres généraux')->schema([
                Forms\Components\Select::make('help_category_id')
                    ->label('Catégorie')
                    ->options(HelpCategory::pluck('name_fr', 'id'))
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'article'  => '📄 Article',
                        'tutorial' => '🎓 Tutoriel',
                        'guide'    => '🗺️ Guide interactif',
                    ])
                    ->required()
                    ->default('article'),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Généré automatiquement depuis le titre FR si vide'),

                Forms\Components\Select::make('difficulty')
                    ->label('Difficulté')
                    ->options([
                        'beginner'     => '🟢 Débutant',
                        'intermediate' => '🟡 Intermédiaire',
                        'advanced'     => '🔴 Avancé',
                    ])
                    ->nullable(),

                Forms\Components\TextInput::make('reading_minutes')
                    ->label('Durée de lecture (min)')
                    ->numeric()
                    ->nullable(),

                Forms\Components\TextInput::make('author_name')
                    ->label('Auteur')
                    ->default('WhatsAppBizAI')
                    ->maxLength(255),

                Forms\Components\FileUpload::make('featured_image')
                    ->label('Image à la une')
                    ->image()
                    ->directory('help')
                    ->maxSize(2048),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordre')
                    ->numeric()
                    ->default(0),
            ])->columns(3),

            Forms\Components\Tabs::make('Contenu')->schema([
                Forms\Components\Tabs\Tab::make('🇫🇷 Français')->schema([
                    Forms\Components\TextInput::make('title_fr')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    Forms\Components\Textarea::make('excerpt_fr')
                        ->label('Résumé / Chapeau')
                        ->rows(2)
                        ->maxLength(400),

                    Forms\Components\RichEditor::make('content_fr')
                        ->label('Contenu complet')
                        ->required()
                        ->toolbarButtons([
                            'bold','italic','underline','strike',
                            'link','h2','h3',
                            'bulletList','orderedList',
                            'blockquote','codeBlock',
                            'undo','redo',
                        ])
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('meta_title_fr')
                        ->label('Meta Title SEO')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('meta_description_fr')
                        ->label('Meta Description SEO')
                        ->rows(2)
                        ->maxLength(500),
                ]),

                Forms\Components\Tabs\Tab::make('🇬🇧 English')->schema([
                    Forms\Components\TextInput::make('title_en')
                        ->label('Title')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('excerpt_en')
                        ->label('Excerpt')
                        ->rows(2)
                        ->maxLength(400),

                    Forms\Components\RichEditor::make('content_en')
                        ->label('Full Content')
                        ->required()
                        ->toolbarButtons([
                            'bold','italic','underline','strike',
                            'link','h2','h3',
                            'bulletList','orderedList',
                            'blockquote','codeBlock',
                            'undo','redo',
                        ])
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('meta_title_en')
                        ->label('SEO Meta Title')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('meta_description_en')
                        ->label('SEO Meta Description')
                        ->rows(2)
                        ->maxLength(500),
                ]),

                Forms\Components\Tabs\Tab::make('⚙️ Étapes (Guide interactif)')->schema([
                    Forms\Components\Repeater::make('steps')
                        ->label('Étapes du guide')
                        ->schema([
                            Forms\Components\TextInput::make('title_fr')->label('Titre étape (FR)')->required(),
                            Forms\Components\TextInput::make('title_en')->label('Step title (EN)')->required(),
                            Forms\Components\Textarea::make('description_fr')->label('Description (FR)')->rows(3),
                            Forms\Components\Textarea::make('description_en')->label('Description (EN)')->rows(3),
                            Forms\Components\TextInput::make('icon')->label('Icône (emoji)')->default('✅'),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->reorderable()
                        ->addActionLabel('+ Ajouter une étape'),
                ]),
            ])->columnSpanFull(),

            Forms\Components\Section::make('Publication')->schema([
                Forms\Components\Toggle::make('is_published')->label('Publié')->default(false),
                Forms\Components\DateTimePicker::make('published_at')->label('Date de publication'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tutorial' => 'success',
                        'guide'    => 'warning',
                        default    => 'info',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'tutorial' => '🎓 Tutoriel',
                        'guide'    => '🗺️ Guide',
                        default    => '📄 Article',
                    }),

                Tables\Columns\TextColumn::make('title_fr')
                    ->label('Titre (FR)')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category.name_fr')
                    ->label('Catégorie')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('difficulty')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'beginner'     => 'success',
                        'intermediate' => 'warning',
                        'advanced'     => 'danger',
                        default        => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),

                Tables\Columns\TextColumn::make('views')
                    ->label('Vues')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(['article' => 'Article', 'tutorial' => 'Tutoriel', 'guide' => 'Guide']),

                Tables\Filters\SelectFilter::make('help_category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name_fr'),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Statut')
                    ->trueLabel('Publié')
                    ->falseLabel('Brouillon'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHelpArticles::route('/'),
            'create' => Pages\CreateHelpArticle::route('/create'),
            'edit'   => Pages\EditHelpArticle::route('/{record}/edit'),
        ];
    }
}
