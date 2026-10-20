<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\StudentPerformance;
use App\Models\User;

class StudentPerformancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator, UserRole::Pelatih);
    }

    public function view(User $user, StudentPerformance $performance): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, StudentPerformance $performance): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, StudentPerformance $performance): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function restore(User $user, StudentPerformance $performance): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, StudentPerformance $performance): bool
    {
        return $user->isAdmin();
    }
}
