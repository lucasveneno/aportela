<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DemandResource\Pages;
use App\Filament\Resources\DemandResource\RelationManagers;
use App\Models\Area;
use App\Models\Demand;
use App\Models\User;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DemandResource extends Resource
{
    protected static ?string $model = Demand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /*Select::make('user_id')
                    ->options(User::all()->where('role', 'assesor')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->label(__('User')),*/
                Select::make('area_id')
                    ->options(Area::all()->where('status', 1)->pluck('name', 'id'))
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->label(__('Area')),
                Textarea::make('description')->required(),
                Toggle::make('requires_councilor'),
                Select::make('status')->options([
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'resolved' => 'Resolved',
                ])->default('pending'),


                FileUpload::make('files')
                    ->multiple()
                    ->disk('public') // The disk where files will be stored
                    ->directory('demand_files'), // Directory within the disk
                //->visibility('public'), // If you're using public visibility

                /*Map::make('location')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);
                    }),
                    */

                Geocomplete::make('full_address')
                    ->isLocation()
                    ->countries(['br']) // restrict autocomplete results to these countries
                    //->debug() // output the results of reverse geocoding in the browser console, useful for figuring out symbol formats
                    ->updateLatLng() // update the lat/lng fields on your form when a Place is selected
                    ->maxLength(1024)
                    //->prefix('Choose:')
                    ->placeholder('Start typing an address ...')
                    ->geolocate() // add a suffix button which requests and reverse geocodes the device location
                    ->geolocateIcon('heroicon-o-map'), // override the default icon for the geolocate button

                /*Map::make('location')
                    ->mapControls([
                        'mapTypeControl'    => true,
                        'scaleControl'      => true,
                        'streetViewControl' => false,
                        'rotateControl'     => true,
                        'fullscreenControl' => true,
                        'searchBoxControl'  => false, // creates geocomplete field inside map
                        'zoomControl'       => true,
                    ])
                    //->height(fn() => '400px') // map height (width is controlled by Filament options)
                    ->defaultZoom(5) // default zoom level when opening form
                    ->autocomplete('full_address') // field on form to use as Places geocompletion field
                    ->placeUpdatedUsing(function (callable $set, array $place) {
                        // do whatever you need with the $place results, and $set your field(s)
                        $set('full_address', $place);
                    })

                    ->autocompleteReverse(true) // reverse geocode marker location to autocomplete field
                    ->reverseGeocode([
                        'street' => '%n %S',
                        'city' => '%L',
                        'state' => '%A1',
                        'zip' => '%z',
                    ]) // reverse geocode marker location to form fields, see notes below
                    ->debug() // prints reverse geocode format strings to the debug console
                    //->defaultLocation([39.526610, -107.727261]) // default for new forms
                    ->draggable() // allow dragging to move marker
                    ->clickable(false) // allow clicking to move marker
                    ->geolocate() // adds a button to request device location and set map marker accordingly
                    ->geolocateLabel('Get Location') // overrides the default label for geolocate button
                    ->geolocateOnLoad(true, false) // geolocate on load, second arg 'always' (default false, only for new form))
                    ->layers([
                        'https://googlearchive.github.io/js-v2-samples/ggeoxml/cta.kml',
                    ]) // array of KML layer URLs to add to the map
                    ->geoJson('https://fgm.test/storage/AGEBS01.geojson') // GeoJSON file, URL or JSON
                    ->geoJsonContainsField('geojson'), // field to capture GeoJSON polygon(s) which contain the map marker
*/
                //TextColumn::make('full_address')->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\IconColumn::make('requires_councilor')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDemands::route('/'),
            'create' => Pages\CreateDemand::route('/create'),
            'edit' => Pages\EditDemand::route('/{record}/edit'),
        ];
    }
}
