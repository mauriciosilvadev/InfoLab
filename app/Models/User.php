<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    const ROLES = [self::ADMIN_ROLE, self::VIGIA_ROLE, self::SUGRAD_ROLE, self::USER_ROLE];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
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
    public function canAccessFilament(): bool
    {
        return $this->hasRole(self::ROLES);
    }
}
