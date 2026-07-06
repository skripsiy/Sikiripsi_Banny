<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2015-umd.js" defer></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 h-screen overflow-hidden"
          x-data="{
              sidebarOpen: window.innerWidth >= 1024,
              isMobile: window.innerWidth < 1024,
              handleResize() {
                  this.isMobile = window.innerWidth < 1024;
                  if (this.isMobile) {
                      this.sidebarOpen = false;
                  }
              }
          }"
          x-init="handleResize()"
          @resize.window.debounce.150ms="handleResize()">
        <div class="h-screen flex flex-row overflow-hidden">

            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen && isMobile"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="sidebar-backdrop lg:hidden"
                 @click="sidebarOpen = false"
                 style="display: none;"></div>

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
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
