<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBusinesses  = Business::count();
        $activeBusinesses = Business::where('is_active', true)->count();
        $totalUsers       = User::count();
        $totalRevenue     = Payment::where('status', 'verified')->sum('amount');
        $activeSubs       = Subscription::where('status', 'active')
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->count();
        $sandboxCount     = Business::where('sandbox_mode', true)->count();

        return [
            Stat::make(__('app.admin.businesses'), $totalBusinesses)
                ->description($activeBusinesses . ' actives')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),

            Stat::make(__('app.admin.users'), $totalUsers)
                ->description('Utilisateurs enregistrés')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Revenu total', number_format($totalRevenue, 0, ',', ' ') . ' XAF')
                ->description('Paiements vérifiés')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Abonnements actifs', $activeSubs)
                ->description('Plans payants en cours')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('info'),

            Stat::make('Sandbox', $sandboxCount)
                ->description('Businesses en mode test')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('warning'),
        ];
    }
}
