<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function isProductOwner(User $user): bool
    {
        return $user->role === 'product_owner';
    }
}
