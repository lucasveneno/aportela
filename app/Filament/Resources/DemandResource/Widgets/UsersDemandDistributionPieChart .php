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
            ->withCount('demands')
            ->has('demands', '>', 0)
            ->orderByDesc('demands_count')
            ->get();

        $colors = [
            '#6366f1', '#ec4899', '#10b981', '#f59e0b', '#ef4444',
            '#8b5cf6', '#3b82f6', '#14b8a6', '#f97316', '#64748b'
        ];

        $datasets = [
            [
                'label' => 'Demands by User',
                'data' => [],
                'backgroundColor' => [],
                'borderColor' => '#fff',
                'borderWidth' => 1,
                'hoverOffset' => 10,
            ]
        ];

        $labels = [];

        foreach ($usersWithDemands as $index => $user) {
            $datasets[0]['data'][] = $user->demands_count;
            $datasets[0]['backgroundColor'][] = $colors[$index % count($colors)];
            $labels[] = $user->name;
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'boxWidth' => 12,
                        'font' => [
                            'size' => 12,
                        ],
                        'padding' => 12,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'callbacks' => [
                        'label' => function($context) {
                            $label = $context->label ?? '';
                            $value = $context->raw ?? 0;
                            $total = array_sum($context->dataset->data);
                            $percentage = round(($value / $total) * 100, 1);
                            return sprintf("%s: %d (%.1f%%)", $label, $value, $percentage);
                        }
                    ]
                ],
            ],
            'cutout' => '65%',
            'animation' => [
                'animateScale' => true,
                'animateRotate' => true,
            ],
        ];
    }

    /**
     * Get the widget description (optional)
     */
    public static function getDescription(): ?string
    {
        return 'Visual representation of demand distribution among team members (Admin only)';
    }
}
