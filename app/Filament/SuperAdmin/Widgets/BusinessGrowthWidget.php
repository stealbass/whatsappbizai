<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Business;
use Filament\Widgets\ChartWidget;

class BusinessGrowthWidget extends ChartWidget
{
    protected static ?string $heading = 'Croissance des entreprises';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'label' => $date->format('M Y'),
                'end'   => $date->copy()->endOfMonth(),
            ]);
        }

        $cumulative = $months->map(function ($month) {
            return Business::where('created_at', '<=', $month['end'])->count();
        });

        $newPerMonth = $months->map(function ($month) {
            return Business::whereMonth('created_at', $month['end']->month)
                ->whereYear('created_at', $month['end']->year)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Total entreprises',
                    'data'  => $cumulative->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Nouvelles / mois',
                    'data'  => $newPerMonth->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
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
