<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create a New Post
        </h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                           placeholder="Give your post a title"
                           class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition">
                    @error('title')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700 mb-1.5">Content</label>
                    <textarea id="content" name="content" rows="8"
                              placeholder="Write your post..."
                              class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition resize-y">{{ old('content') }}</textarea>
                    @error('content')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="featured_image" class="block text-sm font-semibold text-gray-700 mb-1.5">Featured Image</label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*"
                           class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-600 file:text-sm file:font-semibold hover:file:bg-brand-100 transition">
                    @error('featured_image')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-3 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition">
                        Publish Post
                    </button>
                    <a href="{{ route('posts.index') }}"
                       class="px-6 py-3 border border-gray-300 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>