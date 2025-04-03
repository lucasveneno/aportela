<?php

namespace App\Filament\Resources\DemandResource\Pages;

use App\Filament\Resources\DemandResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDemand extends ViewRecord
{
    protected static string $resource = DemandResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->action(function () {
                    $this->dispatch('print-page');
                }),
            Actions\EditAction::make(), // Keep existing edit action
        ];
    }

    // Optional: Customize the view to add print-specific styling
    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}
