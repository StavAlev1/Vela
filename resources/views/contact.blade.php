@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-10 mx-auto">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Get in Touch</h1>
            <p class="text-sm text-gray-500">We'd love to hear from you. Send us a message below.</p>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-5">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Your full name"
                    required
                    class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition"
                >
                @error('name')
                    <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    required
                    class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition"
                >
                @error('email')
                    <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-1.5">Message</label>
                <textarea
                    id="message"
                    name="message"
                    rows="5"
                    placeholder="Write your message here..."
                    class="w-full px-3.5 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition resize-y"
                >{{ old('message') }}</textarea>
                @error('message')
                    <span class="block text-red-500 text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full py-3.5 bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-purple-400/40 active:translate-y-0 transition-all duration-150"
            >
                Send Message
            </button>
        </form>
    </div>
@endsection