<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\BeltPromotion;
use App\Models\User;

class BeltPromotionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator, UserRole::Pelatih);
    }

    public function view(User $user, BeltPromotion $beltPromotion): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, BeltPromotion $beltPromotion): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, BeltPromotion $beltPromotion): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin, UserRole::Operator);
    }

    public function restore(User $user, BeltPromotion $beltPromotion): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, BeltPromotion $beltPromotion): bool
    {
        return $user->isAdmin();
    }
}
