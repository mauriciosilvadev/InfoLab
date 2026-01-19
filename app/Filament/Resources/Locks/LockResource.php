<?php

namespace App\Filament\Resources\Locks;

use App\Filament\Resources\Locks\Pages\CreateLock;
use App\Filament\Resources\Locks\Pages\EditLock;
use App\Filament\Resources\Locks\Pages\ListLocks;
use App\Filament\Resources\Locks\Schemas\LockForm;
use App\Filament\Resources\Locks\Tables\LocksTable;
use App\Models\Lock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LockResource extends Resource
{
    protected static ?string $model = Lock::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $recordTitleAttribute = 'asset_number';

    protected static ?string $navigationLabel = 'Fechaduras';

    protected static ?string $modelLabel = 'fechadura';

    protected static ?string $pluralModelLabel = 'fechaduras';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Infraestrutura';
    }

    public static function form(Schema $schema): Schema
    {
        return LockForm::configure($schema)->columns(1);
    }

    public static function table(Table $table): Table
    {
        return LocksTable::configure($table);
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
            'index' => ListLocks::route('/'),
            'create' => CreateLock::route('/create'),
            'edit' => EditLock::route('/{record}/edit'),
        ];
    }
}
