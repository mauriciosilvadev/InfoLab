<?php

namespace App\Filament\Resources\Laboratories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LaboratoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('building')
                    ->label('Prédio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('computers_count')
                    ->label('Computadores')
                    ->sortable()
                    ->alignRight()
                    ->formatStateUsing(fn ($state) => $state ?? '—'),
                TextColumn::make('softwares')
                    ->label('Softwares')
                    ->limit(20)
                    ->wrap()
                    ->formatStateUsing(function ($state) {
                        if (blank($state)) {
                            return '—';
                        }

                        if (is_string($state)) {
                            $decoded = json_decode($state, true);

                            if (json_last_error() === JSON_ERROR_NONE) {
                                $state = $decoded;
                            } else {
                                return $state;
                            }
                        }

                        if (is_array($state)) {
                            $filtered = array_filter($state, fn ($item) => filled($item));

                            return empty($filtered) ? '—' : implode(', ', $filtered);
                        }

                        return (string) $state;
                    }),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
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
