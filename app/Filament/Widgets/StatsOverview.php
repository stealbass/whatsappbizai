<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Message;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue   = Invoice::where('status', 'paid')->sum('total');
        $pendingRevenue = Invoice::whereIn('status', ['sent', 'overdue'])->sum('total');
        $openConvs      = Conversation::where('status', 'open')->count();
        $newContacts    = Contact::whereDate('created_at', '>=', now()->subDays(7))->count();
        $todayMessages  = Message::whereDate('created_at', today())->count();
        $overdueInv     = Invoice::where('status', 'sent')
                                 ->where('due_date', '<', now())
                                 ->count();

        return [
            Stat::make('Revenus encaissés', number_format($totalRevenue, 0, ',', ' ') . ' XAF')
                ->description('Total factures payées')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('En attente de paiement', number_format($pendingRevenue, 0, ',', ' ') . ' XAF')
                ->description($overdueInv . ' facture(s) en retard')
                ->descriptionIcon('heroicon-m-clock')
                ->color($overdueInv > 0 ? 'danger' : 'warning'),

            Stat::make('Conversations ouvertes', $openConvs)
                ->description('Clients en attente de réponse')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            Stat::make('Nouveaux contacts (7j)', $newContacts)
                ->description($todayMessages . ' messages reçus aujourd\'hui')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary'),
        ];
    }
}
