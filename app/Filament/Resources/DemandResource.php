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
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
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
                Section::make([
                    Placeholder::make('Critérios para Definição de Prioridades:')
                        ->content('✔ Impacto na população (saúde, segurança, mobilidade).')
                        ->content('✔ Impacto na população (saúde, segurança, mobilidade).'),
                    Placeholder::make('')->content('✔ Impacto na população (saúde, segurança, mobilidade).'),
                    Placeholder::make('')->content('✔ Impacto na população (saúde, segurança, mobilidade).'),
                ]),
                Section::make('Critérios para Definição de Prioridades:')
                    ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([

                        Radio::make('priority')
                            ->options([
                                'max' => 'Prioridade Máxima ',
                                'high' => 'Prioridade Alta ',
                                'medium' => 'Prioridade Média',
                                'low' => 'Prioridade Baixa '
                            ])
                            ->descriptions([
                                'max' => 'Urgente - Ação Imediata',
                                'high' => 'Importante - Planejamento Rápido',
                                'medium' => 'Necessária - Médio Prazo',
                                'low' => 'Melhoria - Longo Prazo'
                            ]),
                    ]),





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


                Fieldset::make('')
                    ->schema([
                        Geocomplete::make('full_address')
                            //->isLocation()
                            ->countries(['br']) // restrict autocomplete results to these countries
                            //->debug() // output the results of reverse geocoding in the browser console, useful for figuring out symbol formats
                            ->updateLatLng() // update the lat/lng fields on your form when a Place is selected
                            ->maxLength(1024)
                            //->prefix('Choose:')
                            ->placeholder('Start typing an address ...')
                            ->geolocate() // add a suffix button which requests and reverse geocodes the device location
                            ->geolocateIcon('heroicon-o-map'), // override the default icon for the geolocate button
                        //->geocodeOnLoad(), // server side geocode of lat/lng to address when form is loaded


                        TextInput::make('latitude')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('location', [
                                    'lat' => floatVal($state),
                                    'lng' => floatVal($get('longitude')),
                                ]);
                            })
                            ->lazy(), // important to use lazy, to avoid updates as you type
                        TextInput::make('longitude')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('location', [
                                    'lat' => floatval($get('latitude')),
                                    'lng' => floatVal($state),
                                ]);
                            })
                            ->lazy(), // important to use lazy, to avoid updates as you type

                        Map::make('location')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            }),
                    ]),



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
