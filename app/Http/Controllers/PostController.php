<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::with(['user', 'category'])
            ->withCount('comments')
            ->search($request->query('q'))
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category_id', $request->query('category'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        if ($request->ajax()) {
            return view('posts.partials.results', compact('posts'));
        }

        return view('posts.index', compact('posts', 'categories'));
    }

    public function create(): View
    {
        // create() is already checked inside StorePostRequest::authorize(),
        // but since this method just shows the form (no Form Request involved
        // here), we check it explicitly too.
        $this->authorize('create', Post::class);

        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $this->extractMetadata($request->validated());

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post = Post::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        ActivityLog::record('post.created', "Created post \"{$post->title}\".", $post);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post): View
    {
        $this->authorize('view', $post);

        $post->load(['comments.user', 'user', 'category']);

        // Count each visitor once per browser session rather than once per
        // page load, so refreshing the page doesn't inflate the count.
        $viewed = session()->get('viewed_posts', []);

        if (! in_array($post->id, $viewed, true)) {
            $post->increment('views');
            $viewed[] = $post->id;
            session()->put('viewed_posts', $viewed);
        }

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        // No Form Request involved in showing the edit form, so we check
        // the policy directly here.
        $this->authorize('update', $post);

        $categories = Category::orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $validated = $this->extractMetadata($request->validated(), $post);

        if ($request->hasFile('featured_image')) {
            // Delete the old image if one exists on our own disk — nothing
            // to clean up when it was an external URL (e.g. from the
            // backfill command).
            if ($post->featured_image && ! $post->hasExternalImage()) {
                Storage::disk('public')->delete($post->featured_image);
            }

            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update($validated);

        ActivityLog::record('post.updated', "Updated post \"{$post->title}\".", $post);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        // No Form Request for destroy, so check the policy directly.
        $this->authorize('delete', $post);

        $title = $post->title;

        $post->delete(); // soft delete, since Post uses SoftDeletes

        ActivityLog::record('post.deleted', "Deleted post \"{$title}\".", $post);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    public function forceDestroy(Post $post): RedirectResponse
    {
        $this->authorize('forceDelete', $post);

        $title = $post->title;

        // Comments are linked via a polymorphic relation (no real foreign
        // key possible there), so nothing at the database level would clean
        // them up on its own — delete them explicitly before the post is
        // gone, the same way Admin\UserController::destroy() already does
        // for a deleted user's posts. (See also: comments:prune-orphaned,
        // for any orphans left over from before this fix.)
        $post->comments()->delete();

        $post->forceDelete(); // triggers the booted() event

        ActivityLog::record('post.force_deleted', "Permanently deleted post \"{$title}\".");

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post permanently deleted.');
    }

    public function restore(Post $post): RedirectResponse
    {
        $this->authorize('restore', $post);

        $post->restore();

        ActivityLog::record('post.restored', "Restored post \"{$post->title}\".", $post);

        return redirect()
            ->route('posts.trashed')
            ->with('success', 'Post restored successfully.');
    }

    /**
     * Pull meta_title/meta_description out of the validated payload and fold
     * them into the `metadata` JSON column, preserving any other keys
     * already stored there (e.g. when editing).
     *
     * @param  array<string, mixed>  $validated
     */
    private function extractMetadata(array $validated, ?Post $post = null): array
    {
        $metaTitle = $validated['meta_title'] ?? null;
        $metaDescription = $validated['meta_description'] ?? null;
        unset($validated['meta_title'], $validated['meta_description']);

        $metadata = $post?->metadata ?? [];
        $metadata['meta_title'] = $metaTitle ?: null;
        $metadata['meta_description'] = $metaDescription ?: null;
        $metadata = array_filter($metadata, fn ($value) => ! is_null($value));

        $validated['metadata'] = $metadata ?: null;

        return $validated;
    }
}
