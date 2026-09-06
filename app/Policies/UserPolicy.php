<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only admins can view the user management list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Only admins can change another user's role.
     */
    public function manageRole(User $user, User $targetUser): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Only admins can delete users, and never themselves or another admin
     * (prevents accidental lockouts or admins removing each other).
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->hasRole('admin')
            && $user->id !== $targetUser->id
            && ! $targetUser->hasRole('admin');
    }
}
