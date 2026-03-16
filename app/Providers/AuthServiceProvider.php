<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();
        Gate::before(function ($user, $ability) {
            if ($user->roles()->where('roles.name', 'administrador')->exists()) {
                return true;
            }
        });
        try {
            \App\Models\Permission::pluck('name')->each(function ($name) {
                Gate::define($name, fn ($user) => $user->hasPermission($name));
            });
        } catch (\Exception) {

        }
    }
}
