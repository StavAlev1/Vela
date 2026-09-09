<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Only published posts are publicly viewable (see PostPolicy::view()),
     * so the sitemap mirrors that — no point pointing search engines at a
     * draft's URL when they'd just get a 403 for it.
     */
    public function index(): Response
    {
        // ->query() first, not Post::published() directly — an attribute
        // scope is a real method named published(), so calling it straight
        // off the model class goes through Model::__callStatic() as a plain
        // method call (0 args passed, 1 required) instead of through
        // Builder's scope-forwarding, which is what auto-injects $query.
        $posts = Post::query()->published()->orderBy('updated_at', 'desc')->get(['slug', 'updated_at']);

        $xml = view('sitemap', compact('posts'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
