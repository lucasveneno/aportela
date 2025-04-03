<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use Filament\Widgets\ChartWidget;

class DemandsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $data = Demand::model(Demand::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Blog posts',
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
