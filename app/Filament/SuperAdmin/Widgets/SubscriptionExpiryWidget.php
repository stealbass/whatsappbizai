<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Subscription;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class SubscriptionExpiryWidget extends TableWidget
{
    protected static ?string $heading = 'Abonnements expirant bientôt';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '400px';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Subscription::query()
                    ->where('status', 'active')
                    ->whereNotNull('ends_at')
                    ->whereBetween('ends_at', [now(), now()->addDays(30)])
                    ->orderBy('ends_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('business.name')
                    ->label('Entreprise')
                    ->searchable(),

                Tables\Columns\TextColumn::make('plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'starter'  => 'info',
                        'business' => 'success',
                        'pro'      => 'warning',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Expire le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->ends_at->diffInDays(now()) <= 3 ? 'danger' : 'warning'),

                Tables\Columns\TextColumn::make('days_left')
                    ->label('Jours restants')
                    ->state(fn ($record) => max(0, now()->diffInDays($record->ends_at)))
                    ->badge()
                    ->color(fn ($state) => $state <= 3 ? 'danger' : ($state <= 7 ? 'warning' : 'info')),

                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Montant')
                    ->money($record?->currency ?? 'XAF'),
            ])
            ->paginated([5, 10, 25]);
    }
}
