<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function index(): Response
    {
        // Only published posts belong in a public RSS feed — a draft
        // shouldn't get syndicated to subscribers before it's even public.
        //
        // ->query() first, not Post::published() directly — see the
        // comment in SitemapController for why that matters here.
        $posts = Post::query()->published()
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        $xml = view('feed', compact('posts'))->render();

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
    }
}
