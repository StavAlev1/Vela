<x-app-layout title="New Category">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            New Category
        </h2>
    </x-slot>

    <div class="py-8 max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" autofocus
                        placeholder="e.g. Tutorials"
                        class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition">
                    @error('name')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 py-3 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition">
                        Create Category
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
