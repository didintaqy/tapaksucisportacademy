<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Coach;
use App\Models\User;

class CoachPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, Coach $coach): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Coach $coach): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Coach $coach): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
