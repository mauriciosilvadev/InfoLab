<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\Activity;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
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
                ->modifyQueryUsing(fn (Builder $query) => $query->where('log_name', Activity::LOG_TYPE_SYSTEM))
                ->badge(fn () => $this->getModel()::where('log_name', Activity::LOG_TYPE_SYSTEM)->count()),

            'user' => Tab::make('Usuários')
                ->icon('heroicon-m-users')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('log_name', Activity::LOG_TYPE_USER))
                ->badge(fn () => $this->getModel()::where('log_name', Activity::LOG_TYPE_USER)->count()),

            'security' => Tab::make('Segurança')
                ->icon('heroicon-m-shield-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('log_name', Activity::LOG_TYPE_SECURITY))
                ->badge(fn () => $this->getModel()::where('log_name', Activity::LOG_TYPE_SECURITY)->count()),

            'default' => Tab::make('Outros')
                ->icon('heroicon-m-ellipsis-horizontal')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('log_name', [
                    Activity::LOG_TYPE_SYSTEM,
                    Activity::LOG_TYPE_USER,
                    Activity::LOG_TYPE_SECURITY,
                ]))
                ->badge(fn () => $this->getModel()::whereNotIn('log_name', [
                    Activity::LOG_TYPE_SYSTEM,
                    Activity::LOG_TYPE_USER,
                    Activity::LOG_TYPE_SECURITY,
                ])->count()),
        ];
    }
}
