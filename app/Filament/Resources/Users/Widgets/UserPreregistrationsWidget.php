<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use App\Models\UserPreregistration;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UserPreregistrationsWidget extends TableWidget
{
    protected static ?string $heading = 'Pré-registros Pendentes';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserPreregistration::query()
                    ->with('creator')
                    ->latest()
            )
            ->columns([
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('role')
                    ->label('Perfil')
                    ->badge()
                    ->formatStateUsing(function (?string $state): string {
                        return match ($state) {
                            User::ADMIN_ROLE => 'Administrador',
                            User::TEACHER_ROLE => 'Professor',
                            User::USER_ROLE => 'Usuário',
                            default => $state ?? '—',
                        };
                    })
                    ->color(function (?string $state): string {
                        return match ($state) {
                            User::ADMIN_ROLE => 'danger',
                            User::TEACHER_ROLE => 'info',
                            User::USER_ROLE => 'gray',
                            default => 'gray',
                        };
                    })
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Solicitado por')
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Sistema')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                DeleteAction::make()
                    ->label('Cancelar')
                    ->modalHeading('Cancelar pré-registro'),
            ])
            ->emptyStateHeading('Nenhum pré-registro pendente');
    }
}
