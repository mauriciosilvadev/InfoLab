<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSessionHistory extends Model
{
    protected $table = 'user_sessions_history';

    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'location',
        'started_at',
        'ended_at',
        'is_active',
        'end_reason',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDurationAttribute(): ?string
    {
        if (! $this->ended_at) {
            return null;
        }

        $duration = $this->started_at->diff($this->ended_at);

        if ($duration->days > 0) {
            return $duration->format('%d dias, %h horas e %i minutos');
        } elseif ($duration->h > 0) {
            return $duration->format('%h horas e %i minutos');
        } else {
            return $duration->format('%i minutos');
        }
    }

    public function getStartedAtFormattedAttribute(): string
    {
        $now = now();
        $diff = $this->started_at->diff($now);

        if ($diff->days > 7) {
            return $this->started_at->format('d/m/Y H:i');
        } elseif ($diff->days > 0) {
            return $this->started_at->format('d/m H:i');
        } elseif ($diff->h > 0) {
            return $this->started_at->format('H:i');
        } else {
            return $this->started_at->format('H:i:s');
        }
    }

    public function getEndedAtFormattedAttribute(): ?string
    {
        if (! $this->ended_at) {
            return null;
        }

        $now = now();
        $diff = $this->ended_at->diff($now);

        if ($diff->days > 7) {
            return $this->ended_at->format('d/m/Y H:i');
        } elseif ($diff->days > 0) {
            return $this->ended_at->format('d/m H:i');
        } elseif ($diff->h > 0) {
            return $this->ended_at->format('H:i');
        } else {
            return $this->ended_at->format('H:i:s');
        }
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_active) {
            return 'Ativa';
        }

        return match ($this->end_reason) {
            'logout' => 'Logout',
            'timeout' => 'Expirada',
            'force_logout' => 'Logout Forçado',
            default => 'Finalizada',
        };
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
