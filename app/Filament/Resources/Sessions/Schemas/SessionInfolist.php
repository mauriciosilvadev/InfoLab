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
                                TextEntry::make('session_id')
                                    ->label('ID da Sessão')
                                    ->copyable(),

                                TextEntry::make('user.name')
                                    ->label('Usuário')
                                    ->placeholder('Usuário Removido'),

                                TextEntry::make('ip_address')
                                    ->label('Endereço IP')
                                    ->copyable(),

                                TextEntry::make('status')
                                    ->label('Status da Sessão')
                                    ->badge(),

                                TextEntry::make('started_at')
                                    ->label('Início da Sessão')
                                    ->dateTime('d/m/Y H:i:s'),

                                TextEntry::make('ended_at')
                                    ->label('Fim da Sessão')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->placeholder('Em andamento'),

                                TextEntry::make('duration')
                                    ->label('Duração')
                                    ->placeholder('Em andamento'),

                                TextEntry::make('end_reason')
                                    ->label('Motivo do Fim')
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'logout' => 'Logout',
                                        'timeout' => 'Expirada',
                                        'force_logout' => 'Logout Forçado',
                                        'new_login' => 'Novo Login',
                                        default => 'N/A',
                                    })
                                    ->badge(),
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

                                TextEntry::make('location')
                                    ->label('Localização')
                                    ->placeholder('Não disponível'),
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
