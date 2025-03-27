<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DemandResource\Pages;
use App\Filament\Resources\DemandResource\RelationManagers;
use App\Models\Area;
use App\Models\Category;
use App\Models\Demand;
use App\Models\User;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
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
use Illuminate\Support\HtmlString;

class DemandResource extends Resource
{
    protected static ?string $model = Demand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Section::make([
                    Select::make('area_id')
                        ->label(__('Area'))
                        ->options(Area::query()->where('status', 1)->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->live() // This makes the field update the form in real-time
                        ->afterStateUpdated(function ($state, Select $component) {
                            // When area changes, update the category options
                            $component->getContainer()
                                ->getComponent('category_id')
                                ->options(
                                    $state ? Category::where('area_id', $state)
                                        //->where('status', 1)
                                        ->pluck('name', 'id')
                                        : []
                                );
                        }),

                    Select::make('category_id')
                        ->label(__('Category'))
                        ->options(fn(Get $get): array => Category::query()
                            ->where('area_id', $get('area_id'))
                            ->pluck('name', 'id')
                            ->toArray())
                        ->searchable()
                        ->preload()
                        ->key('category_id'), // Important for the afterStateUpdated to find this field


                ])->columns(2),















                Section::make('Priority')
                    ->description('Selecione a prioridade desta demanda.')
                    ->schema([


                        Section::make([
                            Radio::make('priority')
                                ->label('')
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
                        Section::make([
                            Placeholder::make('Critérios para Definição de Prioridades:')
                                ->content(new HtmlString('
                                ✔ Impacto na população (saúde, segurança, mobilidade...).<br />
                                ✔ Risco de acidentes ou danos materiais.<br />
                                ✔ Custo-benefício (recursos disponíveis x benefício gerado).<br />
                                ✔ Demanda popular (reclamações frequentes).
                                ')),
                        ]),




                    ]),








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

                Toggle::make('status')->label('Salvar como rascunho')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(1),
                Toggle::make('requires_councilor')->label('Requervisitadovereador(a)'),

                Section::make('Priority')
                ->description('Selecione a prioridade desta demanda.')
                ->schema([
                RichEditor::make('description')->required(),
                ])->columns(1),
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
