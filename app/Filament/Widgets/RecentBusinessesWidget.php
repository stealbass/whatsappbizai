<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentBusinessesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = null;

    public function getHeading(): ?string
    {
        return '🏢 Entreprises récentes';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Business::withCount(['users', 'contacts', 'invoices'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Entreprise')
                    ->searchable()
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('owner_name')
                    ->label('Propriétaire')
                    ->color('gray'),
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
                Tables\Columns\IconColumn::make('sandbox_mode')
                    ->label('Sandbox')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('contacts_count')
                    ->label('Contacts')
                    ->counts('contacts')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('invoices_count')
                    ->label('Factures')
                    ->counts('invoices')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Voir')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->url(fn (Business $record) => url("impersonate/{$record->users()->first()?->id}?save_current=true")),
            ])
            ->paginated(false);
    }
}
