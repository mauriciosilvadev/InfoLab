<?php

namespace App\Filament\Resources\Locks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_number')
                    ->label('Número Patrimonial')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('laboratory.name')
                    ->label('Laboratório')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        if (! $record->laboratory) {
                            return '—';
                        }

                        return "{$record->laboratory->name} - {$record->laboratory->building}";
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
