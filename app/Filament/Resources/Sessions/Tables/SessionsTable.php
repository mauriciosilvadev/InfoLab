<?php

namespace App\Filament\Resources\Sessions\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID da Sessão')
                    ->searchable()
                    ->limit(8)
                    ->tooltip(fn ($record) => $record->id),

                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sessão Anônima')
                    ->icon('heroicon-m-user'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->icon('heroicon-m-globe-alt'),

                TextColumn::make('browser')
                    ->label('Navegador')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Chrome' => 'success',
                        'Firefox' => 'warning',
                        'Safari' => 'info',
                        'Edge' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('device')
                    ->label('Dispositivo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mobile' => 'success',
                        'Tablet' => 'warning',
                        'Desktop' => 'primary',
                        default => 'gray',
                    }),

                IconColumn::make('is_active')
                    ->label('Ativa')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('last_activity')
                    ->label('Última Atividade')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->last_activity->format('d/m/Y H:i:s')),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('is_active')
                    ->label('Sessões Ativas')
                    ->query(fn (Builder $query): Builder => $query->where('last_activity', '>=', now()->subMinutes(config('session.lifetime')))),

                Filter::make('last_activity')
                    ->form([
                        DatePicker::make('from')
                            ->label('De'),
                        DatePicker::make('until')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_activity', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_activity', '<=', $date),
                            );
                    }),

                SelectFilter::make('device')
                    ->label('Dispositivo')
                    ->options([
                        'Mobile' => 'Mobile',
                        'Tablet' => 'Tablet',
                        'Desktop' => 'Desktop',
                    ]),
            ])
            ->defaultSort('last_activity', 'desc')
            ->poll('30s');
    }
}
