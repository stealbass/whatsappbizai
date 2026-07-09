<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'users';

    public static function getModelLabel(): string
    {
        return __('app.admin.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.users');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        // Super-admins see all users, regular admins see only their business users
        if (auth()->user() && !auth()->user()->is_super_admin && auth()->user()->business_id) {
            $query->where('business_id', auth()->user()->business_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations personnelles')
                ->icon('heroicon-o-user')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->dehydrateStateUsing(fn ($state) => \Hash::make($state))
                        ->required(fn ($context) => $context === 'create')
                        ->maxLength(255),
                ])->columns(2),

            Forms\Components\Section::make('Rôle & Accès')
                ->icon('heroicon-o-key')
                ->schema([
                    Forms\Components\Select::make('role')
                        ->label('Rôle')
                        ->options([
                            'admin' => 'Admin',
                            'agent' => 'Agent',
                            'user'  => 'Utilisateur',
                        ])
                        ->required()
                        ->default('user'),

                    Forms\Components\Toggle::make('is_super_admin')
                        ->label('Super Admin')
                        ->default(false)
                        ->visible(fn () => auth()->user()?->is_super_admin ?? false),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                ])->columns(3),

            Forms\Components\Section::make('Entreprise')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Forms\Components\Select::make('business_id')
                        ->label('Entreprise')
                        ->relationship('business', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'admin' => 'Admin',
                        'agent' => 'Agent',
                        'user'  => 'User',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'admin' => 'danger',
                        'agent' => 'warning',
                        'user'  => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_super_admin')
                    ->label('Super Admin')
                    ->boolean()
                    ->visible(fn () => auth()->user()?->is_super_admin ?? false),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('business.name')
                    ->label('Entreprise')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Jamais')
                    ->visible(fn () => auth()->user()?->is_super_admin ?? false),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rôle')
                    ->options([
                        'admin' => 'Admin',
                        'agent' => 'Agent',
                        'user'  => 'User',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Se connecter en tant que')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Se connecter en tant que cet utilisateur ?')
                    ->action(fn ($record) => redirect(url("impersonate/{$record->id}?save_current=true")))
                    ->visible(fn ($record) => (auth()->user()?->is_super_admin ?? false) && !$record->is_super_admin),

                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
