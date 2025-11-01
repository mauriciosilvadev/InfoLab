<?php

namespace App\Filament\Resources\Sessions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SessionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações da Sessão')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID da Sessão')
                                    ->copyable(),

                                TextEntry::make('user.name')
                                    ->label('Usuário')
                                    ->placeholder('Sessão Anônima'),

                                TextEntry::make('ip_address')
                                    ->label('Endereço IP')
                                    ->copyable(),

                                TextEntry::make('last_activity')
                                    ->label('Última Atividade')
                                    ->dateTime('d/m/Y H:i:s'),
                            ]),
                    ]),

                Section::make('Informações do Dispositivo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('browser')
                                    ->label('Navegador')
                                    ->badge(),

                                TextEntry::make('device')
                                    ->label('Tipo de Dispositivo')
                                    ->badge(),

                                TextEntry::make('is_active')
                                    ->label('Status da Sessão')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ativa' : 'Inativa')
                                    ->badge(),
                            ]),
                    ]),

                Section::make('User Agent')
                    ->schema([
                        TextEntry::make('user_agent')
                            ->label('User Agent')
                            ->copyable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}