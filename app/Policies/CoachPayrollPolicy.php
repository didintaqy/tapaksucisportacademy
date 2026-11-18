<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\CoachPayroll;
use App\Models\User;

class CoachPayrollPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, CoachPayroll $coachPayroll): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, CoachPayroll $coachPayroll): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, CoachPayroll $coachPayroll): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
