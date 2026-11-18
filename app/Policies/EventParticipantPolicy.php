<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\EventParticipant;
use App\Models\User;

class EventParticipantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, EventParticipant $eventParticipant): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, EventParticipant $eventParticipant): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, EventParticipant $eventParticipant): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
