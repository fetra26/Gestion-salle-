<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Directive Blade pour vérifier le rôle (supporte 'role1|role2')
        Blade::if('role', function ($role) {
            $roles = array_map('trim', explode('|', $role));
            return request()->user()?->hasAnyRole($roles);
        });

        // Directive Blade pour vérifier si peut valider
        Blade::if('peutValider', function () {
            return request()->user()?->peutValider();
        });
    }
}