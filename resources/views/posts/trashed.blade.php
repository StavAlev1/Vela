<x-app-layout title="Trashed Posts">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Trashed Posts
            </h2>

            <a href="{{ route('posts.index') }}" class="text-sm text-brand-600 hover:underline">
                &larr; Back to Posts
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Everyone can see what's in the trash; only the post's owner or an
             admin can restore it, and only an admin can delete it forever
             (see PostPolicy::restore() / forceDelete()) — the buttons below
             just reflect that per-row. --}}
        <p class="text-sm text-gray-400 mb-6">
            Posts deleted within the last few moments still show up here until they're restored or permanently removed.
        </p>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            @if ($posts->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <p class="text-lg">Nothing in the trash.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Post</th>
                            <th class="px-6 py-3">Author</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Deleted</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr class="border-t border-gray-100">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($post->featured_image_url)
                                            <img src="{{ $post->featured_image_url }}" alt=""
                                                 class="w-10 h-10 rounded-lg object-cover shrink-0 grayscale">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gray-50 shrink-0"></div>
                                        @endif
                                        <span class="font-medium text-gray-800 line-clamp-1">{{ $post->title }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-gray-500">{{ $post->user->name ?? '—' }}</td>

                                <td class="px-6 py-4 text-gray-500">{{ $post->category->name ?? '—' }}</td>

                                <td class="px-6 py-4 text-gray-400">
                                    {{ $post->deleted_at->diffForHumans() }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('posts.show', $post) }}" class="text-gray-500 hover:underline text-sm">
                                            View
                                        </a>

                                        @can('restore', $post)
                                            <form method="POST" action="{{ route('posts.restore', $post) }}"
                                                  onsubmit="return confirm('Restore this post?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-brand-600 hover:underline text-sm">
                                                    Restore
                                                </button>
                                            </form>
                                        @endcan

                                        @can('forceDelete', $post)
                                            <button type="button"
                                                    onclick="document.getElementById('force-delete-modal-{{ $post->id }}').classList.remove('hidden')"
                                                    class="text-red-500 hover:underline text-sm">
                                                Delete Forever
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-6 border-t border-gray-100">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Permanent-delete confirmation modals — one per post, hidden by default --}}
    @foreach ($posts as $post)
        @can('forceDelete', $post)
            <div id="force-delete-modal-{{ $post->id }}" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4">
                <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Delete "{{ $post->title }}" forever?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        This permanently deletes the post and its comments. This cannot be undone.
                        Type <span class="font-semibold text-gray-700">{{ $post->title }}</span> to confirm.
                    </p>

                    <form method="POST" action="{{ route('posts.force-destroy', $post) }}"
                          x-data="{ confirmText: '' }">
                        @csrf
                        @method('DELETE')

                        <input type="text" x-model="confirmText" placeholder="Type the title to confirm"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">

                        <div class="flex gap-3">
                            <button type="submit" :disabled="confirmText !== '{{ $post->title }}'"
                                    :class="confirmText === '{{ $post->title }}' ? 'bg-red-600 hover:bg-red-700' : 'bg-red-300 cursor-not-allowed'"
                                    class="flex-1 py-2.5 text-white text-sm font-semibold rounded-lg transition">
                                Delete Permanently
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('force-delete-modal-{{ $post->id }}').classList.add('hidden')"
                                    class="px-5 py-2.5 border border-gray-300 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    @endforeach
</x-app-layout>
