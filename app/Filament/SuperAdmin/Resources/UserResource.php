<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserResource\Pages;
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
    protected static ?string $navigationGroup = 'Gestion des Utilisateurs';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $modelLabelPlural = 'Utilisateurs';
    protected static ?string $slug = 'users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations personnelles')
                ->icon('heroicon-o-user')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom complet')
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
                        ->maxLength(255)
                        ->helperText('Laisser vide pour garder le mot de passe actuel (édition)'),
                ])->columns(2),

            Forms\Components\Section::make('Rôle & Accès')
                ->icon('heroicon-o-key')
                ->schema([
                    Forms\Components\Select::make('role')
                        ->label('Rôle')
                        ->options([
                            'admin'  => 'Administrateur',
                            'agent'  => 'Agent',
                            'user'   => 'Utilisateur',
                        ])
                        ->required()
                        ->default('user'),

                    Forms\Components\Toggle::make('is_super_admin')
                        ->label('Super Admin')
                        ->helperText('Accès complet au panneau super-admin')
                        ->default(false),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Compte actif')
                        ->helperText('Désactiver pour bloquer l\'accès')
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
                        ->nullable()
                        ->helperText('Laisser vide si pas encore assigné'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter(),

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
                    ->boolean(),

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
                    ->placeholder('Jamais'),

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

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->boolean(label: 'Actif', oppositeLabel: 'Inactif'),

                Tables\Filters\TernaryFilter::make('is_super_admin')
                    ->label('Super Admin')
                    ->boolean(label: 'Oui', oppositeLabel: 'Non'),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Se connecter en tant que')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Se connecter en tant que cet utilisateur ?')
                    ->modalDescription(fn ($record) => "Vous allez être connecté en tant que {$record->name} ({$record->email}). Votre session super-admin sera conservée.")
                    ->action(function ($record) {
                        if (!$record->is_active) {
                            Notification::make()
                                ->title('Impossible')
                                ->body('Cet utilisateur est désactivé.')
                                ->danger()
                                ->send();
                            return;
                        }

                        auth()->login($record);

                        return redirect()->route('filament.admin.pages.dashboard');
                    })
                    ->visible(fn ($record) => !$record->is_super_admin),

                Tables\Actions\Action::make('toggle_active')
                    ->label(fn ($record) => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->is_active ? 'Désactiver ce compte ?' : 'Activer ce compte ?')
                    ->action(function ($record) {
                        $record->update(['is_active' => !$record->is_active]);

                        Notification::make()
                            ->title($record->is_active ? 'Compte activé' : 'Compte désactivé')
                            ->success()
                            ->send();
                    }),

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
