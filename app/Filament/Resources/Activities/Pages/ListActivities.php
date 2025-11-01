<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos')
                ->icon('heroicon-m-list-bullet')
                ->badge(fn () => $this->getModel()::count()),

            'system' => Tab::make('Sistema')
                ->icon('heroicon-m-cog-6-tooth')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('log_name', 'system'))
                ->badge(fn () => $this->getModel()::where('log_name', 'system')->count()),

            'user' => Tab::make('Usuários')
                ->icon('heroicon-m-users')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('log_name', 'user'))
                ->badge(fn () => $this->getModel()::where('log_name', 'user')->count()),

            'security' => Tab::make('Segurança')
                ->icon('heroicon-m-shield-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('log_name', 'security'))
                ->badge(fn () => $this->getModel()::where('log_name', 'security')->count()),

            'audit' => Tab::make('Auditoria')
                ->icon('heroicon-m-clipboard-document-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('log_name', 'audit'))
                ->badge(fn () => $this->getModel()::where('log_name', 'audit')->count()),

            'default' => Tab::make('Outros')
                ->icon('heroicon-m-ellipsis-horizontal')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('log_name', ['system', 'user', 'security', 'audit']))
                ->badge(fn () => $this->getModel()::whereNotIn('log_name', ['system', 'user', 'security', 'audit'])->count()),
        ];
    }
}
