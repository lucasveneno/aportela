<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Area;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('area_id')
                    ->options(Area::all()->where('status', 1)->pluck('name', 'id'))
                    ->searchable()
                    //->multiple()
                    ->preload()
                    ->label(__('resources.categories.area')),
                TextInput::make('name')->label(__('resources.categories.name')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable(),


                TextColumn::make('area_id')
                    ->label('Area')
                    ->formatStateUsing(fn($state): string => Area::find($state)?->name ?? 'N/A'),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }



    public static function getPluralModelLabel(): string
    {
        return __('resources.categories.plural_label'); // Plural label
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.categories.label'); // Navigation menu label
    }

    protected static function getContentTabLabel(): string
    {
        return 'Cadastrar Nova Área'; // Your custom title
    }
}
