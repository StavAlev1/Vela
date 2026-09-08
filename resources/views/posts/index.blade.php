<x-app-layout title="Posts">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Posts
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-end mb-8">
            @can('create', App\Models\Post::class)
                <a href="{{ route('posts.create') }}"
                    class="bg-brand-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-brand-700 transition">
                    + New Post
                </a>
            @endcan
        </div>

        <div x-data="postSearch('{{ request('q') }}', '{{ route('posts.index') }}', '{{ request('category') }}')">
            <div class="mb-8 flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" x-model.debounce.400ms="q" placeholder="Search posts by title or content..."
                        class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">

                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <span x-show="loading" x-cloak
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                        Searching…
                    </span>
                </div>

                <select x-model="category"
                    class="px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition sm:w-56">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="posts-results" @click="paginate($event)">
                @include('posts.partials.results')
            </div>
        </div>

    </div>
</x-app-layout>
