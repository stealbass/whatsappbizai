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
    protected static ?string $slug = 'users';

    public static function getModelLabel(): string
    {
        return __('app.super_admin.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.super_admin.users');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('app.super_admin.personal_info'))
                ->icon('heroicon-o-user')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('app.super_admin.name'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label(__('app.super_admin.email'))
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('password')
                        ->label(__('app.super_admin.password'))
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->dehydrateStateUsing(fn ($state) => \Hash::make($state))
                        ->required(fn ($context) => $context === 'create')
                        ->maxLength(255),
                ])->columns(2),

            Forms\Components\Section::make(__('app.super_admin.access_rights'))
                ->icon('heroicon-o-key')
                ->schema([
                    Forms\Components\Select::make('role')
                        ->label(__('app.super_admin.role'))
                        ->options([
                            'admin' => __('app.super_admin.admin_role'),
                            'agent' => __('app.super_admin.agent_role'),
                            'user'  => __('app.super_admin.user_role'),
                        ])
                        ->required()
                        ->default('user'),

                    Forms\Components\Toggle::make('is_super_admin')
                        ->label(__('app.super_admin.super_admin_label'))
                        ->default(false),

                    Forms\Components\Toggle::make('is_active')
                        ->label(__('app.super_admin.active_label'))
                        ->default(true),
                ])->columns(3),

            Forms\Components\Section::make(__('app.super_admin.company'))
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Forms\Components\Select::make('business_id')
                        ->label(__('app.super_admin.business'))
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
                    ->label(__('app.super_admin.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('app.super_admin.email'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label(__('app.super_admin.role'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'admin' => __('app.super_admin.admin_role'),
                        'agent' => __('app.super_admin.agent_role'),
                        'user'  => __('app.super_admin.user_role'),
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'admin' => 'danger',
                        'agent' => 'warning',
                        'user'  => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_super_admin')
                    ->label(__('app.super_admin.super_admin_label'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.super_admin.active_label'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('business.name')
                    ->label(__('app.super_admin.business'))
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label(__('app.super_admin.last_login'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder(__('app.super_admin.never')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.super_admin.registered'))
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label(__('app.super_admin.role'))
                    ->options([
                        'admin' => __('app.super_admin.admin_role'),
                        'agent' => __('app.super_admin.agent_role'),
                        'user'  => __('app.super_admin.user_role'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('app.super_admin.status'))
                    ->boolean()
                    ->trueLabel(__('app.super_admin.active_label'))
                    ->falseLabel(__('app.super_admin.inactive_label')),

                Tables\Filters\TernaryFilter::make('is_super_admin')
                    ->label(__('app.super_admin.super_admin_label'))
                    ->boolean()
                    ->trueLabel(__('app.super_admin.yes'))
                    ->falseLabel(__('app.super_admin.no')),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label(__('app.super_admin.impersonate'))
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('app.super_admin.impersonate'))
                    ->modalDescription(fn ($record) => __('app.super_admin.impersonate_confirm') . " {$record->name} ({$record->email}).")
                    ->action(fn ($record) => redirect(url("impersonate/{$record->id}?save_current=true")))
                    ->visible(fn ($record) => !$record->is_super_admin),

                Tables\Actions\Action::make('toggle_active')
                    ->label(fn ($record) => $record->is_active ? __('app.super_admin.deactivated') : __('app.super_admin.activated'))
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_active' => !$record->is_active]);

                        Notification::make()
                            ->title($record->is_active ? __('app.super_admin.activated') : __('app.super_admin.deactivated'))
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
