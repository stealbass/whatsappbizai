<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PlatformRevenueChart extends ChartWidget
{
    protected static ?string $heading = null;
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return '📈 Revenus mensuels (plateforme)';
    }

    protected function getData(): array
    {
        $months  = [];
        $revenue = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');

            $revenue[] = Payment::where('status', 'verified')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenu vérifié',
                    'data'            => $revenue,
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
