<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        // viewAny check — since your PostPolicy allows any logged-in user,
        // this is optional right now, but wiring it in keeps things consistent
        // and future-proofs it if you tighten the rule later.
        $this->authorize('viewAny', Post::class);

        $posts = Post::with('user')
            ->withCount('comments')
            ->latest()
            ->paginate(10);

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
        // No $this->authorize() needed here — StorePostRequest already
        // ran PostPolicy::create() before this method executed.
        $post = Post::create([
            ...$request->validated(),
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
        // No $this->authorize() needed here — UpdatePostRequest already
        // ran PostPolicy::update() before this method executed.
        $post->update($request->validated());

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
}
