<?php

namespace App\Filament\Resources\LockPermissions;

use App\Filament\Resources\LockPermissions\Pages\CreateLockPermission;
use App\Filament\Resources\LockPermissions\Pages\EditLockPermission;
use App\Filament\Resources\LockPermissions\Pages\ListLockPermissions;
use App\Filament\Resources\LockPermissions\Schemas\LockPermissionForm;
use App\Filament\Resources\LockPermissions\Tables\LockPermissionsTable;
use App\Models\LockPermission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LockPermissionResource extends Resource
{
    protected static ?string $model = LockPermission::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Permissões de Acesso';

    protected static ?string $modelLabel = 'permissão de acesso';

    protected static ?string $pluralModelLabel = 'permissões de acesso';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Fechaduras';
    }

    public static function form(Schema $schema): Schema
    {
        return LockPermissionForm::configure($schema)->columns(1);
    }

    public static function table(Table $table): Table
    {
        return LockPermissionsTable::configure($table);
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
            'index' => ListLockPermissions::route('/'),
            'create' => CreateLockPermission::route('/create'),
            'edit' => EditLockPermission::route('/{record}/edit'),
        ];
    }
}
