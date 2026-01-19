<?php

namespace App\Models;

use App\Models\Activity as ModelsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lock extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'asset_number',
        'laboratory_id',
    ];

    /**
     * Configure the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['asset_number', 'laboratory_id'])
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
        $assetNumber = $this->asset_number;
        $laboratoryName = $this->laboratory?->name ?? 'N/A';

        $activity->log_name = ModelsActivity::LOG_TYPE_SYSTEM;
        $activity->causer_id = $user?->getAuthIdentifier();
        $activity->causer_type = $user ? get_class($user) : null;

        $description = match ($eventName) {
            'created' => "Usuário {$userName} criou a fechadura {$assetNumber}",
            'updated' => "Usuário {$userName} editou a fechadura {$assetNumber}",
            'deleted' => "Usuário {$userName} deletou a fechadura {$assetNumber}",
            default => "Atividade realizada na fechadura {$assetNumber}",
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
                'lock_id' => $this->getKey(),
                'asset_number' => $this->asset_number,
                'laboratory_id' => $this->laboratory_id,
                'laboratory_name' => $laboratoryName,
                'user_id' => $user?->getAuthIdentifier(),
                'user_name' => $user?->name ?? 'N/A',
                'user_email' => $user?->email ?? 'N/A',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString(),
            ]
        );
    }

    /**
     * Get the laboratory that owns the lock.
     */
    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }
}
