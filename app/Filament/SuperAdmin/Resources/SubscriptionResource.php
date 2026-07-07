<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\SubscriptionResource\Pages;
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
    protected static ?string $modelLabel = 'Abonnement';
    protected static ?string $modelLabelPlural = 'Abonnements';
    protected static ?string $slug = 'subscriptions';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Détails de l\'abonnement')
                ->icon('heroicon-o-document-text')
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
                            'active'   => 'Actif',
                            'expired'  => 'Expiré',
                            'cancelled' => 'Annulé',
                            'pending'  => 'En attente',
                        ])
                        ->required()
                        ->default('pending'),

                    Forms\Components\Select::make('billing_cycle')
                        ->label('Cycle de facturation')
                        ->options([
                            'monthly' => 'Mensuel',
                            'yearly'  => 'Annuel',
                        ])
                        ->required()
                        ->default('monthly'),
                ])->columns(2),

            Forms\Components\Section::make('Dates')
                ->icon('heroicon-o-calendar')
                ->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Date de début')
                        ->required(),

                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('Date de fin')
                        ->helperText('Laisser vide pour les plans sans expiration'),
                ])->columns(2),

            Forms\Components\Section::make('Paiement')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\TextInput::make('amount_paid')
                        ->label('Montant payé')
                        ->numeric()
                        ->default(0)
                        ->suffix('XAF'),

                    Forms\Components\TextInput::make('currency')
                        ->label('Devise')
                        ->default('XAF')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('flutterwave_tx_ref')
                        ->label('Référence Flutterwave')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('flutterwave_tx_id')
                        ->label('Transaction ID Flutterwave')
                        ->maxLength(255),
                ])->columns(3),
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
                    ->money($record?->currency ?? 'XAF')
                    ->sortable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->ends_at && $record->ends_at->isPast() ? 'danger' : null),

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

                Tables\Filters\SelectFilter::make('billing_cycle')
                    ->label('Cycle')
                    ->options([
                        'monthly' => 'Mensuel',
                        'yearly'  => 'Annuel',
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

    public static function getRelations(): array
    {
        return [];
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
