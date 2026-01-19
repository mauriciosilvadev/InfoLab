<?php

namespace App\Filament\Resources\LockPermissions\Pages;

use App\Filament\Resources\LockPermissions\LockPermissionResource;
use Filament\Resources\Pages\EditRecord;

class EditLockPermission extends EditRecord
{
    protected static string $resource = LockPermissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
