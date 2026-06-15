<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2015-umd.js" defer></script>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
        <div class="h-screen flex flex-row overflow-hidden">
            
            <!-- Sidebar Component -->
            <x-sidebar />

            <!-- Main Content Section -->
            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
                
                <!-- Topbar Component -->
                <x-topbar>
                    @isset($header)
                        {{ $header }}
                    @else
                        {{ __('Dashboard') }}
                    @endisset
                </x-topbar>

                <!-- Page Content -->
                <main class="flex-1 p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
