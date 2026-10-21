<?php

namespace App\Policies;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, Post $post): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Post $post): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        // Operator tidak boleh mengubah status ke Published
        if (! $user->isAdmin() && $post->status === PostStatus::Review) {
            return false;
        }

        return true;
    }

    public function updateStatus(User $user, Post $post, PostStatus $status): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        return in_array($status->value, PostStatus::moderatable(), true);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->viewAny($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
