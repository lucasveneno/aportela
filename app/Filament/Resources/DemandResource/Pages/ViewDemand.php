<?php

namespace App\Filament\Resources\DemandResource\Pages;

use App\Filament\Resources\DemandResource;
use App\Models\Area;
use App\Models\Category;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDemand extends ViewRecord
{
    protected static string $resource = DemandResource::class;


    protected function getHeaderActions(): array
    {
        return [
            /*Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->action(function () {
                    $this->dispatch('print-page');
                }),*/
            Actions\Action::make('pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return $this->generatePDF();
                }),
            Actions\EditAction::make(), // Keep existing edit action
        ];
    }

    protected function generatePDF()
    {
        $pdf = Pdf::loadHTML(
            view('filament.pdf.demand', [
                'record' => $this->record,
                'title' => $this->record->title,
                'date' => now()->format('Y-m-d'),
                'requester' => User::query()->where('id', $this->record->user_id)->pluck('name')->first(),
                'demand_code' => $this->record->demand_code,
                'area' =>  Area::query()->where('id', $this->record->area_id)->pluck('name')->first(),
                'category' =>  Category::query()->where('id', $this->record->category_id)->pluck('name')->first()
            ])
        );

        // Set PDF options to remove margins
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => -20,
            'margin_left' => 0,
            'padding' => 0,
            'font_height_ratio'=>1
        ]);

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "{$this->record->demand_code}.pdf"
        );
    }

    // Optional: Customize the view to add print-specific styling
    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}
