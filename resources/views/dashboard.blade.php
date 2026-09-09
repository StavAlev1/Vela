<x-app-layout title="Dashboard">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Welcome --}}
        <div class="bg-white rounded-2xl shadow-sm p-8 flex items-center gap-5">
            <img src="{{ Auth::user()->profile->avatar_url }}" alt="{{ Auth::user()->name }}"
                class="w-16 h-16 rounded-full object-cover">
            <div>
                <h3 class="text-xl font-bold text-gray-800">
                    Welcome back, {{ Auth::user()->name }} 👋
                </h3>
                <p class="text-sm text-gray-500">Here's what's happening with your content.</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid gap-6 sm:grid-cols-3">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-sm text-gray-400 mb-1">Your Posts</p>
                <p class="text-3xl font-bold text-gray-800">{{ $postCount }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-sm text-gray-400 mb-1">Comments to your Posts</p>
                <p class="text-3xl font-bold text-gray-800">{{ $commentCount }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-sm text-gray-400 mb-1">Total Site Posts</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalPosts }}</p>
            </div>
        </div>

        {{-- Recent posts --}}
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-800">Your Recent Posts</h3>
                @can('create', \App\Models\Post::class)
                    <a href="{{ route('posts.create') }}" class="text-sm text-brand-600 hover:underline">+ New Post</a>
                @endcan
            </div>

            @forelse ($recentPosts as $post)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <a href="{{ route('posts.show', $post) }}"
                            class="font-medium text-gray-800 hover:text-brand-600">
                            {{ $post->title }}
                        </a>
                        <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('posts.edit', $post) }}"
                        class="text-sm text-gray-500 hover:text-brand-600">Edit</a>
                </div>
            @empty
                <p class="text-sm text-gray-400">You haven't written any posts yet.</p>
            @endforelse
        </div>

        {{-- Admin-only section --}}
        @if (Auth::user()->hasRole('admin'))
            <div class="bg-brand-50 border border-brand-100 rounded-2xl p-8 space-y-8">
                <div>
                    <h3 class="text-lg font-bold text-brand-900 mb-4">Admin Overview</h3>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <p class="text-sm text-brand-500 mb-1">Total Users</p>
                            <p class="text-2xl font-bold text-brand-900">{{ $totalUsers }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-brand-500 mb-1">Total Comments (site-wide)</p>
                            <p class="text-2xl font-bold text-brand-900">{{ $totalComments }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-brand-500 mb-1">Categories</p>
                            <p class="text-2xl font-bold text-brand-900">{{ $totalCategories }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-brand-500 mb-1">Published / Draft</p>
                            <p class="text-2xl font-bold text-brand-900">{{ $publishedCount }} / {{ $draftCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-8 lg:grid-cols-2">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-bold text-brand-900">Most Viewed Posts</h4>
                        </div>
                        <div class="bg-white rounded-xl divide-y divide-gray-100">
                            @forelse ($mostViewedPosts as $post)
                                <a href="{{ route('posts.show', $post) }}"
                                    class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                                    <span class="text-sm text-gray-700 truncate pr-3">{{ $post->title }}</span>
                                    <span class="text-xs text-gray-400 shrink-0">👁 {{ $post->views }}</span>
                                </a>
                            @empty
                                <p class="px-4 py-6 text-sm text-gray-400 text-center">No views recorded yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-bold text-brand-900">Recent Signups</h4>
                            <a href="{{ route('admin.users.index') }}" class="text-xs text-brand-600 hover:underline">
                                View all
                            </a>
                        </div>
                        <div class="bg-white rounded-xl divide-y divide-gray-100">
                            @forelse ($recentSignups as $signup)
                                <div class="flex items-center justify-between px-4 py-3">
                                    <span class="text-sm text-gray-700 truncate pr-3">{{ $signup->name }}</span>
                                    <span class="text-xs text-gray-400 shrink-0">{{ $signup->created_at->diffForHumans() }}</span>
                                </div>
                            @empty
                                <p class="px-4 py-6 text-sm text-gray-400 text-center">No signups yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.activity.index') }}" class="text-sm text-brand-600 hover:underline">
                    View full activity log →
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
