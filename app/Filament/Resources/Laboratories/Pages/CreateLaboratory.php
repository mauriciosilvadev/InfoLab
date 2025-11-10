<?php

namespace App\Filament\Resources\Laboratories\Pages;

use App\Filament\Resources\Laboratories\LaboratoryResource;
use App\Models\Activity;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateLaboratory extends CreateRecord
{
    protected static string $resource = LaboratoryResource::class;

    protected function afterCreate(): void
    {
        $user = Auth::user();

        if (! $user instanceof Model) {
            return;
        }

        activity(Activity::LOG_TYPE_SYSTEM)
            ->performedOn($this->record)
            ->causedBy($user)
            ->withProperties([
                'laboratory_id' => $this->record->getKey(),
                'laboratory_name' => $this->record->name,
                'building' => $this->record->building,
                'user_id' => $user->getAuthIdentifier(),
                'user_name' => $user->name ?? 'N/A',
                'user_email' => $user->email ?? 'N/A',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString(),
            ])
            ->log("Usuário {$user->name} criou o laboratório {$this->record->name}");
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
