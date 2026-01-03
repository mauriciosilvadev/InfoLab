<?php

namespace App\Models;

use App\Models\Activity as ModelsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Laboratory extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'building',
        'computers_count',
        'photos',
        'softwares',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'computers_count' => 'integer',
        'photos' => 'array',
        'softwares' => 'array',
    ];

    /**
     * Configure the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'building', 'computers_count', 'photos', 'softwares'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Customize the activity before it's saved.
     */
    public function tapActivity(Activity $activity, string $eventName): void
    {
        $user = Auth::user();
        $userName = $user?->name ?? 'Sistema';
        $laboratoryName = $this->name;

        $activity->log_name = ModelsActivity::LOG_TYPE_SYSTEM;
        $activity->causer_id = $user?->getAuthIdentifier();
        $activity->causer_type = $user ? get_class($user) : null;

        $description = match ($eventName) {
            'created' => "Usuário {$userName} criou o laboratório {$laboratoryName}",
            'updated' => "Usuário {$userName} editou o laboratório {$laboratoryName}",
            'deleted' => "Usuário {$userName} deletou o laboratório {$laboratoryName}",
            default => "Atividade realizada no laboratório {$laboratoryName}",
        };

        $activity->description = $description;

        $existingProperties = $activity->properties;
        if ($existingProperties instanceof Collection) {
            $existingProperties = $existingProperties->toArray();
        } elseif (! is_array($existingProperties)) {
            $existingProperties = [];
        }

        $activity->properties = array_merge(
            $existingProperties,
            [
                'laboratory_id' => $this->getKey(),
                'laboratory_name' => $this->name,
                'building' => $this->building,
                'user_id' => $user?->getAuthIdentifier(),
                'user_name' => $user?->name ?? 'N/A',
                'user_email' => $user?->email ?? 'N/A',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString(),
            ]
        );
    }
}
