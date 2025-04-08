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
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class DemandResource extends Resource
{
    protected static ?string $model = Demand::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make([
                    TextInput::make('demand_code')
                        ->label('Código da Demanda')
                        ->default('DEM-' . date('Ymd') . '-' .  Str::upper(Str::random(8)))
                        ->disabled()
                        ->dehydrated()
                        ->unique(ignoreRecord: true)
                        ->helperText('Código gerado automaticamente'),
                ]),

                Section::make(__('resources.demands.classify_demand'))
                    //->description('Selecione a priority desta demanda.')
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

                Section::make([
                    /*ToggleButtons::make('requires_councilor')
                            ->label(__('resources.demands.requires_councilor_on_site'))
                            ->options([
                                0 => 'Não',
                                1 => 'Sim, mas não é urgente',
                                2 => 'sim, urgentemente'
                            ])->default(0)->inline(),
                        */

                    Section::make(__('resources.demands.section_applicant_title'))
                        ->description(__('resources.demands.section_applicant_description'))
                        ->schema([

                            ToggleButtons::make('applicant_demand_origin')
                                ->label(__('resources.demands.applicant_demand_origin'))
                                ->boolean()
                                ->default(0)
                                ->inline()
                                ->live(), // Makes it update in real-time

                            Section::make([
                                Repeater::make('applicants')
                                    ->label(__('resources.demands.applicant'))
                                    ->schema([
                                        TextInput::make('applicant_name')->label(__('resources.demands.applicant_name'))->required(),
                                        Select::make('applicant_role')
                                            ->label(__('resources.demands.applicant_role'))
                                            ->searchable()
                                            ->options([
                                                'leadership' => 'Leadership',
                                                'citizen' => 'Citizen',
                                                'government' => 'Government Employee',
                                                'other' => 'Other'
                                            ]),


                                        TextInput::make('applicant_cpf')->label(__('resources.demands.applicant_cpf'))->required(),
                                        TextInput::make('applicant_full_address')->label(__('resources.demands.applicant_full_address'))->required(),
                                        TextInput::make('applicant_phone')->label(__('resources.demands.applicant_phone'))->required(),
                                        TextInput::make('applicant_email')->label(__('resources.demands.applicant_email'))->required(),
                                        TextInput::make('applicant_instagram')->label(__('resources.demands.applicant_instagram'))->required(),
                                        TextInput::make('applicant_facebook')->label(__('resources.demands.applicant_facebook'))->required(),

                                        /*Select::make('user_id')  // Store the user ID
                                            ->label('User')
                                            ->options(User::query()->pluck('name', 'id'))  // Get all users as [id => name]
                                            ->searchable()  // Allow searching through users
                                            ->required(),*/



                                    ])
                                    ->columns(2)
                                    ->columnSpan('full'),

                            ])
                                ->columns(1)
                                ->visible(fn(Get $get): bool => $get('applicant_demand_origin')), // Shows only when toggle is on,
                        ]),

                ]),


                Section::make(__('resources.demands.section_priority_title'))
                    ->description(__('resources.demands.section_priority_description'))
                    ->schema([

                        Section::make([
                            CheckboxList::make('prioritization_criteria')
                                ->label('Critérios de Priorização')
                                ->options([
                                    'impacto_populacao' => 'Impacto na população (saúde, segurança, mobilidade...)',
                                    'risco_acidentes' => 'Risco de acidentes ou danos materiais',
                                    'custo_beneficio' => 'Custo-benefício (recursos x benefício)',
                                    'demanda_popular' => 'Demanda popular (reclamações frequentes)',
                                    'alinhamento_metas' => 'Alinhamento com metas municipais',
                                    'viabilidade_tecnica' => 'Viabilidade técnica de implementação',
                                ])
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('priority', self::calcularpriority($state));
                                    //$set('priority_description', self::descricaopriority($state));
                                }), //->columns(2),

                        ]),
                        Section::make([
                            Hidden::make('priority')
                                ->default(__('resources.demands.low')),
                            TextInput::make('priority')
                                ->label('Nível de priority')
                                ->disabled()
                                ->dehydrated(),
                            Placeholder::make('priority_description')
                                ->label('Justificativa')
                                ->content(fn($get) => self::descricaopriority($get('prioritization_criteria') ?? [])),
                        ]),

                    ]),


                Section::make(__('resources.demands.section_location_title'))
                    ->description(__('resources.demands.section_location_description'))

                    ->schema([
                        Geocomplete::make('full_address')
                            //->isLocation()
                            ->countries(['br']) // restrict autocomplete results to these countries
                            //->debug() // output the results of reverse geocoding in the browser console, useful for figuring out symbol formats
                            ->updateLatLng() // update the lat/lng fields on your form when a Place is selected
                            ->maxLength(1024)
                            //->prefix('Choose:')
                            ->placeholder('Start typing an address ...')
                            ->geolocate(fn(?string $context) => in_array($context, ['create', 'edit'])) // add a suffix button which requests and reverse geocodes the device location
                            ->geolocateIcon('heroicon-o-map-pin'), // override the default icon for the geolocate button
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
                            ->mapControls([
                                'mapTypeControl'    => false,
                                'scaleControl'      => false,
                                'streetViewControl' => false,
                                'rotateControl'     => false,
                                'fullscreenControl' => true,
                                'searchBoxControl'  => false, // creates geocomplete field inside map
                                'zoomControl'       => true,
                            ])
                            ->defaultZoom(15) // default zoom level when opening form
                            ->autocomplete('full_address') // field on form to use as Places geocompletion field
                            ->autocompleteReverse(true) // reverse geocode marker location to autocomplete field
                            ->reactive()
                            ->draggable(fn(?string $context) => in_array($context, ['create', 'edit'])) // Draggable in create/edit and allow dragging to move marker
                            ->defaultLocation(['-20.4648517', '-54.6218477'])
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            }),
                    ])->columns(1),


                Section::make(__('resources.demands.section_files_title'))
                    ->description(__('resources.demands.section_files_description'))
                    ->schema([
                        FileUpload::make('files')
                            ->label('')
                            ->multiple()
                            ->disk('public') // The disk where files will be stored
                            ->directory('demand_files'), // Directory within the disk
                    ])->columns(1),
                Section::make([
                    ToggleButtons::make('requires_councilor')
                        ->label(__('resources.demands.requires_councilor_on_site'))
                        ->boolean()
                        ->default(0)
                        ->inline(),


                ]),

                Section::make(__('resources.demands.draft'))
                    ->description(__('resources.demands.draft_description'))
                    ->schema([

                        ToggleButtons::make('draft')
                            ->label(__('resources.demands.draft_input'))
                            ->boolean()
                            ->default(0)
                            ->inline(), // Makes it update in real-time


                    ])->visible(fn(string $context): bool => in_array($context, ['create', 'edit'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('demand_code')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('user_id')
                    ->label('User')
                    ->formatStateUsing(function ($state) {
                        $user = User::find($state);
                        return $user ? $user->name : 'N/A';
                    }),

                TextColumn::make('area_id')
                    ->label(__('resources.categories.area'))
                    ->formatStateUsing(fn($state): string => Area::find($state)?->name ?? 'N/A')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'published' => 'success',  // Green
                        'reviewing' => 'warning',  // Yellow/Orange
                        'draft'     => 'danger',  // Red
                        default     => 'gray',     // Fallback
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'published' => 'heroicon-o-check-circle',
                        'reviewing' => 'heroicon-o-clock',
                        'draft'     => 'heroicon-o-pencil',
                        default     => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                //IconColumn::make('draft')->boolean(),
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
            // Conditionally allow row click only for drafts
            /*->recordUrl(function ($record) {
                if ($record->draft) {
                    return static::getUrl('edit', ['record' => $record]);
                }
                return null;
            })*/
            ->recordUrl(fn($record) => $record->draft ? static::getUrl('edit', ['record' => $record]) : null)
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn($record) => $record->draft),
                Tables\Actions\ViewAction::make()->visible(fn($record) => !$record->draft), // Uses the ViewDemand page by default

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
            'view' => Pages\ViewDemand::route('/{record}/view'),
        ];
    }



    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->isAdmin()) {
            // Admins see:
            // 1. All non-draft posts from any user (draft = false)
            // 2. All posts (draft or not) if they belong to the current admin
            return $query->where(function ($query) {
                $query->where('draft', false)
                    ->orWhere('user_id', auth()->id());
            });
        }
        // Non-admin can only see their own posts (no draft filter)
        return $query->where('user_id', auth()->id());
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

    private static function calcularPontuacao(array $prioritization_criteria): int
    {
        $pesos = [
            // Critérios Essenciais (alto impacto)
            'impacto_populacao' => 5,       // Saúde, segurança e bem-estar público
            'risco_acidentes' => 5,         // Prevenção de perdas humanas/materiais

            // Critérios Estratégicos
            'alinhamento_metas' => 4,       // priority governamental/ODS
            'custo_beneficio' => 3,         // Eficiência de recursos

            // Critérios Operacionais
            'viabilidade_tecnica' => 3,     // Complexidade de implementação
            'demanda_popular' => 2,         // Pressão social/reclamações

            // Novos critérios sugeridos (exemplo)
            'prazo_legal' => 4,             // Obrigações legais (se aplicável)
            'retorno_economico' => 2,       // Geração de empregos/atividade
        ];

        return array_reduce(
            $prioritization_criteria,
            fn(int $total, string $criterio) => $total + ($pesos[$criterio] ?? 0),
            0
        );
    }

    public static function calcularpriority(array $prioritization_criteria): string
    {
        $total = count($prioritization_criteria);
        $pontuacao = self::calcularPontuacao($prioritization_criteria); // Método separado para reuso

        return match (true) {
            $pontuacao >= 20 =>  __('resources.demands.max'),
            $pontuacao >= 15 =>  __('resources.demands.high'),
            $pontuacao >= 10 =>  __('resources.demands.medium'),
            default =>  __('resources.demands.low'),
        };
    }

    public static function descricaopriority(array $prioritization_criteria): string
    {
        $total = count($prioritization_criteria);
        $pontuacao = self::calcularPontuacao($prioritization_criteria); // Método separado para reuso

        return match (true) {
            $pontuacao >= 20 =>  __('resources.demands.max_description'),
            $pontuacao >= 15 =>  __('resources.demands.high_description'),
            $pontuacao >= 10 =>  __('resources.demands.medium_description'),
            default =>  __('resources.demands.low_description'),
        };
    }

    /*
Melhorias implementadas:

    Novos Critérios:

        Adicionados alinhamento_metas e viabilidade_tecnica com pesos adequados

    Layout Aprimorado:

        Organização em 3 colunas para melhor visualização

        CheckboxList com 2 colunas para os critérios

    Lógica de Pontuação:

        Pesos revisados (risco de acidentes agora tem peso 5)

        Novas faixas de classificação:

            Crítico (20+ pontos)

            Alta (15-19)

            Média (10-14)

            Baixa (<10)

    Descrições Contextuais:

        Justificativas mais detalhadas para cada nível

        Linguagem mais clara para o usuário final

    Otimizações:

        Método separado para cálculo de pontuação

        Uso de array_reduce para cálculo mais limpo

        Melhor tratamento do estado inicial

    Terminologia:

        Campos renomeados para maior clareza ("Justificativa" em vez de "Descrição")
    */
}
