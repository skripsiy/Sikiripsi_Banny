<header class="h-16 backdrop-blur-md border-b border-gray-100/50 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20">
    <!-- Left Side: Hamburger Menu (Mobile/Tablet only) -->
    <div class="flex items-center gap-3">
        <button @click.stop="sidebarOpen = !sidebarOpen"
                class="lg:hidden inline-flex items-center justify-center p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition-all duration-150"
                aria-label="Toggle sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <!-- Header title for mobile -->
        <h1 class="text-sm font-bold text-gray-800 truncate lg:hidden max-w-[200px] sm:max-w-xs">
            {{ $slot }}
        </h1>
    </div>

    <!-- Right Side: User Dropdown -->
    <div class="flex items-center">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-2 sm:px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-transparent hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <div class="text-right mr-2 select-none hidden sm:block">
                        <div class="font-semibold text-gray-700 text-sm leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-400 font-light mt-0.5 leading-tight">{{ Auth::user()->email }}</div>
                    </div>
                    <!-- Mobile: show avatar circle instead -->
                    <div class="sm:hidden w-8 h-8 rounded-full bg-[#0c2b4d] text-white flex items-center justify-center text-xs font-bold mr-1">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
