<x-app-layout title="Activity Log">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Activity Log
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <ul class="divide-y divide-gray-100">
                @forelse ($activities as $activity)
                    <li class="px-6 py-4 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold text-gray-800">
                                    {{ $activity->user?->name ?? 'System' }}
                                </span>
                                {{ $activity->description }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">{{ $activity->action }}</p>
                        </div>
                        <span class="text-xs text-gray-400 shrink-0" title="{{ $activity->created_at }}">
                            {{ $activity->created_at->diffForHumans() }}
                        </span>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-gray-400">
                        No activity recorded yet.
                    </li>
                @endforelse
            </ul>
        </div>

        {{ $activities->links() }}
    </div>
</x-app-layout>
