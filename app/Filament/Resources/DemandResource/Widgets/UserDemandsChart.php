<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use Filament\Widgets\ChartWidget;

class UserDemandsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
