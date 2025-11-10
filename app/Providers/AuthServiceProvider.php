<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Laboratory;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\LaboratoryPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Laboratory::class => LaboratoryPolicy::class,
        Activity::class => ActivityPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole(User::ADMIN_ROLE) ? true : null;
        });
    }
}
