<?php

namespace App\Filament\Resources\Sessions;

use App\Filament\Resources\Sessions\Pages\ListSessions;
use App\Filament\Resources\Sessions\Pages\ViewSession;
use App\Filament\Resources\Sessions\Schemas\SessionInfolist;
use App\Filament\Resources\Sessions\Tables\SessionsTable;
use App\Models\UserSessionHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SessionResource extends Resource
{
    protected static ?string $model = UserSessionHistory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Histórico de Sessões';

    protected static ?string $modelLabel = 'sessão';

    protected static ?string $pluralModelLabel = 'sessões';

    protected static ?int $navigationSort = 20;

    public static function infolist(Schema $schema): Schema
    {
        return SessionInfolist::configure($schema);
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Segurança';
    }

    public static function table(Table $table): Table
    {
        return SessionsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSessions::route('/'),
            'view' => ViewSession::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return $record->user_id === Auth::id();
    }
}
