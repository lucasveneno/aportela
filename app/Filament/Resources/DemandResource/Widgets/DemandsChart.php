<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DemandsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

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
                ->reactive(),
        ];
    }

    protected function getData(): array
    {
        // Get the selected filter value
        $timePeriod = $this->filters['time_period'] ?? 'month';

        // Determine date range based on filter
        [$startDate, $endDate] = match ($timePeriod) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->subWeek()->startOfDay(), now()->endOfDay()],
            'month' => [now()->subMonth()->startOfDay(), now()->endOfDay()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [Demand::oldest()->value('created_at'), now()->endOfDay()],
        };

        // Base query with user restriction for non-admins
        $query = Demand::query();
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        // Get trend data
        $data = Trend::query($query)
            ->between(start: $startDate, end: $endDate)
            ->perMonth() // or perDay()/perWeek() based on your needs
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Demands',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
