<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;

class AbsensiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            User::ROLE_ADMIN,
            User::ROLE_GURU,
            User::ROLE_KEPALA_SEKOLAH,
        ]);
    }

    public function view(User $user, Absensi $absensi): bool
    {
        if ($user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_KEPALA_SEKOLAH])) {
            return true;
        }

        return $user->guru?->is($absensi->guru) === true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_GURU]);
    }

    public function update(User $user, Absensi $absensi): bool
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        return $user->hasRole(User::ROLE_GURU)
            && $user->guru?->is($absensi->guru) === true;
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function restore(User $user, Absensi $absensi): bool
    {
        return false;
    }

    public function forceDelete(User $user, Absensi $absensi): bool
    {
        return false;
    }
}
