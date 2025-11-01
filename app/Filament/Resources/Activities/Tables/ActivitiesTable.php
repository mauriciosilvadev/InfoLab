<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('log_name')
                    ->label('Tipo de Log')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'default' => 'gray',
                        'user' => 'blue',
                        'system' => 'green',
                        'security' => 'red',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),

                TextColumn::make('event')
                    ->label('Evento')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('subject_type')
                    ->label('Tipo do Objeto')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),

                TextColumn::make('subject_id')
                    ->label('ID do Objeto')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('causer_type')
                    ->label('Tipo do Usuário')
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : 'Sistema'),

                TextColumn::make('causer_id')
                    ->label('ID do Usuário')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sistema'),

                TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('batch_uuid')
                    ->label('Lote')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(8),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Tipo de Log')
                    ->options([
                        'default' => 'Padrão',
                        'user' => 'Usuário',
                        'system' => 'Sistema',
                        'security' => 'Segurança',
                    ]),

                SelectFilter::make('event')
                    ->label('Evento')
                    ->options([
                        'created' => 'Criado',
                        'updated' => 'Atualizado',
                        'deleted' => 'Excluído',
                        'restored' => 'Restaurado',
                    ]),

                SelectFilter::make('subject_type')
                    ->label('Tipo do Objeto')
                    ->options(function () {
                        return \Spatie\Activitylog\Models\Activity::query()
                            ->distinct()
                            ->whereNotNull('subject_type')
                            ->pluck('subject_type')
                            ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                            ->toArray();
                    }),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Data inicial'),
                        DatePicker::make('created_until')
                            ->label('Data final'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
