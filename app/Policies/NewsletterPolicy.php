<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Newsletter;
use App\Models\User;

class NewsletterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, Newsletter $newsletter): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Newsletter $newsletter): bool
    {
        return $this->viewAny($user);
    }

    public function send(User $user, Newsletter $newsletter): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Newsletter $newsletter): bool
    {
        return $this->viewAny($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, Newsletter $newsletter): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Newsletter $newsletter): bool
    {
        return $user->isAdmin();
    }
}
