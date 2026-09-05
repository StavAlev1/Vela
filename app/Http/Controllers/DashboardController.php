<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $data = [
            'postCount' => $user->posts()->count(),
            'commentCount' => $user->commentsReceived()->count(),
            'totalPosts' => Post::count(),
            'recentPosts' => $user->posts()->latest()->take(5)->get(),
        ];

        if ($user->hasRole('admin')) {
            $data['totalUsers'] = User::count();
            $data['totalComments'] = Comment::count();
        }

        return view('dashboard', $data);
    }
}