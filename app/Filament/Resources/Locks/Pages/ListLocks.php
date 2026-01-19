<?php

namespace App\Filament\Resources\Locks\Pages;

use App\Filament\Resources\Locks\LockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLocks extends ListRecords
{
    protected static string $resource = LockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
