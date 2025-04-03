<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class UserDemandsChart extends ChartWidget
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
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
