<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Users
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                    <tr>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Joined</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}"
                                         class="w-9 h-9 rounded-full object-cover">
                                    <span class="font-medium text-gray-800">{{ $user->name }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>

                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ match(true) {
                                        $user->hasRole('admin') => 'bg-indigo-100 text-indigo-700',
                                        $user->hasRole('editor') => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ $user->roles->pluck('name')->join(', ') ?: 'none' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-400">
                                {{ $user->created_at->format('M j, Y') }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    @if ($user->hasRole('admin'))
                                        <span class="text-gray-300 text-xs">Protected</span>
                                    @else
                                        @if ($user->hasRole('editor'))
                                            <form method="POST" action="{{ route('admin.users.demote', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-gray-500 hover:text-gray-700 hover:underline text-sm">
                                                    Demote
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.promote', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-indigo-600 hover:underline text-sm">
                                                    Promote to Editor
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button"
                                                onclick="document.getElementById('delete-modal-{{ $user->id }}').classList.remove('hidden')"
                                                class="text-red-500 hover:underline text-sm">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-6 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- Delete confirmation modals — one per user, hidden by default --}}
    @foreach ($users as $user)
        @unless ($user->hasRole('admin'))
            <div id="delete-modal-{{ $user->id }}" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4">
                <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Delete {{ $user->name }}?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        This permanently deletes their account, posts, and comments. This cannot be undone.
                        Type <span class="font-semibold text-gray-700">{{ $user->name }}</span> to confirm.
                    </p>

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          x-data="{ confirmText: '' }">
                        @csrf
                        @method('DELETE')

                        <input type="text" x-model="confirmText" placeholder="Type the name to confirm"
                               class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">

                        <div class="flex gap-3">
                            <button type="submit" :disabled="confirmText !== '{{ $user->name }}'"
                                    :class="confirmText === '{{ $user->name }}' ? 'bg-red-600 hover:bg-red-700' : 'bg-red-300 cursor-not-allowed'"
                                    class="flex-1 py-2.5 text-white text-sm font-semibold rounded-lg transition">
                                Delete Permanently
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('delete-modal-{{ $user->id }}').classList.add('hidden')"
                                    class="px-5 py-2.5 border border-gray-300 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endunless
    @endforeach
</x-app-layout>