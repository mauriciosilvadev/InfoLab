<?php

namespace App\Filament\Resources\Laboratories;

use App\Filament\Resources\Laboratories\Pages\CreateLaboratory;
use App\Filament\Resources\Laboratories\Pages\EditLaboratory;
use App\Filament\Resources\Laboratories\Pages\ListLaboratories;
use App\Filament\Resources\Laboratories\Schemas\LaboratoryForm;
use App\Filament\Resources\Laboratories\Tables\LaboratoriesTable;
use App\Models\Laboratory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LaboratoryResource extends Resource
{
    protected static ?string $model = Laboratory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Laboratórios';

    protected static ?string $modelLabel = 'laboratório';

    protected static ?string $pluralModelLabel = 'laboratórios';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Infraestrutura';
    }

    public static function form(Schema $schema): Schema
    {
        return LaboratoryForm::configure($schema)->columns(1);
    }

    public static function table(Table $table): Table
    {
        return LaboratoriesTable::configure($table);
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
            'index' => ListLaboratories::route('/'),
            'create' => CreateLaboratory::route('/create'),
            'edit' => EditLaboratory::route('/{record}/edit'),
        ];
    }
}
