<?php

namespace App\Helpers;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log to user activity.
     */
    public static function logUser(string $description, ?Model $subject = null, array $properties = []): void
    {
        activity(Activity::LOG_TYPE_USER)
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties($properties)
            ->log($description);
    }

    /**
     * Log to system activity.
     */
    public static function logSystem(string $description, ?Model $subject = null, array $properties = []): void
    {
        activity(Activity::LOG_TYPE_SYSTEM)
            ->performedOn($subject)
            ->withProperties($properties)
            ->log($description);
    }

    /**
     * Log to security activity.
     */
    public static function logSecurity(string $description, ?Model $subject = null, array $properties = []): void
    {
        activity(Activity::LOG_TYPE_SECURITY)
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties($properties)
            ->log($description);
    }

    /**
     * Log de erro.
     */
    public static function logError(string $description, ?Model $subject = null, array $properties = []): void
    {
        activity(Activity::LOG_TYPE_ERROR)
            ->performedOn($subject)
            ->withProperties($properties)
            ->log($description);
    }

    /**
     * Log de debug.
     */
    public static function logDebug(string $description, ?Model $subject = null, array $properties = []): void
    {
        activity(Activity::LOG_TYPE_DEBUG)
            ->performedOn($subject)
            ->withProperties($properties)
            ->log($description);
    }
}
