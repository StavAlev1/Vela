<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Every post is publicly viewable in this app today (see PostPolicy),
     * so the sitemap mirrors that: every non-trashed post, plus the static
     * pages. If a real draft/published workflow gets wired up later, add
     * ->published() here to match.
     */
    public function index(): Response
    {
        $posts = Post::orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);

        $xml = view('sitemap', compact('posts'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
