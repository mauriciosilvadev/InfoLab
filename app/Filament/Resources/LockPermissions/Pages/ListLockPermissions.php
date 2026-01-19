<?php

namespace App\Filament\Resources\LockPermissions\Pages;

use App\Filament\Resources\LockPermissions\LockPermissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLockPermissions extends ListRecords
{
    protected static string $resource = LockPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with('locks');
    }
}
