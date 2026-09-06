<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // anyone can browse posts
    }

    public function view(User $user, Post $post): bool
    {
        return true; // anyone can view a post
    }

    /**
     * Only editors/admins can create posts — regular users cannot.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'admin']);
    }

    /**
     * Author can update their own post, or an admin can update any post.
     * (Editors no longer need to moderate OTHER editors' posts unless you want that —
     * see note below.)
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->hasRole('admin');
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->hasRole('admin');
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->hasRole('admin');
    }
}
