<?php

namespace App\Models;

use App\Models\Activity as ModelsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LockPermission extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * The attributes that should not be mass assignable.
     * user_id is managed by observers.
     */
    protected $guarded = [
        'user_id',
    ];

    /**
     * Get the user that owns the lock permission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the locks that have this permission.
     */
    public function locks(): BelongsToMany
    {
        return $this->belongsToMany(Lock::class, 'lock_lock_permission');
    }

    /**
     * Configure the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'user_id'])
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
        $name = $this->name;

        $activity->log_name = ModelsActivity::LOG_TYPE_SYSTEM;
        $activity->causer_id = $user?->getAuthIdentifier();
        $activity->causer_type = $user ? get_class($user) : null;

        $description = match ($eventName) {
            'created' => "Usuário {$userName} criou a permissão de acesso {$name}",
            'updated' => "Usuário {$userName} editou a permissão de acesso {$name}",
            'deleted' => "Usuário {$userName} deletou a permissão de acesso {$name}",
            default => "Atividade realizada na permissão de acesso {$name}",
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
                'lock_permission_id' => $this->getKey(),
                'name' => $this->name,
                'email' => $this->email,
                'user_id' => $this->user_id,
                'user_name' => $user?->name ?? 'N/A',
                'user_email' => $user?->email ?? 'N/A',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString(),
            ]
        );
    }
}
