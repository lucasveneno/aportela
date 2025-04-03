<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class UsersDemandDistributionPieChart  extends ChartWidget
{
    protected static ?string $heading = 'Demand Distribution by User';
    protected static ?string $maxHeight = '400px';
    protected static ?string $pollingInterval = null;
    protected int|string|array $columnSpan = 'md';

    /**
     * Determine if the widget should be visible
     */
    public static function canView(): bool
    {
        return Auth::user()->isAdmin();
    }

    protected function getData(): array
    {
        $usersWithDemands = User::query()
            ->whereHas('demands') // Only users with demands
            ->withCount('demands')
            ->orderByDesc('demands_count')
            ->get();

        $colors = [
            '#6366f1',
            '#ec4899',
            '#10b981',
            '#f59e0b',
            '#ef4444',
            '#8b5cf6',
            '#3b82f6',
            '#14b8a6',
            '#f97316',
            '#64748b'
        ];

        $data = [
            'datasets' => [
                [
                    'label' => 'Demands by User',
                    'data' => $usersWithDemands->pluck('demands_count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $usersWithDemands->count()),
                    'borderColor' => '#fff',
                    'borderWidth' => 1,
                    'hoverOffset' => 10,
                ],
            ],
            'labels' => $usersWithDemands->pluck('name')->toArray(),
        ];

        return $data;
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => function ($context) {
                            $total = array_sum($context->dataset->data);
                            $percentage = round(($context->raw / $total) * 100, 1);
                            return "{$context->label}: {$context->raw} ({$percentage}%)";
                        }
                    ]
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
