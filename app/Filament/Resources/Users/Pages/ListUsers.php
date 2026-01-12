<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Widgets\UserPreregistrationsWidget;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Pré-registrar Usuário')
                ->visible(fn () => auth()->user()?->hasRole(User::ADMIN_ROLE)),
        ];
    }

    public function getHeaderWidgets(): array
    {
        if (! auth()->user()?->hasRole(User::ADMIN_ROLE)) {
            return [];
        }

        return [
            UserPreregistrationsWidget::class,
        ];
    }
}
