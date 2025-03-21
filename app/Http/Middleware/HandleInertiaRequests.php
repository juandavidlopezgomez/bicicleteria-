<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(fn($role) => ['name' => $role->name]),
                ] : null,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
            ],
            'urls' => [
                'dashboard' => route('dashboard'),
                'categories' => route('categories.index'),
                'categories_create' => route('categories.create'),
                'logout' => route('logout'),
            ],
        ]);
    }
}