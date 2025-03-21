<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Compartir los datos del usuario autenticado con Inertia
        Inertia::share('auth', function () {
            return [
                'user' => Auth::check() ? Auth::user()->load('roles') : null,
            ];
        });
    }
}
