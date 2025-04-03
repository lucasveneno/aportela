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

    protected static ?string $heading = 'Demands Overview';
    protected static ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = 'full';

    protected function getFilters(): ?array
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
                ->afterStateUpdated(fn() => $this->updateChartData()),
        ];
    }

    protected function getData(): array
    {
        $timePeriod = $this->filters['time_period'] ?? 'month';

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
                    'label' => 'Number of Demands',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#4f46e5',
                    'borderColor' => '#4f46e5',
                    'tension' => 0.1,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $this->formatLabel($value->date, $groupBy)),
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
                    'display' => false,
                ],
            ],
        ];
    }

    public function getHeading(): string
    {
        $period = $this->filters['time_period'] ?? 'month';
        return 'Demands (' . match ($period) {
            'today' => 'Today',
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'This Year',
            'all' => 'All Time',
        } . ')';
    }
}
