<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Author can update their own post, OR an editor/admin can update any post.
     * hasAnyRole() checks Spatie roles directly.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->hasAnyRole(['editor', 'admin']);
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->hasAnyRole(['editor', 'admin']);
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->hasAnyRole(['editor', 'admin']);
    }

    /**
     * Force delete is destructive and permanent — restrict to admins only.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->hasRole('admin');
    }
}