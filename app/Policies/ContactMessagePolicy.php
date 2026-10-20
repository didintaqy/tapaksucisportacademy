<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, ContactMessage $message): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactMessage $message): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, ContactMessage $message): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, ContactMessage $message): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, ContactMessage $message): bool
    {
        return $user->isAdmin();
    }
}
