<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Contact Us
        </h2>
    </x-slot>

    <div class="py-8 max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-10">
            <p class="text-sm text-gray-500 text-center mb-8">
                We'd love to hear from you. Send us a message below.
            </p>

            <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           placeholder="Your full name" required
                           class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition">
                    @error('name')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           placeholder="you@example.com" required
                           class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition">
                    @error('email')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-semibold text-gray-700 mb-1.5">Message</label>
                    <textarea id="message" name="message" rows="5"
                              placeholder="Write your message here..." required
                              class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition resize-y">{{ old('message') }}</textarea>
                    @error('message')
                        <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full py-3.5 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</x-app-layout>