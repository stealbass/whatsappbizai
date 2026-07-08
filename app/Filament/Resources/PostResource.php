<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Blog';

    protected static ?string $modelLabel = 'Article';

    protected static ?string $pluralModelLabel = 'Articles';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->is_super_admin ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Slug & Image')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('featured_image')
                            ->image()
                            ->directory('blog')
                            ->maxSize(2048),

                        Forms\Components\Select::make('category')
                            ->options([
                                'astuce' => 'Astuce',
                                'tutorial' => 'Tutoriel',
                                'news' => 'Actualité',
                                'cas_client' => 'Cas client',
                                'ia' => 'Intelligence Artificielle',
                            ])
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('author_name')
                            ->default('WhatsAppBizAI')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Tabs::make('content_tabs')->schema([
                    Forms\Components\Tabs\Tab::make('Français')
                        ->icon('heroicon-o-language')
                        ->schema([
                            Forms\Components\TextInput::make('title_fr')
                                ->label('Titre')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\RichEditor::make('excerpt_fr')
                                ->label('Extrait')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'link'])
                                ->maxLength(500),

                            Forms\Components\RichEditor::make('content_fr')
                                ->label('Contenu')
                                ->required()
                                ->toolbarButtons([
                                    'bold', 'italic', 'underline', 'strike',
                                    'link', 'h2', 'h3',
                                    'bulletList', 'orderedList',
                                    'blockquote', 'codeBlock',
                                    'undo', 'redo',
                                ]),

                            Forms\Components\TextInput::make('meta_title_fr')
                                ->label('Meta Title')
                                ->maxLength(255),

                            Forms\Components\Textarea::make('meta_description_fr')
                                ->label('Meta Description')
                                ->rows(2)
                                ->maxLength(500),
                        ]),

                    Forms\Components\Tabs\Tab::make('English')
                        ->icon('heroicon-o-language')
                        ->schema([
                            Forms\Components\TextInput::make('title_en')
                                ->label('Title')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\RichEditor::make('excerpt_en')
                                ->label('Excerpt')
                                ->toolbarButtons(['bold', 'italic', 'underline', 'link'])
                                ->maxLength(500),

                            Forms\Components\RichEditor::make('content_en')
                                ->label('Content')
                                ->required()
                                ->toolbarButtons([
                                    'bold', 'italic', 'underline', 'strike',
                                    'link', 'h2', 'h3',
                                    'bulletList', 'orderedList',
                                    'blockquote', 'codeBlock',
                                    'undo', 'redo',
                                ]),

                            Forms\Components\TextInput::make('meta_title_en')
                                ->label('Meta Title')
                                ->maxLength(255),

                            Forms\Components\Textarea::make('meta_description_en')
                                ->label('Meta Description')
                                ->rows(2)
                                ->maxLength(500),
                        ]),
                ])->columnSpanFull(),

                Forms\Components\Section::make('Publication')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publié')
                            ->default(false),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Date de publication'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->circular(),

                Tables\Columns\TextColumn::make('title_fr')
                    ->label('Titre (FR)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'astuce' => 'info',
                        'tutorial' => 'success',
                        'news' => 'warning',
                        'cas_client' => 'primary',
                        'ia' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'astuce' => 'Astuce',
                        'tutorial' => 'Tutoriel',
                        'news' => 'Actualité',
                        'cas_client' => 'Cas client',
                        'ia' => 'IA',
                    ]),

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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
