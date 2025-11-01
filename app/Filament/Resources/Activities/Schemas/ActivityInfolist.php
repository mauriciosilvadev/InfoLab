<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações Gerais')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),

                        TextEntry::make('log_name')
                            ->label('Tipo de Log')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'default' => 'gray',
                                'user' => 'blue',
                                'system' => 'green',
                                'security' => 'red',
                                default => 'gray',
                            }),

                        TextEntry::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),

                        TextEntry::make('event')
                            ->label('Evento')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                'restored' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('created_at')
                            ->label('Data/Hora')
                            ->dateTime('d/m/Y H:i:s'),

                        TextEntry::make('batch_uuid')
                            ->label('UUID do Lote')
                            ->placeholder('Não agrupado'),
                    ])
                    ->columns(2),

                Section::make('Propriedades')
                    ->schema([
                        KeyValueEntry::make('properties')
                            ->label('Dados Adicionais')
                            ->columnSpanFull()
                            ->keyLabel('Propriedade')
                            ->valueLabel('Valor'),
                    ])
                    ->collapsible(),
            ]);
    }
}
