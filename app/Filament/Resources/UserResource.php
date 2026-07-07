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
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $model = User::class;

    public static function getNavigationLabel(): string
    {
        return __('app.admin.users');
    }

    public static function getModelLabel(): string
    {
        return __('app.admin.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.admin.users');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.admin.nav_administration');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('app.admin.personal_info'))->schema([
                Forms\Components\TextInput::make('name')->label(__('app.admin.name'))->required(),
                Forms\Components\TextInput::make('email')->label(__('app.admin.email'))->email()->required(),
                Forms\Components\Select::make('role')->label(__('app.admin.role'))
                    ->options([
                        'admin' => __('app.admin.admin_role'),
                        'agent' => __('app.admin.agent_role'),
                        'user'  => __('app.admin.user_role'),
                    ])->required(),
                Forms\Components\Select::make('business_id')->label(__('app.admin.business'))
                    ->relationship('business', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('password')->label(__('app.admin.password'))
                    ->password()->revealable()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn($context) => $context === 'create'),
            ])->columns(2),

            Forms\Components\Section::make('Statut & Accès')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label(__('app.admin.active'))
                    ->default(true),
                Forms\Components\Toggle::make('is_super_admin')
                    ->label('Super Admin')
                    ->helperText('Accès complet au panneau super-admin')
                    ->default(false),
                Forms\Components\DateTimePicker::make('last_login_at')
                    ->label('Dernière connexion')
                    ->disabled(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('app.admin.name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label(__('app.admin.email'))->searchable(),
                Tables\Columns\TextColumn::make('role')->label(__('app.admin.role'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'admin' => __('app.admin.admin_role'),
                        'agent' => __('app.admin.agent_role'),
                        'user'  => __('app.admin.user_role'),
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'admin' => 'danger',
                        'agent' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('business.name')->label(__('app.admin.business'))->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.admin.active'))->boolean()->default(true),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Jamais'),
                Tables\Columns\TextColumn::make('created_at')->label(__('app.admin.registered'))
                    ->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(['admin' => __('app.admin.admin_role'), 'agent' => __('app.admin.agent_role'), 'user' => __('app.admin.user_role')]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('app.admin.active'))
                    ->boolean(label: 'Actif', oppositeLabel: 'Inactif'),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Se connecter en tant que')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Se connecter en tant que cet utilisateur ?')
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
                    ->visible(fn ($record) => !$record->is_super_admin && $record->id !== auth()->id()),

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
