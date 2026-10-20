<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Gallery;
use App\Models\User;

class GalleryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, Gallery $gallery): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Gallery $gallery): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Gallery $gallery): bool
    {
        return $this->viewAny($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, Gallery $gallery): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Gallery $gallery): bool
    {
        return $user->isAdmin();
    }
}
