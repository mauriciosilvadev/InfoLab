<?php

namespace App\Filament\Resources\Locks\Schemas;

use App\Models\Laboratory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class LockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações da Fechadura')
                    ->schema([
                        TextInput::make('asset_number')
                            ->label('Número Patrimonial')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('laboratory_id')
                            ->label('Laboratório')
                            ->relationship('laboratory', 'name', modifyQueryUsing: function (Builder $query, $get, $record) {
                                $query->whereNotExists(function ($subquery) use ($record) {
                                    $subquery->selectRaw('1')
                                        ->from('locks')
                                        ->whereColumn('locks.laboratory_id', 'laboratories.id');

                                    if ($record && $record->exists) {
                                        $subquery->where('locks.id', '!=', $record->id);
                                    }
                                });
                            })
                            ->getOptionLabelFromRecordUsing(fn (Laboratory $record): string => "{$record->name} - {$record->building}")
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
            ]);
    }
}
