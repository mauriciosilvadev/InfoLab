<?php

namespace App\Filament\Resources\LockPermissions\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LockPermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Usuário')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome do Usuário')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->helperText('Email para vínculo automático com usuário do sistema'),
                    ])
                    ->columns(2),

                Section::make('Fechaduras Autorizadas')
                    ->schema([
                        CheckboxList::make('locks')
                            ->label('Selecione as fechaduras')
                            ->relationship('locks', 'asset_number')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $lab = $record->laboratory;

                                return "{$record->asset_number} - {$lab->name} ({$lab->building})";
                            })
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->required()
                            ->helperText('Selecione uma ou mais fechaduras para autorizar acesso'),
                    ])
                    ->columns(1),

            ]);
    }
}
