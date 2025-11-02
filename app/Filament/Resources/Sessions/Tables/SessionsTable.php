<?php

namespace App\Filament\Resources\Sessions\Tables;

use Filament\Forms\Components\DatePicker;
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
                TextColumn::make('session_id')
                    ->label('ID da Sessão')
                    ->searchable()
                    ->limit(8)
                    ->tooltip(fn ($record) => $record->session_id),

                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Usuário Removido')
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

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ativa' => 'success',
                        'Logout' => 'info',
                        'Expirada' => 'warning',
                        'Logout Forçado' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('started_at')
                    ->label('Início')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->started_at->format('d/m/Y H:i:s')),

                TextColumn::make('ended_at')
                    ->label('Fim')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->placeholder('Em andamento')
                    ->since()
                    ->tooltip(fn ($record) => $record->ended_at?->format('d/m/Y H:i:s')),

                TextColumn::make('duration')
                    ->label('Duração')
                    ->placeholder('Em andamento'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('is_active')
                    ->label('Sessões Ativas')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),

                Filter::make('started_at')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('started_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('started_at', '<=', $date),
                            );
                    }),

                SelectFilter::make('device')
                    ->label('Dispositivo')
                    ->options([
                        'Mobile' => 'Mobile',
                        'Tablet' => 'Tablet',
                        'Desktop' => 'Desktop',
                    ]),

                SelectFilter::make('end_reason')
                    ->label('Motivo do Fim')
                    ->options([
                        'logout' => 'Logout',
                        'timeout' => 'Expirada',
                        'force_logout' => 'Logout Forçado',
                        'new_login' => 'Novo Login',
                    ]),
            ])
            ->defaultSort('started_at', 'desc');
    }
}
