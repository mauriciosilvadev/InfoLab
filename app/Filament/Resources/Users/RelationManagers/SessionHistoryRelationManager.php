<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Sessions\Tables\SessionsTable;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SessionHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'sessionHistory';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Histórico de Sessões';

    protected static ?string $modelLabel = 'sessão';

    protected static ?string $pluralModelLabel = 'sessões';

    public function table(Table $table): Table
    {
        return SessionsTable::configure($table)
            ->filters([
                Filter::make('is_active')
                    ->label('Sessões Ativas')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                Filter::make('started_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('De'),
                        DatePicker::make('until')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('started_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('started_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('device')
                    ->label('Dispositivo')
                    ->options([
                        'Mobile' => 'Mobile',
                        'Tablet' => 'Tablet',
                        'Desktop' => 'Desktop',
                    ]),
                SelectFilter::make('end_reason')
                    ->label('Motivo do Fim')
                    ->options([
                        'logout' => 'Logout',
                        'timeout' => 'Expirada',
                        'force_logout' => 'Logout Forçado',
                        'new_login' => 'Novo Login',
                    ]),
            ]);
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof User;
    }

    public function canCreate(): bool
    {
        return false;
    }

    public function canEdit($record): bool
    {
        return false;
    }

    public function canDelete($record): bool
    {
        return false;
    }
}
