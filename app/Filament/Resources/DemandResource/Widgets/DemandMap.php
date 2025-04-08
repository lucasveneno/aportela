<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Filament\Resources\DemandResource\Pages\EditDemand;
use App\Filament\Resources\DemandResource\Pages\ViewDemand;
use App\Models\Area;
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
use Illuminate\Support\HtmlString;

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

			//TextColumn::make('latitude'),
			//TextColumn::make('longitude'),
			MapColumn::make('location')
				->extraImgAttributes(
					fn($record): array => ['title' => $record->latitude . ',' . $record->longitude]
				)
				->height('150')
				->width('150')
				->type('hybrid')
				->zoom(16),
			TextColumn::make('demand_code'),
			TextColumn::make('area_id')
				->label(__('resources.categories.area'))
				->formatStateUsing(fn($state): string => Area::find($state)?->name ?? 'N/A')
				->sortable(),
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
			//Tables\Actions\ViewAction::make(),
			//Tables\Actions\EditAction::make(),
			Tables\Actions\ViewAction::make()
				->url(fn($record) => ViewDemand::getUrl(['record' => $record])),
			Tables\Actions\EditAction::make()
				->url(fn($record) => EditDemand::getUrl(['record' => $record])),
			GoToAction::make()
				->zoom(17)
				->label('Ver no mapa')
				->extraAttributes(function ($record) {
					$originalAttributes = [
						'x-on:click' => new HtmlString(
							sprintf("\$dispatch('filament-google-maps::widget/setMapCenter', {lat: %f, lng: %f, zoom: %d})",
								round(floatval($record->latitude), 8),
								round(floatval($record->longitude), 8),
								17
							)
						)
					];
					
					return array_merge($originalAttributes, [
						'@click' => <<<JS
							setTimeout(() => {
								const section = document.getElementById('map-section');
								if (section) {
									// First scroll to section
									section.scrollIntoView({
										behavior: 'smooth',
										block: 'start'
									});
									
									// Then highlight it
									section.classList.add('bg-blue-50');
									setTimeout(() => {
										section.classList.remove('bg-blue-50');
									}, 1000);
								}
							}, 300)
						JS
					]);
				}),
			//RadiusAction::make(),
		];
	}

	protected function getData(): array
	{
		$locations = $this->getRecords();

		$data = [];

		foreach ($locations as $location) {
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
