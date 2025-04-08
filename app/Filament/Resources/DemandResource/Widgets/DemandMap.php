<?php

namespace App\Filament\Resources\DemandResource\Widgets;

use App\Models\Demand;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapWidget;

class DemandMap extends MapWidget
{
    protected static ?string $heading = 'Map';

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null;

    protected static ?bool $clustering = true;

    protected static ?bool $fitToBounds = true;

    protected static ?int $zoom = 15;

    protected static ?string $mapId = 'incidents';


    protected int|string|array $columnSpan = 'full';


    protected function getData(): array
    {
        /**
         * You can use whatever query you want here, as long as it produces a set of records with your
         * lat and lng fields in them.
         */
        //$locations = Demand::all();

        $query = Demand::query();

        $query->where('draft', '0');


        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }


        // Get the results (removed ->all() which was causing issues)
        $locations = $query->latest()->get();

        $data = [];

        foreach ($locations as $location) {
            /**
             * Each element in the returned data must be an array
             * containing a 'location' array of 'lat' and 'lng',
             * and a 'label' string (optional but recommended by Google
             * for accessibility.
             *
             * You should also include an 'id' attribute for internal use by this plugin
             */
            $data[] = [
                'location'  => [
                    'lat' => $location->latitude ? round(floatval($location->latitude), static::$precision) : 0,
                    'lng' => $location->longitude ? round(floatval($location->longitude), static::$precision) : 0,
                ],

                //'label'     => 'Código: '.$location->demand_code.' '. $location->latitude . ',' . $location->longitude,


                'id' => $location->getKey(),

                /**
                 * Optionally you can provide custom icons for the map markers,
                 * either as scalable SVG's, or PNG, which doesn't support scaling.
                 * If you don't provide icons, the map will use the standard Google marker pin.
                 */
                //'icon' => [
                //    'url' => url('images/dealership.svg'),
                //    'type' => 'svg',
                //    'scale' => [35, 35],
                //],

                'icon'     => view(
                    'filament-panels::widgets.map-label',
                    [
                        'dealershipId'   => $location->demand_code,
                        'dealershipName' => $location->status,
                        'dealershipIcon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
</svg>
',
                    ]
                )->render(),



            ];
        }

        return $data;
    }
}
