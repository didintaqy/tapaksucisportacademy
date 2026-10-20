<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\PaymentType;
use App\Models\User;

class PaymentTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function view(User $user, PaymentType $paymentType): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, PaymentType $paymentType): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, PaymentType $paymentType): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
