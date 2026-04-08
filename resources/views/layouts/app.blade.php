<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if(app()->environment('local'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="stylesheet" href="{{ asset('build/assets/app-Bnxu1mYu.css') }}">
            <script type="module" src="{{ asset('build/assets/app-DeW0ZCOM.js') }}"></script>
        @endif
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <!-- Use the unified navigation -->
            @include('partials.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-sm border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 rounded-xl flex items-center justify-center">
                                <span class="text-white text-lg">🏠</span>
                            </div>
                            <div class="text-gray-800 dark:text-gray-200">{{ $header }}</div>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="bg-gray-50 dark:bg-gray-900">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
