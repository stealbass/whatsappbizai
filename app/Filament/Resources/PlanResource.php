<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
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
    protected static ?string $slug = 'plans';

    public static function getModelLabel(): string
    {
        return 'Plan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Plans';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations générales')
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) => $set('name', $state ? Str::title($state) : '')),
                    Forms\Components\TextInput::make('name')
                        ->label('Nom du plan')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\RichEditor::make('description')
                        ->label('Description')
                        ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link'])
                        ->maxLength(500),
                ])->columns(2),

            Forms\Components\Section::make('Tarification')
                ->schema([
                    Forms\Components\TextInput::make('price_monthly')
                        ->label('Prix mensuel')
                        ->required()
                        ->numeric()
                        ->default(0)
                        ->suffix('XAF'),
                    Forms\Components\TextInput::make('price_yearly')
                        ->label('Prix annuel')
                        ->required()
                        ->numeric()
                        ->default(0)
                        ->suffix('XAF'),
                    Forms\Components\TextInput::make('currency')
                        ->label('Devise')
                        ->default('XAF')
                        ->maxLength(10),
                ])->columns(3),

            Forms\Components\Section::make('Limites')
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
                ->schema([
                    Forms\Components\KeyValue::make('features')
                        ->label('Features')
                        ->reorderable(),
                ]),

            Forms\Components\Section::make('Affichage')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Mis en avant')
                        ->default(false),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
