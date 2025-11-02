<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    /**
     * Constants for log types.
     */
    public const LOG_TYPE_DEFAULT = 'default';
    public const LOG_TYPE_USER = 'user';
    public const LOG_TYPE_SYSTEM = 'system';
    public const LOG_TYPE_SECURITY = 'security';
    public const LOG_TYPE_ERROR = 'error';
    public const LOG_TYPE_DEBUG = 'debug';

    /**
     * Returns all available log types.
     */
    public static function getLogTypes(): array
    {
        return [
            self::LOG_TYPE_DEFAULT => 'Padrão',
            self::LOG_TYPE_USER => 'Usuário',
            self::LOG_TYPE_SYSTEM => 'Sistema',
            self::LOG_TYPE_SECURITY => 'Segurança',
            self::LOG_TYPE_ERROR => 'Erro',
            self::LOG_TYPE_DEBUG => 'Debug',
        ];
    }

    /**
     * Returns the color of the badge based on the log type.
     */
    public function getLogTypeColor(): string
    {
        return match ($this->log_name) {
            self::LOG_TYPE_DEFAULT => 'gray',
            self::LOG_TYPE_USER => 'blue',
            self::LOG_TYPE_SYSTEM => 'green',
            self::LOG_TYPE_SECURITY => 'red',
            self::LOG_TYPE_ERROR => 'red',
            self::LOG_TYPE_DEBUG => 'slate',
            default => 'gray',
        };
    }

    /**
     * Scope to filter by log type.
     */
    public function scopeByLogType($query, string $logType)
    {
        return $query->where('log_name', $logType);
    }

    /**
     * Scope to filter by event.
     */
    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope to filter by security and audit logs.
     */
    public function scopeSecurityAudit($query)
    {
        return $query->whereIn('log_name', [
            self::LOG_TYPE_SECURITY,
        ]);
    }

    /**
     * Scope to filter by system and integrations logs.
     */
    public function scopeSystemIntegration($query)
    {
        return $query->whereIn('log_name', [
            self::LOG_TYPE_SYSTEM,
        ]);
    }

    /**
     * Accessor to get the friendly name of the log type.
     */
    public function getLogTypeNameAttribute(): string
    {
        return self::getLogTypes()[$this->log_name] ?? $this->log_name;
    }

    /**
     * Accessor to get the friendly name of the event.
     */
    public function getEventNameAttribute(): string
    {
        return self::getEvents()[$this->event] ?? $this->event;
    }
}
