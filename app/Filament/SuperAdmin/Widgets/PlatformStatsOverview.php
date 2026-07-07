<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Business;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalBusinesses = Business::count();
        $activeBusinesses = Business::where('is_active', true)->count();
        $totalUsers = User::count();
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->count();

        // MRR
        $mrr = Subscription::where('status', 'active')
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->where('billing_cycle', 'monthly')
            ->sum('amount_paid');

        $yearlySubscriptions = Subscription::where('status', 'active')
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->where('billing_cycle', 'yearly')
            ->sum('amount_paid');

        $mrr += $yearlySubscriptions / 12;

        // Pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');

        // Revenue this month
        $monthlyRevenue = Payment::where('status', 'verified')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Expiring soon (7 days)
        $expiringSoon = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [now(), now()->addDays(7)])
            ->count();

        return [
            Stat::make('MRR (Revenu mensuel)', number_format($mrr, 0, ',', ' ') . ' XAF')
                ->description('Revenu récurrent mensuel')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->chart([7, 5, 8, 4, 6, 3, 9, 2, 7, 5, 8, 4]),

            Stat::make('Entreprises actives', $activeBusinesses)
                ->description("sur {$totalBusinesses} total")
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('info'),

            Stat::make('Abonnements actifs', $activeSubscriptions)
                ->description('Plans en cours')
                ->descriptionIcon('heroicon-o-receipt-percent')
                ->color('primary'),

            Stat::make('Utilisateurs totaux', $totalUsers)
                ->description('Inscrits sur la plateforme')
                ->descriptionIcon('heroicon-o-users')
                ->color('warning'),

            Stat::make('Paiements en attente', $pendingPayments)
                ->description(number_format($pendingAmount, 0, ',', ' ') . ' XAF')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),

            Stat::make('Revenu ce mois', number_format($monthlyRevenue, 0, ',', ' ') . ' XAF')
                ->description('Paiements vérifiés')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Expirent bientôt', $expiringSoon)
                ->description('Dans les 7 prochains jours')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($expiringSoon > 0 ? 'danger' : 'success'),
        ];
    }
}
