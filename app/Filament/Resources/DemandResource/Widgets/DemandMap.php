<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use Cheesegrits\FilamentGoogleMaps\Actions\GoToAction;
use Cheesegrits\FilamentGoogleMaps\Actions\RadiusAction;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class DemandMap extends MapTableWidget
{
	protected static ?string $heading = 'Demand Map';

	protected static ?int $sort = 1;

	protected static ?string $pollingInterval = null;

	protected static ?bool $clustering = true;

	protected static ?string $mapId = 'incidents';

	protected int|string|array $columnSpan = 'full';

	protected function getTableQuery(): Builder
	{
		$query = Demand::query();
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

		return $query->latest();
		
		//return \App\Models\Demand::query()->latest();
	}

	protected function getTableColumns(): array
	{
		return [
			TextColumn::make('latitude'),
			TextColumn::make('longitude'),
			MapColumn::make('location')
				->extraImgAttributes(
					fn ($record): array => ['title' => $record->latitude . ',' . $record->longitude]
				)
				->height('150')
				->width('250')
				->type('hybrid')
				->zoom(15),
		];
	}

	protected function getTableFilters(): array
	{
		return [
			RadiusFilter::make('location')
				->section('Radius Filter')
				->selectUnit(),
            MapIsFilter::make('map'),
		];
	}

	protected function getTableActions(): array
	{
		return [
			Tables\Actions\ViewAction::make(),
			Tables\Actions\EditAction::make(),
			GoToAction::make()
				->zoom(14),
			RadiusAction::make(),
		];
	}

	protected function getData(): array
	{
		$locations = $this->getRecords();

		$data = [];

		foreach ($locations as $location)
		{
			$data[] = [
				'location' => [
					'lat' => $location->latitude ? round(floatval($location->latitude), static::$precision) : 0,
                    'lng' => $location->longitude ? round(floatval($location->longitude), static::$precision) : 0,
				],
                'id'      => $location->id,
			];
		}

		return $data;
	}
}
