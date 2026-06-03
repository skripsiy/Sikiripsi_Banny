<header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 sticky top-0 z-20">
    <!-- Left Side: Toggle Button + Page Title -->
    <div class="flex items-center gap-4">
        <!-- Toggle Button -->
        <button @click="sidebarOpen = !sidebarOpen" 
                class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-lg transition-colors focus:outline-none"
                aria-label="Toggle Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        
        <!-- Title Slot -->
        <div class="text-lg font-semibold text-gray-800">
            {{ $slot }}
        </div>
    </div>

    <!-- Right Side: User Dropdown -->
    <div class="flex items-center">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <div class="text-right mr-2 select-none">
                        <div class="font-semibold text-gray-700 text-sm leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-400 font-light mt-0.5 leading-tight">{{ Auth::user()->email }}</div>
                    </div>
                    <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

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
