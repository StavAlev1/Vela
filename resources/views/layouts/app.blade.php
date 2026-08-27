<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-gradient-to-br from-indigo-500 to-purple-600 text-gray-900 flex flex-col">

    {{-- Navbar --}}
    <header class="bg-white shadow-sm">
        <nav class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-lg font-bold text-indigo-600">
                {{ config('app.name', 'Laravel') }}
            </a>

            <div class="flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="{{ url('/') }}" class="hover:text-indigo-600 transition">Home</a>
                <a href="{{ route('contact') }}" class="hover:text-indigo-600 transition">Contact</a>
            </div>
        </nav>
    </header>

    {{-- Flash messages (global, available on every page) --}}
    <div class="max-w-6xl mx-auto w-full px-6">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mt-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mt-4">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Main content --}}
    <main class="flex-1 max-w-6xl mx-auto w-full px-6 py-10">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-6xl mx-auto px-6 py-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
