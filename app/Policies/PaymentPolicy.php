<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Payment $payment): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }
}
