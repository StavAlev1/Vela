<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a new comment on a Post.
     * (A separate method/route handles Video comments if needed later.)
     */
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $post->comments()->create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Comment added.');
    }

    /**
     * Delete a comment. Route model binding fetches the Comment directly,
     * regardless of whether its parent is a Post or Video.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        // Redirect back to whichever parent (Post or Video) owns this comment
        $redirectRoute = $comment->commentable instanceof Post
            ? route('posts.show', $comment->commentable)
            : route('videos.show', $comment->commentable);

        // Log it as moderation whenever someone other than the comment's own
        // author removes it (an admin/editor cleaning up someone else's comment).
        // Only Posts are commentable in practice today, so keep this scoped
        // to that case rather than assuming Video also has a ->title.
        if ($comment->user_id !== Auth::id() && $comment->commentable instanceof Post) {
            ActivityLog::record(
                'comment.moderated',
                "Removed a comment by {$comment->user->name} on \"{$comment->commentable->title}\".",
                $comment->commentable,
            );
        }

        $comment->delete();

        return redirect($redirectRoute)->with('success', 'Comment deleted.');
    }
}
