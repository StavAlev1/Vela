@if ($posts->isEmpty())
    <div class="text-center py-20 text-gray-400">
        @if (request('q'))
            <p class="text-lg">No posts found for "{{ request('q') }}".</p>
        @else
            <p class="text-lg">No posts yet.</p>
        @endif
    </div>
@else
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <a href="{{ route('posts.show', $post) }}"
                class="bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden group">

                @if ($post->featured_image_url)
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}"
                        class="w-full h-44 object-cover group-hover:opacity-90 transition">
                @else
                    <div class="w-full h-44 bg-gray-100 flex items-center justify-center text-gray-300">
                        No image
                    </div>
                @endif

                <div class="p-5">
                    @if ($post->category)
                        <span class="inline-block mb-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-50 text-brand-600">
                            {{ $post->category->name }}
                        </span>
                    @endif

                    <h2 class="font-semibold text-gray-800 text-lg mb-1.5 line-clamp-1">
                        {{ $post->title }}
                    </h2>
                    <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                        {{ Str::limit($post->content, 100) }}
                    </p>

                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span>{{ $post->user->name }}</span>
                        <span class="flex items-center gap-3">
                            <span class="flex items-center gap-1">👁 {{ $post->views }}</span>
                            <span class="flex items-center gap-1">💬 {{ $post->comments_count }}</span>
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-10">
        {{ $posts->links() }}
    </div>
@endif
