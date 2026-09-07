<?php

namespace App\Http\Controllers;

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

        $posts = Post::with('user')
            ->withCount('comments')
            ->search($request->query('q'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('posts.partials.results', compact('posts'));
        }

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        // create() is already checked inside StorePostRequest::authorize(),
        // but since this method just shows the form (no Form Request involved
        // here), we check it explicitly too.
        $this->authorize('create', Post::class);

        return view('posts.create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post = Post::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post): View
    {
        $this->authorize('view', $post);

        $post->load(['comments.user', 'user']);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        // No Form Request involved in showing the edit form, so we check
        // the policy directly here.
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('featured_image')) {
            // delete old image if one exists
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }

            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        // No Form Request for destroy, so check the policy directly.
        $this->authorize('delete', $post);

        $post->delete(); // soft delete, since Post uses SoftDeletes

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    public function forceDestroy(Post $post): RedirectResponse
    {
        $this->authorize('forceDelete', $post);

        $post->forceDelete(); // triggers the booted() event

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post permanently deleted.');
    }

    public function restore(Post $post): RedirectResponse
    {
        $this->authorize('restore', $post);

        $post->restore();

        return redirect()
            ->route('posts.trashed')
            ->with('success', 'Post restored successfully.');
    }
}
