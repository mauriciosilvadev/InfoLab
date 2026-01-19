<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    const ADMIN_ROLE = 'admin';
    const VIGIA_ROLE = 'vigia';
    const SUGRAD_ROLE = 'sugrad';
    const USER_ROLE = 'user';
    const TEACHER_ROLE = 'teacher';

    const ROLES = [self::ADMIN_ROLE, self::VIGIA_ROLE, self::SUGRAD_ROLE, self::USER_ROLE, self::TEACHER_ROLE];

    const SYSTEM_ROLES = [self::ADMIN_ROLE, self::VIGIA_ROLE, self::SUGRAD_ROLE];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'alternative_email',
        'cpf',
        'matricula',
        'password',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Verify if the user has permission to access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(self::ROLES);
    }

    /**
     * Get the session history for the user.
     */
    public function sessionHistory(): HasMany
    {
        return $this->hasMany(UserSessionHistory::class);
    }

    /**
     * Get the lock permissions for the user.
     */
    public function lockPermissions(): HasMany
    {
        return $this->hasMany(LockPermission::class);
    }
}
