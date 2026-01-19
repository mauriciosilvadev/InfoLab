<?php

namespace App\Filament\Resources\LockPermissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LockPermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome do Usuário')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Usuário Vinculado')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->user_id ? 'success' : 'gray')
                    ->state(fn ($record) => $record->user?->name ?? 'Não vinculado')
                    ->tooltip(fn ($record) => $record->user?->name ? 'Usuário "' . $record->user?->name . '" vinculado' : 'O sistema não encontrou um usuário vinculado a este email'),

                TextColumn::make('locks.asset_number')
                    ->label('Fechaduras')
                    ->badge()
                    ->wrap()
                    ->sortable(false),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Vínculo')
                    ->options([
                        '1' => 'Vinculados',
                        '0' => 'Não vinculados',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === '1') {
                            return $query->whereNotNull('user_id');
                        }
                        if ($data['value'] === '0') {
                            return $query->whereNull('user_id');
                        }

                        return $query;
                    }),

                SelectFilter::make('lock_id')
                    ->label('Fechadura')
                    ->relationship('locks', 'asset_number')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
