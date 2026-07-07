<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class MRRChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenus mensuels (MRR)';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'label' => $date->format('M Y'),
                'start' => $date->copy()->startOfMonth(),
                'end'   => $date->copy()->endOfMonth(),
            ]);
        }

        $revenue = $months->map(function ($month) {
            return Payment::where('status', 'verified')
                ->whereBetween('created_at', [$month['start'], $month['end']])
                ->sum('amount');
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenu vérifié',
                    'data'  => $revenue->toArray(),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
