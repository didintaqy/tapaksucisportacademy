<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, Program $program): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Program $program): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Program $program): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Program $program): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Program $program): bool
    {
        return $user->isAdmin();
    }
}
