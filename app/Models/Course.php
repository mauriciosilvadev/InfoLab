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

class Course extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Configure the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name'])
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
        $courseName = $this->name;

        $activity->log_name = ModelsActivity::LOG_TYPE_SYSTEM;
        $activity->causer_id = $user?->getAuthIdentifier();
        $activity->causer_type = $user ? get_class($user) : null;

        $description = match ($eventName) {
            'created' => "Usuário {$userName} criou o curso {$courseName}",
            'updated' => "Usuário {$userName} editou o curso {$courseName}",
            'deleted' => "Usuário {$userName} deletou o curso {$courseName}",
            default => "Atividade realizada no curso {$courseName}",
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
                'course_id' => $this->getKey(),
                'course_name' => $this->name,
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
