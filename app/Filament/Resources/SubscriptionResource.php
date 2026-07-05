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
    protected static ?string $navigationIcon  = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Abonnements';
    protected static ?string $modelLabel      = 'Abonnement';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int    $navigationSort  = 21;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('business_id')->label('Entreprise')
                ->relationship('business', 'name')->required(),
            Forms\Components\Select::make('plan')->label('Plan')
                ->options(['free' => 'Gratuit', 'starter' => 'Starter', 'business' => 'Business', 'pro' => 'Pro'])
                ->required(),
            Forms\Components\Select::make('status')->label('Statut')
                ->options(['active' => 'Actif', 'expired' => 'Expiré', 'cancelled' => 'Annulé', 'pending' => 'En attente'])
                ->required(),
            Forms\Components\Select::make('billing_cycle')->label('Cycle')
                ->options(['monthly' => 'Mensuel', 'yearly' => 'Annuel'])->required(),
            Forms\Components\DateTimePicker::make('starts_at')->label('Début'),
            Forms\Components\DateTimePicker::make('ends_at')->label('Fin'),
            Forms\Components\TextInput::make('amount_paid')->label('Montant payé')->numeric(),
            Forms\Components\TextInput::make('currency')->label('Devise')->default('XAF'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business.name')->label('Entreprise')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('plan')->label('Plan')
                    ->colors(['gray' => 'free', 'warning' => 'starter', 'primary' => 'business', 'success' => 'pro']),
                Tables\Columns\BadgeColumn::make('status')->label('Statut')
                    ->colors(['success' => 'active', 'danger' => 'expired', 'gray' => 'cancelled', 'warning' => 'pending']),
                Tables\Columns\TextColumn::make('billing_cycle')->label('Cycle')
                    ->formatStateUsing(fn($s) => $s === 'yearly' ? 'Annuel' : 'Mensuel'),
                Tables\Columns\TextColumn::make('ends_at')->label('Expire le')
                    ->dateTime('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('amount_paid')->label('Montant')
                    ->formatStateUsing(fn($s, $r) => number_format($s ?? 0, 0, ',', ' ') . ' ' . $r->currency),
                Tables\Columns\TextColumn::make('created_at')->label('Créé le')
                    ->dateTime('d/m/Y')->sortable()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->options(['free' => 'Gratuit', 'starter' => 'Starter', 'business' => 'Business', 'pro' => 'Pro']),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Actif', 'expired' => 'Expiré', 'cancelled' => 'Annulé']),
            ])
            ->actions([
                Tables\Actions\Action::make('extend')
                    ->label('+30 jours')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn(Subscription $r) => $r->status === 'active')
                    ->action(function (Subscription $record) {
                        $record->update([
                            'ends_at' => ($record->ends_at ?? now())->addDays(30),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Abonnement étendu de 30 jours')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
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
