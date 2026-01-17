<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Permission;
use App\Models\Role;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Jika diakses lewat Ngrok, paksa link menjadi HTTPS
        if (str_contains(request()->getHttpHost(), 'ngrok-free.dev')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        // Tambahkan ini di paling atas
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        Gate::define('view_any_shield::role', function ($user) {
            return $user->hasRole('super_admin');
        });

        Filament::serving(function () {
            if (!auth()->user()?->hasRole('super_admin')) {
                Filament::renderHook(
                    'sidebar.items.start',
                    fn() => null // Ini akan memicu refresh state navigasi
                );
            }
        });

        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);
    }
}
