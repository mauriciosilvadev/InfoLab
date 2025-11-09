<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\Activity;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentActivitiesWidget extends TableWidget
{
    protected static ?string $heading = 'Auditorias Recentes';

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->with('causer')
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('causer.name')
                    ->label('Usuário')
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Sistema')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    })
                    ->wrap(),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Activity $record): string => ActivityResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([3, 10])
            ->defaultPaginationPageOption(3)
            ->emptyStateHeading('Nenhuma auditoria encontrada');
    }
}
