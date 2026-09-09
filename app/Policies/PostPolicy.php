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

    /**
     * A published post is visible to anyone; an unpublished draft is only
     * visible to its own author or an admin (so a draft link can't just be
     * shared/guessed at while it's still being worked on).
     */
    public function view(User $user, Post $post): bool
    {
        return $post->is_published || $user->id === $post->user_id || $user->hasRole('admin');
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
