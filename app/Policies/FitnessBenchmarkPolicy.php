<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\FitnessBenchmark;
use App\Models\User;

class FitnessBenchmarkPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator, UserRole::Pelatih);
    }

    public function view(User $user, FitnessBenchmark $benchmark): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function update(User $user, FitnessBenchmark $benchmark): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function delete(User $user, FitnessBenchmark $benchmark): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, FitnessBenchmark $benchmark): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, FitnessBenchmark $benchmark): bool
    {
        return $user->isAdmin();
    }
}
