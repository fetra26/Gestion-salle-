<?php

use App\Providers\AppServiceProvider;
use App\Providers\PermissionServiceProvider;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionServiceProvider as SpatiePermissionServiceProvider;

return [
    AppServiceProvider::class,
    SpatiePermissionServiceProvider::class,
    PermissionServiceProvider::class,
];
