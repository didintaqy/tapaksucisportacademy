<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\TrainingSchedule;
use App\Models\User;

class AchievementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator, UserRole::Pelatih);
    }

    public function view(User $user, Achievement $achievement): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        if (! $user->isPelatih()) {
            return false;
        }

        if ($achievement->student_id === null || ! $achievement->student) {
            return true;
        }

        return TrainingSchedule::query()
            ->where('coach_id', $user->id)
            ->where('branch', $achievement->student->branch)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Achievement $achievement): bool
    {
        return $this->view($user, $achievement);
    }

    public function delete(User $user, Achievement $achievement): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function restore(User $user, Achievement $achievement): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Achievement $achievement): bool
    {
        return $user->isAdmin();
    }
}
