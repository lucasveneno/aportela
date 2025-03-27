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
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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


                Section::make(__('resources.demands.classify_demand'))
                    //->description('Selecione a prioridade desta demanda.')
                    ->schema([
                        Select::make('area_id')
                            ->label(__('resources.demands.area'))
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
                            })->required(),

                        Select::make('category_id')
                            ->label(__('resources.demands.category'))
                            ->options(fn(Get $get): array => Category::query()
                                ->where('area_id', $get('area_id'))
                                ->pluck('name', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->key('category_id'), // Important for the afterStateUpdated to find this field


                    ])->columns(2),

                Section::make([
                    RichEditor::make('description')
                        ->toolbarButtons([
                            //'attachFiles',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->label(__('resources.demands.description'))->required(),
                ])->columns(1),

                Section::make(__('resources.demands.section_priority_title'))
                    ->description(__('resources.demands.section_priority_description'))
                    ->schema([
                        Section::make([
                            CheckboxList::make('criterios')
                                ->label('Critérios de Priorização')
                                ->options([
                                    'impacto_populacao' => 'Impacto na população (saúde, segurança,mobilidade, bem-estar)',
                                    'risco_acidentes' => 'Risco de acidentes ou danos materiais',
                                    'custo_beneficio' => 'Custo-benefício (recursos x benefício)',
                                    'demanda_popular' => 'Demanda popular (reclamações frequentes)',
                                    'alinhamento_metas' => 'Alinhamento com metas municipais',
                                    'viabilidade_tecnica' => 'Viabilidade técnica de implementação',
                                ])
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('prioridade', self::calcularPrioridade($state));
                                    //$set('descricao_prioridade', self::descricaoPrioridade($state));
                                }),
                        ])->columnSpanFull(),
                        Hidden::make('prioridade')
                            ->default(__('resources.demands.low')),


                        TextInput::make('prioridade')
                            ->label('Nível de Prioridade')
                            ->disabled()->lazy(),
                        //->dehydrated()
                        //->columnSpan(1),

                        Placeholder::make('descricao_prioridade')
                            ->label('Justificativa')
                            ->content(fn($get) => self::descricaoPrioridade($get('criterios') ?? []))
                            ->columnSpan(2),





                    ]),





                Section::make([
                    FileUpload::make('files')
                        ->multiple()
                        ->disk('public') // The disk where files will be stored
                        ->directory('demand_files'), // Directory within the disk
                ])->columns(1),







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
                Toggle::make('requires_councilor')->label('Representante precisa estar presente no local?'),


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


    public static function getNavigationLabel(): string
    {
        return __('resources.demands.plural_label'); // Navigation menu label

    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.demands.plural_label'); // Navigation menu label
    }

    public static function getModelLabel(): string
    {
        return __('resources.demands.label'); // Navigation menu label

    }

    public static function getNavigationGroup(): string
    {
        return __('resources.demands_group');
    }

    private static function calcularPontuacao(array $criterios): int
    {
        $pesos = [
            // Critérios Essenciais (alto impacto)
            'impacto_populacao' => 5,       // Saúde, segurança e bem-estar público
            'risco_acidentes' => 5,         // Prevenção de perdas humanas/materiais

            // Critérios Estratégicos
            'alinhamento_metas' => 4,       // Prioridade governamental/ODS
            'custo_beneficio' => 3,         // Eficiência de recursos

            // Critérios Operacionais
            'viabilidade_tecnica' => 3,     // Complexidade de implementação
            'demanda_popular' => 2,         // Pressão social/reclamações

            // Novos critérios sugeridos (exemplo)
            'prazo_legal' => 4,             // Obrigações legais (se aplicável)
            'retorno_economico' => 2,       // Geração de empregos/atividade
        ];

        return array_reduce(
            $criterios,
            fn(int $total, string $criterio) => $total + ($pesos[$criterio] ?? 0),
            0
        );
    }

    public static function calcularPrioridade(array $criterios): string
    {
        $total = count($criterios);
        $pontuacao = self::calcularPontuacao($criterios); // Método separado para reuso

        return match (true) {
            $pontuacao >= 20 => $pontuacao . ' - ' . __('resources.demands.max'),
            $pontuacao >= 15 => $pontuacao . ' - ' . __('resources.demands.high'),
            $pontuacao >= 10 => $pontuacao . ' - ' . __('resources.demands.medium'),
            default => $pontuacao . ' - ' . __('resources.demands.low'),
        };
    }

    public static function descricaoPrioridade(array $criterios): string
    {
        $total = count($criterios);
        $pontuacao = self::calcularPontuacao($criterios); // Método separado para reuso

        return match (true) {
            $pontuacao >= 20 => $pontuacao . ' - ' . __('resources.demands.max_description'),
            $pontuacao >= 15 => $pontuacao . ' - ' . __('resources.demands.high_description'),
            $pontuacao >= 10 => $pontuacao . ' - ' . __('resources.demands.medium_description'),
            default => $pontuacao . ' - ' . __('resources.demands.low_description'),
        };
    }
}
