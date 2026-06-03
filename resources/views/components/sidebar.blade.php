<aside x-data 
       @click.outside="sidebarOpen = false"
       :class="sidebarOpen ? 'w-64' : 'w-20'" 
       class="bg-[#0c2b4d] text-white flex-shrink-0 flex flex-col justify-between shadow-xl select-none transition-all duration-300 ease-in-out z-30">
    
    <div class="flex flex-col h-full overflow-y-auto overflow-x-hidden">
        <!-- Sidebar Header (Logo + Brand Name) -->
        <div class="h-16 flex items-center border-b border-blue-900/30 transition-all duration-300"
             :class="sidebarOpen ? 'justify-start px-6 gap-3' : 'justify-center px-0 gap-0'">
            <img src="{{ asset('assets/images/LOGO SMKN 1.png') }}" class="h-9 w-auto object-contain" alt="SMKN 1 Logo">
            <span x-show="sidebarOpen" 
                  x-transition:enter="transition ease-out duration-200" 
                  x-transition:enter-start="opacity-0" 
                  x-transition:enter-end="opacity-100"
                  class="font-bold text-sm tracking-wider uppercase whitespace-nowrap">
                S.T.O.V.I.A
            </span>
        </div>

        <!-- Navigation Menus -->
        <nav class="mt-6 px-4 space-y-2">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-blue-100/70 hover:bg-white/5 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                <svg class="w-5 h-5 text-current flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            </a>

            <!-- Dynamic menus based on roles -->
            @if (Auth::user()->role === 'admin')
                <div x-show="sidebarOpen" class="pt-4 pb-2 px-4 text-xs font-semibold text-blue-300/40 uppercase tracking-wider select-none whitespace-nowrap">
                    Admin Menu
                </div>
                <div x-show="!sidebarOpen" class="h-px bg-blue-900/30 my-4 mx-2"></div>

                <!-- Kelola Tahun Ajaran -->
                <a href="{{ route('admin.manage.tahun-ajarans.index') }}" 
                   class="flex items-center rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.manage.tahun-ajarans.*') ? 'bg-white/10 text-white font-semibold' : 'text-blue-100/70 hover:bg-white/5 hover:text-white' }}"
                   :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 text-current flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Kelola Tahun Ajaran</span>
                </a>

                <!-- Kelola Jurusan -->
                <a href="{{ route('admin.manage.jurusans.index') }}" 
                   class="flex items-center rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.manage.jurusans.*') ? 'bg-white/10 text-white font-semibold' : 'text-blue-100/70 hover:bg-white/5 hover:text-white' }}"
                   :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 text-current flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Kelola Jurusan</span>
                </a>
                
                <div x-data="{ kelolaOpen: false }" class="flex flex-col">
                    <button @click="kelolaOpen = !kelolaOpen" 
                            class="rounded-lg text-sm font-medium text-blue-100/70 hover:bg-white/5 hover:text-white transition-all duration-300 focus:outline-none"
                            :class="sidebarOpen 
                                ? 'w-full flex items-center justify-between px-4 py-3 gap-3' 
                                : 'w-12 mx-auto flex items-center justify-center py-3'">
                        <div class="flex items-center" :class="sidebarOpen ? 'gap-3' : 'gap-0'">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Kelola Pengguna</span>
                        </div>
                        <svg x-show="sidebarOpen" 
                             class="w-4 h-4 transition-transform duration-200" 
                             :class="kelolaOpen ? 'rotate-180' : ''" 
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="kelolaOpen && sidebarOpen" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="pl-12 mt-1 space-y-1 overflow-hidden flex flex-col text-left">
                        <a href="{{ route('admin.manage.admins.index') }}" class="block py-2 text-xs text-blue-100/60 hover:text-white transition-colors {{ request()->routeIs('admin.manage.admins.*') ? 'text-white font-semibold' : '' }}">
                            Kelola Admin
                        </a>
                        <a href="{{ route('admin.manage.gurus.index') }}" class="block py-2 text-xs text-blue-100/60 hover:text-white transition-colors {{ request()->routeIs('admin.manage.gurus.*') ? 'text-white font-semibold' : '' }}">
                            Kelola Guru
                        </a>
                        <a href="{{ route('admin.manage.murids.index') }}" class="block py-2 text-xs text-blue-100/60 hover:text-white transition-colors {{ request()->routeIs('admin.manage.murids.*') ? 'text-white font-semibold' : '' }}">
                            Kelola Murid
                        </a>
                    </div>
                </div>
            @elseif (Auth::user()->role === 'guru')
                <div x-show="sidebarOpen" class="pt-4 pb-2 px-4 text-xs font-semibold text-blue-300/40 uppercase tracking-wider select-none whitespace-nowrap">
                    Guru Menu
                </div>
                <div x-show="!sidebarOpen" class="h-px bg-blue-900/30 my-4 mx-2"></div>

                <a href="#" 
                   class="flex items-center rounded-lg text-sm font-medium text-blue-100/70 hover:bg-white/5 hover:text-white transition-all duration-300"
                   :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Bahan Ajar</span>
                </a>
            @elseif (Auth::user()->role === 'murid')
                <div x-show="sidebarOpen" class="pt-4 pb-2 px-4 text-xs font-semibold text-blue-300/40 uppercase tracking-wider select-none whitespace-nowrap">
                    Siswa Menu
                </div>
                <div x-show="!sidebarOpen" class="h-px bg-blue-900/30 my-4 mx-2"></div>

                <a href="#" 
                   class="flex items-center rounded-lg text-sm font-medium text-blue-100/70 hover:bg-white/5 hover:text-white transition-all duration-300"
                   :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Ujian / Tugas</span>
                </a>
            @endif
        </nav>
    </div>
</aside>
