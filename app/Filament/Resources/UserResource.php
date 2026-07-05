<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations personnelles')->schema([
                Forms\Components\TextInput::make('name')->label('Nom')->required(),
                Forms\Components\TextInput::make('email')->label('Email')->email()->required(),
                Forms\Components\Select::make('role')->label('Rôle')
                    ->options([
                        'admin' => 'Administrateur',
                        'agent' => 'Agent',
                        'user'  => 'Utilisateur',
                    ])->required(),
                Forms\Components\Select::make('business_id')->label('Entreprise')
                    ->relationship('business', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('password')->label('Mot de passe')
                    ->password()->revealable()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn($context) => $context === 'create'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\BadgeColumn::make('role')->label('Rôle')
                    ->colors([
                        'danger'  => 'admin',
                        'warning' => 'agent',
                        'gray'    => 'user',
                    ]),
                Tables\Columns\TextColumn::make('business.name')->label('Entreprise')->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')->label('Vérifié')
                    ->dateTime('d/m/Y H:i')->sortable()
                    ->placeholder('Non vérifié'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')->boolean()->default(true),
                Tables\Columns\TextColumn::make('created_at')->label('Inscrit le')
                    ->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(['admin' => 'Admin', 'agent' => 'Agent', 'user' => 'Utilisateur']),
                Tables\Filters\Filter::make('verified')
                    ->query(fn($q) => $q->whereNotNull('email_verified_at'))
                    ->label('Email vérifié'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
