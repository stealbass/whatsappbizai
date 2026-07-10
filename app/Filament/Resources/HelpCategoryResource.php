<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HelpCategoryResource\Pages;
use App\Models\HelpCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HelpCategoryResource extends Resource
{
    protected static ?string $model = HelpCategory::class;
    protected static ?string $navigationIcon  = 'heroicon-o-folder-open';
    protected static ?string $navigationGroup = 'Help Center';
    protected static ?string $navigationLabel = 'Catégories';
    protected static ?string $modelLabel      = 'Catégorie';
    protected static ?string $pluralModelLabel = 'Catégories';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_help_center') ?? 'Help Center';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identité')->schema([
                Forms\Components\TextInput::make('slug')
                    ->required()->unique(ignoreRecord: true)->maxLength(255),
                Forms\Components\TextInput::make('icon')
                    ->label('Icône (emoji)')->default('📄')->maxLength(10),
                Forms\Components\ColorPicker::make('color')
                    ->label('Couleur')->default('#0ea5e9'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordre')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')->default(true),
            ])->columns(3),

            Forms\Components\Tabs::make('translations')->schema([
                Forms\Components\Tabs\Tab::make('Français')->schema([
                    Forms\Components\TextInput::make('name_fr')
                        ->label('Nom')->required()->maxLength(255),
                    Forms\Components\Textarea::make('description_fr')
                        ->label('Description')->rows(3),
                ]),
                Forms\Components\Tabs\Tab::make('English')->schema([
                    Forms\Components\TextInput::make('name_en')
                        ->label('Name')->required()->maxLength(255),
                    Forms\Components\Textarea::make('description_en')
                        ->label('Description')->rows(3),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')->label('')->searchable(false)->width(40),
                Tables\Columns\TextColumn::make('name_fr')->label('Nom (FR)')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name_en')->label('Name (EN)')->searchable(),
                Tables\Columns\TextColumn::make('published_articles_count')
                    ->label('Articles')
                    ->counts('publishedArticles')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Ordre')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
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
            'index'  => Pages\ListHelpCategories::route('/'),
            'create' => Pages\CreateHelpCategory::route('/create'),
            'edit'   => Pages\EditHelpCategory::route('/{record}/edit'),
        ];
    }
}
