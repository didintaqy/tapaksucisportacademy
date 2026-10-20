<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\TrainingSchedule;
use App\Models\User;

class TrainingSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator, UserRole::Pelatih);
    }

    public function view(User $user, TrainingSchedule $trainingSchedule): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }
        if ($user->isPelatih()) {
            return $trainingSchedule->coach_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function update(User $user, TrainingSchedule $trainingSchedule): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }
        if ($user->isPelatih()) {
            return $trainingSchedule->coach_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, TrainingSchedule $trainingSchedule): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
