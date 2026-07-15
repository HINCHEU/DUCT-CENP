<?php

namespace App\Filament\Resources\DuctTypes;

use App\Filament\Resources\DuctTypes\Pages\CreateDuctType;
use App\Filament\Resources\DuctTypes\Pages\EditDuctType;
use App\Filament\Resources\DuctTypes\Pages\ListDuctTypes;
use App\Filament\Resources\DuctTypes\Pages\ViewDuctType;
use App\Filament\Resources\DuctTypes\Schemas\DuctTypeForm;
use App\Filament\Resources\DuctTypes\Schemas\DuctTypeInfolist;
use App\Filament\Resources\DuctTypes\Tables\DuctTypesTable;
use App\Models\DuctType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DuctTypeResource extends Resource
{
    protected static ?string $model = DuctType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DuctTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DuctTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DuctTypesTable::configure($table);
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
            'index' => ListDuctTypes::route('/'),
            'create' => CreateDuctType::route('/create'),
            'view' => ViewDuctType::route('/{record}'),
            'edit' => EditDuctType::route('/{record}/edit'),
        ];
    }
}
