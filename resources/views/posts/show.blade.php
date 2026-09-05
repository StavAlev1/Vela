<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <article class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
            @if ($post->featured_image_url)
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}"
                     class="w-full h-72 object-cover">
            @endif

            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm text-gray-400">
                        By <span class="font-medium text-gray-600">{{ $post->user->name }}</span>
                        · {{ $post->created_at->diffForHumans() }}
                    </div>

                    <div class="flex gap-3 text-sm">
                        @can('update', $post)
                            <a href="{{ route('posts.edit', $post) }}" class="text-blue-600 hover:underline">Edit</a>
                        @endcan

                        @can('delete', $post)
                            <form method="POST" action="{{ route('posts.destroy', $post) }}"
                                  onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    {{ $post->content }}
                </div>
            </div>
        </article>

        {{-- Comments --}}
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">
                Comments ({{ $post->comments->count() }})
            </h2>

            @auth
                <form method="POST" action="{{ route('comments.store', $post) }}" class="mb-8">
                    @csrf
                    <textarea name="body" rows="3" placeholder="Add a comment..."
                              class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition resize-y"></textarea>
                    @error('body')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror

                    <button type="submit"
                            class="mt-3 px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                        Post Comment
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-400 mb-8">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Log in</a> to leave a comment.
                </p>
            @endauth

            <div class="space-y-5">
                @forelse ($post->comments as $comment)
                    <div class="flex gap-3 border-b border-gray-100 pb-5 last:border-0">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-semibold shrink-0">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-800">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>

                                @can('delete', $comment)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                          onsubmit="return confirm('Delete this comment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                                    </form>
                                @endcan
                            </div>
                            <p class="text-sm text-gray-600">{{ $comment->body }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No comments yet. Be the first!</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>