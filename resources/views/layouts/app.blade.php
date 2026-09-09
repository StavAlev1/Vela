<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('images/vela-icon.png') }}">
        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- SEO / Open Graph -->
        @php
            $metaDescription = $description ?? 'Vela — write, tag, and share your posts.';
        @endphp
        <meta name="description" content="{{ $metaDescription }}">
        <meta property="og:site_name" content="{{ config('app.name', 'Laravel') }}">
        <meta property="og:type" content="article">
        <meta property="og:title" content="{{ $title ?? config('app.name', 'Laravel') }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @isset($ogImage)
            <meta property="og:image" content="{{ $ogImage }}">
        @endisset
        <meta name="twitter:card" content="{{ isset($ogImage) ? 'summary_large_image' : 'summary' }}">

        <!-- Feeds -->
        <link rel="alternate" type="application/rss+xml" title="{{ config('app.name', 'Laravel') }} Feed" href="{{ route('feed') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Disables a form's submit button(s) the instant it's submitted, so
             a fast double-click can't fire the same POST twice (e.g. clicking
             "Save Post" twice used to create the same post twice). Plain
             inline script rather than part of the Vite bundle — it's small
             enough that it isn't worth a build step, and this way it takes
             effect immediately on every page, with nothing to compile. -->
        <script>
            document.addEventListener('submit', (event) => {
                // A form's own onsubmit="return confirm(...)" (Delete/Restore/
                // Force-delete forms) runs before this, so if the user clicked
                // Cancel the event is already prevented — leave that button
                // alone, otherwise Cancel would permanently lock it out.
                if (event.defaultPrevented) return;

                event.submitter?.setAttribute('disabled', '');
            });
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash messages -->
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
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

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </div>
            </footer>
        </div>
    </body>
</html>
