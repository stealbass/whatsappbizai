<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Abonnements & Paiements';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'subscriptions';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->is_super_admin ?? false;
    }

    public static function getModelLabel(): string
    {
        return 'Abonnement';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Abonnements';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Détails')
                ->schema([
                    Forms\Components\Select::make('business_id')
                        ->label('Entreprise')
                        ->relationship('business', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('plan')
                        ->label('Plan')
                        ->options([
                            'free'     => 'Free',
                            'starter'  => 'Starter',
                            'business' => 'Business',
                            'pro'      => 'Pro',
                        ])
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options([
                            'active'    => 'Actif',
                            'expired'   => 'Expiré',
                            'cancelled' => 'Annulé',
                            'pending'   => 'En attente',
                        ])
                        ->required()
                        ->default('pending'),
                    Forms\Components\Select::make('billing_cycle')
                        ->label('Cycle')
                        ->options([
                            'monthly' => 'Mensuel',
                            'yearly'  => 'Annuel',
                        ])
                        ->required()
                        ->default('monthly'),
                ])->columns(2),

            Forms\Components\Section::make('Dates & Paiement')
                ->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Début')
                        ->required(),
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('Fin'),
                    Forms\Components\TextInput::make('amount_paid')
                        ->label('Montant payé')
                        ->numeric()
                        ->default(0)
                        ->suffix('XAF'),
                    Forms\Components\TextInput::make('currency')
                        ->label('Devise')
                        ->default('XAF')
                        ->maxLength(10),
                ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'free'     => 'gray',
                        'starter'  => 'info',
                        'business' => 'success',
                        'pro'      => 'warning',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'expired'   => 'danger',
                        'cancelled' => 'warning',
                        'pending'   => 'info',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Cycle')
                    ->formatStateUsing(fn ($state) => $state === 'monthly' ? 'Mensuel' : 'Annuel'),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Montant')
                    ->money($record?->currency ?? 'XAF'),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active'    => 'Actif',
                        'expired'   => 'Expiré',
                        'cancelled' => 'Annulé',
                        'pending'   => 'En attente',
                    ]),
                Tables\Filters\SelectFilter::make('plan')
                    ->label('Plan')
                    ->options([
                        'free'     => 'Free',
                        'starter'  => 'Starter',
                        'business' => 'Business',
                        'pro'      => 'Pro',
                    ]),
            ])
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit'   => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
