<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 6;
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.services');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.service');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.services');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_catalogue');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label(__('app.admin.service_name'))->required(),
            RichEditor::make('description')->label(__('app.admin.description')),
            Forms\Components\TextInput::make('unit_price')->label(__('app.admin.unit_price'))
                ->numeric()->required()->prefix('FCFA'),
            Forms\Components\Select::make('currency')
                ->options(['XAF' => 'XAF (FCFA)', 'EUR' => 'EUR (€)', 'USD' => 'USD ($)'])
                ->default('XAF'),
            Forms\Components\Select::make('unit')->label(__('app.admin.unit'))
                ->options([
                    'forfait' => __('app.admin.flat_rate'),
                    'heure'   => __('app.admin.hour'),
                    'jour'    => __('app.admin.day'),
                    'mois'    => __('app.admin.month'),
                    'unité'   => __('app.admin.unit'),
                ])->default('forfait'),
            Forms\Components\Toggle::make('is_active')->label(__('app.admin.active'))->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('app.admin.service'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('unit_price')->label(__('app.admin.amount'))
                    ->sortable()->alignRight(),
                Tables\Columns\TextColumn::make('currency')->label(__('app.admin.currency')),
                Tables\Columns\TextColumn::make('unit')->label(__('app.admin.unit')),
                Tables\Columns\IconColumn::make('is_active')->label(__('app.admin.active'))->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit'   => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
