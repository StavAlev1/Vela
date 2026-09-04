@extends('layouts.app')

@section('title', 'New Post')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Create a New Post</h1>

        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}"
                       placeholder="Give your post a title"
                       class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                @error('title')
                    <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-1.5">Content</label>
                <textarea id="content" name="content" rows="8"
                          placeholder="Write your post..."
                          class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition resize-y">{{ old('content') }}</textarea>
                @error('content')
                    <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="featured_image" class="block text-sm font-semibold text-gray-700 mb-1.5">Featured Image</label>
                <input type="file" id="featured_image" name="featured_image" accept="image/*"
                       class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm file:font-semibold hover:file:bg-indigo-100 transition">
                @error('featured_image')
                    <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                    Publish Post
                </button>
                <a href="{{ route('posts.index') }}"
                   class="px-6 py-3 border border-gray-300 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection