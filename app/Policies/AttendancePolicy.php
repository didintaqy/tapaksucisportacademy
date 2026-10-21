<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator, UserRole::Pelatih);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $user->isPelatih()
            && $attendance->trainingSchedule?->coach_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $this->view($user, $attendance);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function restore(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }
}
