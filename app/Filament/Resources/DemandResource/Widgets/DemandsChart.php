<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DemandsChart extends ChartWidget
{
    protected static ?string $heading = 'Demands Overview';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'month';


    /*protected function getFilters(): ?array
    {
        return [
            Select::make('time_period')
                ->label('Time Period')
                ->options([
                    'today' => 'Today',
                    'week' => 'Last Week',
                    'month' => 'Last Month',
                    'year' => 'This Year',
                    'all' => 'All Time',
                ])
                ->default('month')
                ->live() // This is the key change
                ->afterStateUpdated(function () {
                    if (method_exists($this, 'updateChartData')) {
                        $this->updateChartData();
                    }
                }),
        ];
    }*/

    protected function getFilters(): array
    {
        return [
            'today' => 'Today',
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'This Year',
            'all' => 'All Time',
        ];
    }

    protected function getData(): array
    {

        $timePeriod = $this->filter ?? 'month';

        [$startDate, $endDate, $groupBy] = $this->getDateRangeAndGrouping($timePeriod);

        $query = Demand::query();
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $data = Trend::query($query)
            ->between(start: $startDate, end: $endDate)
            ->{$groupBy}()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Demands',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    //'borderColor' => '#6366f1',
                    //'backgroundColor' => '#6366f1',
                ],
            ],

            'labels' => $data->map(fn(TrendValue $value) => $this->formatLabel($value->date, $groupBy)),
            'hoverOffset'=> 4
        ];
    }

    protected function getDateRangeAndGrouping(string $timePeriod): array
    {
        return match ($timePeriod) {
            'today' => [
                now()->startOfDay(),
                now()->endOfDay(),
                'perHour'
            ],
            'week' => [
                now()->subWeek()->startOfDay(),
                now()->endOfDay(),
                'perDay'
            ],
            'month' => [
                now()->subMonth()->startOfDay(),
                now()->endOfDay(),
                'perDay'
            ],
            'year' => [
                now()->startOfYear(),
                now()->endOfYear(),
                'perMonth'
            ],
            default => [
                Demand::oldest()->value('created_at') ?? now()->subYear()->startOfDay(),
                now()->endOfDay(),
                'perMonth'
            ],
        };
    }


    protected function getDateRange(): array
    {
        return match ($this->filter) {
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay()
            ],
            'week' => [
                'start' => now()->subWeek()->startOfDay(),
                'end' => now()->endOfDay()
            ],
            'month' => [
                'start' => now()->subMonth()->startOfDay(),
                'end' => now()->endOfDay()
            ],
            'year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear()
            ],
            default => [
                'start' => Demand::oldest()->value('created_at') ?? now()->subYear(),
                'end' => now()->endOfDay()
            ],
        };
    }

    protected function formatLabel(string $date, string $groupBy): string
    {
        return match ($groupBy) {
            'perHour' => now()->parse($date)->format('H:i'),
            'perDay' => now()->parse($date)->format('M j'),
            'perMonth' => now()->parse($date)->format('M Y'),
            default => $date,
        };
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }

    public function getHeading(): string
    {
        $period = $this->filter ?? 'month';
        return 'Demands (' . match ($period) {
            'today' => 'Today',
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'This Year',
            'all' => 'All Time',
        } . ')';
    }
}
