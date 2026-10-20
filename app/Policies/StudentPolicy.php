<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator, UserRole::Pelatih);
    }

    public function view(User $user, Student $student): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }
}
