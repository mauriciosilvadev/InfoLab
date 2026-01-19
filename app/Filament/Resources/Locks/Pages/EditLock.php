<?php

namespace App\Filament\Resources\Locks\Pages;

use App\Filament\Resources\Locks\LockResource;
use Filament\Resources\Pages\EditRecord;

class EditLock extends EditRecord
{
    protected static string $resource = LockResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
