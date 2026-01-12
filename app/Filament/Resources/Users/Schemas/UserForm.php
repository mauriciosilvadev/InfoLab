<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Usuário')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->disabled()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->disabled()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        DateTimePicker::make('email_verified_at')
                            ->label('E-mail Verificado em')
                            ->displayFormat('d/m/Y H:i')
                            ->disabled(),
                        Select::make('roles')
                            ->label('Perfil')
                            ->required()
                            ->preload()
                            ->options(function () {
                                return Role::whereIn('name', User::ROLES)
                                    ->pluck('name', 'id');
                            })
                            ->default(function ($record) {
                                return $record?->roles()->first()?->id;
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record && ! $state) {
                                    $component->state($record->roles()->first()?->id);
                                }
                            })
                            ->helperText('Selecione o perfil do usuário. Este é o único campo editável.'),
                    ])
                    ->columns(2),
            ]);
    }
}
