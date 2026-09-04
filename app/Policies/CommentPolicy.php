<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Any logged-in user can comment — checked via the route/controller,
     * not usually needed as a full policy method, but included for consistency.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the comment's author, or an editor/admin, can delete it.
     * This lets moderators remove abusive/spam comments on posts they don't own.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->hasAnyRole(['editor', 'admin']);
    }
}