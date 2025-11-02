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
                            ->color(fn ($record): string => $record->getLogTypeColor()),

                        TextEntry::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),

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
