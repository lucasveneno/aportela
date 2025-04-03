<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use Filament\Forms\Components\DatePicker;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DemandsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    public ?string $filter = 'today';

    protected function getFilters(): ?array
    {
        return [
            DatePicker::make('start_date')
                ->label('From')
                ->default(now()->startOfYear()),
            DatePicker::make('end_date')
                ->label('To')
                ->default(now()->endOfYear()),
        ];
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $data = Trend::model(Demand::class)
            ->between(
                //start: now()->startOfYear(),
                //end: now()->endOfYear(),
                start: $filters['start_date'] ?? now()->startOfYear(),
                end: $filters['end_date'] ?? now()->endOfYear(),
            )
            ->perMonth()
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
