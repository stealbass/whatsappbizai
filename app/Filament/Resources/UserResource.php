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
    protected static ?string $navigationLabel = __('app.admin.users');
    protected static ?string $modelLabel = __('app.admin.user');
    protected static ?string $pluralModelLabel = __('app.admin.users');
    protected static ?string $navigationGroup = __('app.admin.nav_administration');
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations personnelles')->schema([
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
                Tables\Columns\TextColumn::make('name')->label(__('app.admin.name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label(__('app.admin.email'))->searchable(),
                Tables\Columns\BadgeColumn::make('role')->label(__('app.admin.role'))
                    ->colors([
                        'danger'  => 'admin',
                        'warning' => 'agent',
                        'gray'    => 'user',
                    ]),
                Tables\Columns\TextColumn::make('business.name')->label(__('app.admin.business'))->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')->label(__('app.admin.verified'))
                    ->dateTime('d/m/Y H:i')->sortable()
                    ->placeholder(__('app.admin.unverified')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.admin.active'))->boolean()->default(true),
                Tables\Columns\TextColumn::make('created_at')->label(__('app.admin.registered'))
                    ->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(['admin' => __('app.admin.admin_role'), 'agent' => __('app.admin.agent_role'), 'user' => __('app.admin.user_role')]),
                Tables\Filters\Filter::make('verified')
                    ->query(fn($q) => $q->whereNotNull('email_verified_at'))
                    ->label(__('app.admin.email_verified')),
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
