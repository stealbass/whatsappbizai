<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading    = '📈 ' . __('app.admin.monthly_revenue');
    protected static ?int    $sort       = 3;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $months  = [];
        $revenue = [];
        $paid    = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');

            $businessId = auth()->user()?->business_id;

            $revenue[] = Invoice::when($businessId, fn($q) => $q->where('business_id', $businessId))
                ->whereYear('issue_date', $date->year)
                ->whereMonth('issue_date', $date->month)
                ->whereNotIn('status', ['draft', 'cancelled'])
                ->sum('total');

            $paid[] = Invoice::when($businessId, fn($q) => $q->where('business_id', $businessId))
                ->where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('paid_amount');
        }

        return [
            'datasets' => [
                [
                    'label'           => __('app.admin.billed'),
                    'data'            => $revenue,
                    'borderColor'     => '#0ea5e9',
                    'backgroundColor' => 'rgba(14,165,233,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => __('app.admin.collected'),
                    'data'            => $paid,
                    'borderColor'     => '#22c55e',
                    'backgroundColor' => 'rgba(34,197,94,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
