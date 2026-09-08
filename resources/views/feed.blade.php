<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name', 'Vela') }}</title>
        <link>{{ url('/') }}</link>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        <description>Latest posts from {{ config('app.name', 'Vela') }}</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        @if ($posts->isNotEmpty())
            <lastBuildDate>{{ $posts->first()->created_at->toRfc2822String() }}</lastBuildDate>
        @endif

        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('posts.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('posts.show', $post) }}</guid>
                <pubDate>{{ $post->created_at->toRfc2822String() }}</pubDate>
                <author>{{ $post->user->name }}</author>
                <description>
                    <![CDATA[{{ $post->meta_description }}]]>
                </description>
            </item>
        @endforeach
    </channel>
</rss>
