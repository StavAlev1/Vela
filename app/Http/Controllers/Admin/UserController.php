<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function promoteToEditor(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manageRole', $user);

        $user->syncRoles(['editor']); // replaces 'user' role with 'editor'

        return back()->with('success', "{$user->name} is now an editor.");
    }

    public function demoteToUser(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manageRole', $user);

        $user->syncRoles(['user']);

        return back()->with('success', "{$user->name} is now a regular user.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        DB::transaction(function () use ($user) {
            // Delete each post properly through Eloquent, so model events
            // (image cleanup, comment cleanup) actually fire
            $user->posts()->get()->each(function ($post) {
                $post->comments()->delete();

                if ($post->featured_image) {
                    Storage::disk('public')->delete($post->featured_image);
                }

                $post->forceDelete();
            });

            // Delete comments this user wrote on OTHER people's posts
            $user->commentsWritten()->delete();

            // Delete their avatar file and profile
            if ($user->profile?->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $user->profile()->delete();

            $user->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$user->name} and all their content have been deleted.");
    }
}
