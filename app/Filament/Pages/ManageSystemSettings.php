<?php

namespace App\Filament\Pages;

use App\Models\Laboratory;
use App\Models\User;
use App\Settings\SystemSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSystemSettings extends SettingsPage
{
    protected static string $settings = SystemSettings::class;

    protected static ?string $title = 'Configurações do Sistema';

    protected static ?string $navigationLabel = 'Configurações';

    protected static ?string $pluralModelLabel = 'configurações';

    protected static string|UnitEnum|null $navigationGroup = 'Gestão';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(User::ADMIN_ROLE) ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Semestre Letivo')
                    ->schema([
                        TextInput::make('current_semester_year')
                            ->label('Ano Letivo')
                            ->numeric()
                            ->required()
                            ->minValue(now()->year),
                        Select::make('current_semester_period')
                            ->label('Período')
                            ->required()
                            ->options([
                                '1' => '1º Semestre',
                                '2' => '2º Semestre',
                            ]),
                    ]),
                Section::make('Monitoria')
                    ->schema([
                        Select::make('monitoring_laboratory_id')
                            ->label('Laboratório de Monitoria')
                            ->options(function () {
                                return Laboratory::query()
                                    ->get()
                                    ->mapWithKeys(function ($lab) {
                                        return [$lab->id => "{$lab->name} - {$lab->building}"];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),
                Section::make('Contato e Links')
                    ->schema([
                        Repeater::make('contact_emails')
                            ->label('E-mails de Contato')
                            ->simple(
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                            )
                            ->defaultItems(0)
                            ->collapsible(),
                        Repeater::make('useful_links')
                            ->label('Links Úteis')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Título')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required(),
                            ])
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
            ]);
    }
}
