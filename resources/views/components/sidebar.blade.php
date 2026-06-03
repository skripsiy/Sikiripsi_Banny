<aside x-data="{ 
            init() {
                // Keep User Management submenu open if any child route is active
                if ({{ request()->routeIs('admin.manage.admins.*') || request()->routeIs('admin.manage.gurus.*') || request()->routeIs('admin.manage.murids.*') ? 'true' : 'false' }}) {
                    this.kelolaOpen = true;
                }
            },
            kelolaOpen: false
       }"
       @click.outside="sidebarOpen = false"
       :class="sidebarOpen ? 'w-64' : 'w-20'" 
       class="my-4 ml-4 mr-4 h-[calc(100vh-2rem)] bg-[#1b61d1] text-white flex-shrink-0 flex flex-col shadow-2xl select-none transition-all duration-300 ease-in-out z-30 font-sans rounded-[24px] overflow-hidden">
    
    <!-- Top Scrollable Section -->
    <div class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
        <!-- Sidebar Collapse / Expand Button -->
        <div class="h-16 flex items-center border-b border-white/10 cursor-pointer hover:bg-white/5 transition-all duration-300"
             @click="sidebarOpen = !sidebarOpen"
             :class="sidebarOpen ? 'px-6 gap-3 justify-start' : 'justify-center px-0 gap-0'">
            <svg class="w-4 h-4 text-blue-100 flex-shrink-0 transition-transform duration-300" 
                 :class="!sidebarOpen ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span x-show="sidebarOpen" 
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm font-semibold text-blue-100/90 whitespace-nowrap">Collapse</span>
        </div>

        <!-- Brand Logo / Avatar Section -->
        <!-- Expanded State -->
        <div class="flex flex-col items-center justify-center py-8 border-b border-white/10" 
             x-show="sidebarOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <img src="{{ asset('assets/images/LOGO SMKN 1.png') }}" class="h-16 w-auto object-contain mx-auto select-none" alt="SMKN 1 Logo">
            <h1 class="mt-4 text-white text-base font-extrabold tracking-wider uppercase">STOVIA</h1>
            <p class="mt-1 text-blue-100/80 text-[11px] font-medium text-center px-4 max-w-[200px] leading-relaxed">
                Academic Management Portal
            </p>
        </div>

        <!-- Collapsed State -->
        <div class="flex flex-col items-center justify-center py-6 border-b border-white/10" 
             x-show="!sidebarOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <img src="{{ asset('assets/images/LOGO SMKN 1.png') }}" class="h-10 w-auto object-contain mx-auto select-none" alt="SMKN 1 Logo">
        </div>

        <!-- Navigation Menus -->
        <nav class="mt-6 px-4 space-y-1.5 flex-1">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
               :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            </a>

            <!-- Dynamic menus based on roles -->
            @if (Auth::user()->role === 'admin')
                <!-- User Management (Kelola Pengguna) Dropdown Group -->
                <div class="flex flex-col">
                    <button @click="if (!sidebarOpen) { sidebarOpen = true; kelolaOpen = true; } else { kelolaOpen = !kelolaOpen; }" 
                            class="rounded-xl text-sm font-semibold hover:bg-white/10 hover:text-white transition-all duration-300 focus:outline-none"
                            :class="[
                                sidebarOpen ? 'w-full flex items-center justify-between px-4 py-3 gap-3' : 'w-12 mx-auto flex items-center justify-center py-3',
                                ({{ request()->routeIs('admin.manage.admins.*') || request()->routeIs('admin.manage.gurus.*') || request()->routeIs('admin.manage.murids.*') ? 'true' : 'false' }}) ? 'text-white' : 'text-white/80'
                            ]">
                        <div class="flex items-center" :class="sidebarOpen ? 'gap-3' : 'gap-0'">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">User Management</span>
                        </div>
                        <svg x-show="sidebarOpen" 
                             class="w-4 h-4 transition-transform duration-200" 
                             :class="kelolaOpen ? 'rotate-180' : ''" 
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- Submenu Items -->
                    <div x-show="kelolaOpen && sidebarOpen" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="pl-4 mt-1 mb-1 space-y-1.5 overflow-hidden flex flex-col text-left border-l border-white/10 ml-6">
                        <a href="{{ route('admin.manage.admins.index') }}" 
                           class="block py-2 px-3 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.manage.admins.*') ? 'bg-[#0c2b4d] text-white font-bold' : 'text-blue-100/70 hover:text-white hover:bg-white/5' }}">
                            Kelola Admin
                        </a>
                        <a href="{{ route('admin.manage.gurus.index') }}" 
                           class="block py-2 px-3 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.manage.gurus.*') ? 'bg-[#0c2b4d] text-white font-bold' : 'text-blue-100/70 hover:text-white hover:bg-white/5' }}">
                            Kelola Guru
                        </a>
                        <a href="{{ route('admin.manage.murids.index') }}" 
                           class="block py-2 px-3 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.manage.murids.*') ? 'bg-[#0c2b4d] text-white font-bold' : 'text-blue-100/70 hover:text-white hover:bg-white/5' }}">
                            Kelola Murid
                        </a>
                    </div>
                </div>

                <!-- Department (Kelola Jurusan) -->
                <a href="{{ route('admin.manage.jurusans.index') }}" 
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.jurusans.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Department</span>
                </a>

                <!-- Subject (Kelola Mata Pelajaran) -->
                <a href="{{ route('admin.manage.subjects.index') }}" 
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.subjects.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Subject</span>
                </a>

                <!-- Class (Kelola Kelas) -->
                <a href="{{ route('admin.manage.classrooms.index') }}" 
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.classrooms.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Class</span>
                </a>

                <!-- Academic Year (Kelola Tahun Ajaran) -->
                <a href="{{ route('admin.manage.tahun-ajarans.index') }}" 
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.tahun-ajarans.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Academic Year</span>
                </a>
            @endif
        </nav>
    </div>
</aside>
