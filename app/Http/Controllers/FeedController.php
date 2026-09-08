<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function index(): Response
    {
        $posts = Post::with('user')
            ->latest()
            ->take(20)
            ->get();

        $xml = view('feed', compact('posts'))->render();

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
    }
}
