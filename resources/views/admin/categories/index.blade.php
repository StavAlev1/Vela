<x-app-layout title="Manage Categories">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Categories
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex items-center justify-end">
            <a href="{{ route('admin.categories.create') }}"
                class="bg-brand-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-brand-700 transition">
                + New Category
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Slug</th>
                        <th class="px-6 py-3">Posts</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $category->posts_count }}</td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                    class="text-brand-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                    class="inline"
                                    onsubmit="return confirm('Delete this category? Its posts will become uncategorized.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                No categories yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $categories->links() }}
    </div>
</x-app-layout>
