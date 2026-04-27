<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Absensi;
use App\Policies\AbsensiPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Absensi::class => AbsensiPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('manage-master-data', fn (User $user) => $user->hasRole(User::ROLE_ADMIN));
        Gate::define('manage-users', fn (User $user) => $user->hasRole(User::ROLE_ADMIN));
        Gate::define('manage-sessions-and-jadwal', fn (User $user) => $user->hasRole(User::ROLE_ADMIN));
        Gate::define('input-absensi', fn (User $user) => $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_GURU]));
        Gate::define('edit-any-absensi', fn (User $user) => $user->hasRole(User::ROLE_ADMIN));
        Gate::define('view-all-absensi', fn (User $user) => $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_KEPALA_SEKOLAH]));
        Gate::define('export-laporan', fn (User $user) => $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_KEPALA_SEKOLAH]));
    }
}
