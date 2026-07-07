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
            Stat::make(__('app.admin.total_revenue'), number_format($totalRevenue, 0, ',', ' ') . ' XAF')
                ->description(__('app.admin.total_revenue_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('app.admin.pending_payments'), number_format($pendingRevenue, 0, ',', ' ') . ' XAF')
                ->description($overdueInv . ' ' . __('app.admin.pending_payments_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($overdueInv > 0 ? 'danger' : 'warning'),

            Stat::make(__('app.admin.open_conversations'), $openConvs)
                ->description(__('app.admin.open_conversations_desc'))
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            Stat::make(__('app.admin.new_contacts'), $newContacts)
                ->description($todayMessages . ' ' . __('app.admin.new_contacts_desc'))
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary'),
        ];
    }
}
