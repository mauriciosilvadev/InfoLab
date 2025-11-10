<?php

namespace App\Filament\Resources\Laboratories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LaboratoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Laboratório')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('building')
                            ->label('Prédio')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('computers_count')
                            ->label('Quantidade de Computadores')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(9999)
                            ->nullable(),
                        FileUpload::make('photos')
                            ->label('Fotos')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('laboratories/photos')
                            ->maxFiles(10)
                            ->imagePreviewHeight('150')
                            ->columnSpanFull()
                            ->nullable(),
                        TagsInput::make('softwares')
                            ->label('Softwares Disponíveis')
                            ->placeholder('Adicionar software')
                            ->helperText('Escreva o nome do software e clique em Enter para adicionar mais')
                            ->nullable()
                            ->separator(','),
                    ])
                    ->columns(2),
            ]);
    }
}
