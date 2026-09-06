<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Vela') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50">

    <div class="min-h-screen flex flex-col">

        {{-- Simple top nav for guests --}}
        <header class="max-w-7xl mx-auto w-full px-6 py-6 flex items-center justify-between">
            <x-application-logo class="h-9 w-auto fill-current text-gray-800" />

            <div class="flex items-center gap-4 text-sm font-medium">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-brand-600 transition">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                    class="bg-brand-600 text-white px-4 py-2 rounded-lg hover:bg-brand-700 transition">
                    Get Started
                </a>
            </div>
        </header>

        {{-- Hero --}}
        <main class="flex-1 flex items-center">
            <div class="max-w-4xl mx-auto px-6 text-center py-20">
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-800 mb-6 leading-tight">
                    Write. Share. Discuss.
                </h1>
                <p class="text-lg text-gray-500 mb-10 max-w-xl mx-auto">
                    A place to publish posts, join the conversation, and connect with people who care about the same
                    things you do.
                </p>

                <div class="flex items-center justify-center gap-4">
                    
                    <a href="{{ route('login') }}"
                        class="bg-brand-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-brand-700 transition">
                        Log In
                    </a>
                    <a href="{{ route('register') }}"
                        class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Create an Account
                    </a>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-6 py-6 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'Vela') }}. All rights reserved.
            </div>
        </footer>
    </div>

</body>

</html>
