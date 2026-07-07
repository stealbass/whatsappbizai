<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Plans & Tarification';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Plan';
    protected static ?string $modelLabelPlural = 'Plans';
    protected static ?string $slug = 'plans';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations générales')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->helperText('Identifiant unique (ex: starter, business)')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) => $set('name', $state ? Str::title($state) : '')),

                    Forms\Components\TextInput::make('name')
                        ->label('Nom du plan')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->maxLength(500),
                ])->columns(2),

            Forms\Components\Section::make('Tarification')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\TextInput::make('price_monthly')
                        ->label('Prix mensuel')
                        ->required()
                        ->numeric()
                        ->default(0)
                        ->suffix('XAF')
                        ->minValue(0),

                    Forms\Components\TextInput::make('price_yearly')
                        ->label('Prix annuel')
                        ->required()
                        ->numeric()
                        ->default(0)
                        ->suffix('XAF')
                        ->minValue(0),

                    Forms\Components\TextInput::make('currency')
                        ->label('Devise')
                        ->default('XAF')
                        ->maxLength(10),
                ])->columns(3),

            Forms\Components\Section::make('Limites')
                ->icon('heroicon-o-chart-bar')
                ->schema([
                    Forms\Components\TextInput::make('max_contacts')
                        ->label('Contacts max')
                        ->required()
                        ->numeric()
                        ->default(50)
                        ->helperText('-1 = illimité'),

                    Forms\Components\TextInput::make('max_invoices')
                        ->label('Factures max')
                        ->required()
                        ->numeric()
                        ->default(10)
                        ->helperText('-1 = illimité'),

                    Forms\Components\TextInput::make('max_messages')
                        ->label('Messages max')
                        ->required()
                        ->numeric()
                        ->default(100)
                        ->helperText('-1 = illimité'),
                ])->columns(3),

            Forms\Components\Section::make('Fonctionnalités')
                ->icon('heroicon-o-puzzle-piece')
                ->schema([
                    Forms\Components\KeyValue::make('features')
                        ->label('Features')
                        ->helperText('Clé = feature slug, valeur = true/false ou description')
                        ->reorderable(),
                ]),

            Forms\Components\Section::make('Affichage')
                ->icon('heroicon-o-eye')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('Mis en avant')
                        ->default(false),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->slug),

                Tables\Columns\TextColumn::make('price_monthly')
                    ->label('Mensuel')
                    ->money($record?->currency ?? 'XAF')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_yearly')
                    ->label('Annuel')
                    ->money($record?->currency ?? 'XAF')
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_contacts')
                    ->label('Contacts')
                    ->formatStateUsing(fn ($state) => $state == -1 ? '∞' : number_format($state))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('max_invoices')
                    ->label('Factures')
                    ->formatStateUsing(fn ($state) => $state == -1 ? '∞' : number_format($state))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('max_messages')
                    ->label('Messages')
                    ->formatStateUsing(fn ($state) => $state == -1 ? '∞' : number_format($state))
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                Tables\Columns\TextColumn::make('businesses_count')
                    ->label('Businesses')
                    ->counts('businesses')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->boolean(label: 'Actif', oppositeLabel: 'Inactif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Supprimer ce plan supprimera aussi les associations.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit'   => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
