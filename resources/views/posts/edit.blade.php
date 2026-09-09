<x-app-layout title="Edit Post">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Post
        </h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}"
                           class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition">
                    @error('title')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Category</label>
                    <select id="category_id" name="category_id"
                            class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition">
                        <option value="">Uncategorized</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700 mb-1.5">Content</label>
                    <textarea id="content" name="content" rows="8"
                              class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition resize-y">{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                @if ($post->featured_image_url)
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1.5">Current Image</p>
                        <img src="{{ $post->featured_image_url }}" class="w-40 h-28 object-cover rounded-lg">
                    </div>
                @endif

                <div>
                    <label for="featured_image" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Replace Image (optional)
                    </label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*"
                           class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-600 file:text-sm file:font-semibold hover:file:bg-brand-100 transition">
                    @error('featured_image')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <details class="group border border-gray-200 rounded-lg" @if($post->metadata) open @endif>
                    <summary class="cursor-pointer select-none px-4 py-3 text-sm font-semibold text-gray-700">
                        SEO settings <span class="text-gray-400 font-normal">(optional)</span>
                    </summary>
                    <div class="px-4 pb-4 space-y-4 border-t border-gray-100 pt-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                SEO title
                            </label>
                            <input type="text" id="meta_title" name="meta_title"
                                   value="{{ old('meta_title', data_get($post->metadata, 'meta_title')) }}"
                                   placeholder="Defaults to the post title"
                                   class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition">
                            @error('meta_title')
                                <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                SEO description
                            </label>
                            <textarea id="meta_description" name="meta_description" rows="2" maxlength="255"
                                      placeholder="Defaults to an excerpt of the content"
                                      class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition resize-y">{{ old('meta_description', data_get($post->metadata, 'meta_description')) }}</textarea>
                            @error('meta_description')
                                <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </details>

                <label class="flex items-center gap-2.5 text-sm text-gray-700 select-none">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published))
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-400">
                    Published
                    <span class="text-gray-400">— uncheck to pull it back to a draft only you (and admins) can see.</span>
                </label>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-3 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition">
                        Save Changes
                    </button>
                    <a href="{{ route('posts.show', $post) }}"
                       class="px-6 py-3 border border-gray-300 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
