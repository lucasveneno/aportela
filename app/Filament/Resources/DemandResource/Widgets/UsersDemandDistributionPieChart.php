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
    protected int|string|array $columnSpan = 'lg';

    public ?string $filter = 'month'; // Global filter property

    protected function getFilters(): array
    {
        return [
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'This Year',
            'all' => 'All Time',
        ];
    }

    /**
     * Determine if the widget should be visible
     */
    public static function canView(): bool
    {
        return Auth::user()->isAdmin();
    }

    protected function getData(): array
    {
        // Get date range based on filter
        [$startDate, $endDate] = $this->getDateRange();

        $usersWithDemands = User::query()
            ->whereHas('demands', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->withCount(['demands' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
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

    protected function getDateRange(): array
    {
        return match ($this->filter) {
            'week' => [
                now()->subWeek()->startOfDay(),
                now()->endOfDay()
            ],
            'month' => [
                now()->subMonth()->startOfDay(),
                now()->endOfDay()
            ],
            'year' => [
                now()->startOfYear(),
                now()->endOfYear()
            ],
            default => [
                Demand::oldest()->value('created_at') ?? now()->subYear(),
                now()->endOfDay()
            ],
        };
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
                    'labels' => [
                        'boxWidth' => 12,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => function ($tooltipItem) {
                            $label = $tooltipItem->label;
                            $value = $tooltipItem->raw;
                            $percentage = round(($value / $this->totalDemands) * 100, 1);
                            return "{$label}: {$value} ({$percentage}%)";
                        },
                        'afterLabel' => function ($tooltipItem) {
                            return "Total: {$this->totalDemands}";
                        }
                    ]
                ],
            ],
            'cutout' => '60%',
        ];
    }

    public function getHeading(): string
    {
        $period = $this->filter ?? 'month';
        return 'Demand Distribution (' . match ($period) {
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'This Year',
            'all' => 'All Time',
        } . ')';
    }
}
