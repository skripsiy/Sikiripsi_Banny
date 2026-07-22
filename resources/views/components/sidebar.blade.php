<aside x-data="{
            init() {
                // Keep User Management submenu open if any child route is active
                if ({{ request()->routeIs('admin.manage.admins.*') || request()->routeIs('admin.manage.gurus.*') || request()->routeIs('admin.manage.murids.*') ? 'true' : 'false' }}) {
                    this.kelolaOpen = true;
                }
                // Keep Tahun & Semester submenu open if active
                if ({{ request()->routeIs('admin.manage.semesters.*') || request()->routeIs('admin.manage.tahun_akademiks.*') ? 'true' : 'false' }}) {
                    this.akademikOpen = true;
                }
            },
            kelolaOpen: false,
            akademikOpen: false,
            navigateTo(url) {
                if (this.isMobile) {
                    this.sidebarOpen = false;
                }
                window.location.href = url;
            }
       }"
       x-show="isMobile ? sidebarOpen : true"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full opacity-0"
       x-transition:enter-end="translate-x-0 opacity-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="translate-x-0 opacity-100"
       x-transition:leave-end="-translate-x-full opacity-0"
       :class="isMobile ? 'fixed inset-y-0 left-0 z-30 w-72 h-screen m-0 rounded-r-[24px] rounded-l-none' : 'my-4 ml-4 mr-4 h-[calc(100vh-2rem)] rounded-[24px] ' + (sidebarOpen ? 'w-64' : 'w-20')"
       class="bg-[#1b61d1] text-white flex-shrink-0 flex flex-col shadow-2xl select-none transition-all duration-300 ease-in-out font-sans overflow-hidden"
       style="display: none;">

    <!-- Top Scrollable Section -->
    <div class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar">
        <!-- Sidebar Collapse / Expand Button -->
        <div class="h-16 flex items-center border-b border-white/10 cursor-pointer hover:bg-white/5 transition-all duration-300"
             @click="isMobile ? (sidebarOpen = false) : (sidebarOpen = !sidebarOpen)"
             :class="isMobile || sidebarOpen ? 'px-6 gap-3 justify-start' : 'justify-center px-0 gap-0'">
            <svg class="w-4 h-4 text-blue-100 flex-shrink-0 transition-transform duration-300"
                 :class="!isMobile && !sidebarOpen ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span x-show="isMobile || sidebarOpen"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm font-semibold text-blue-100/90 whitespace-nowrap" x-text="isMobile ? 'Tutup Menu' : 'Collapse'"></span>
        </div>

        <!-- Brand Logo / Avatar Section -->
        <!-- Expanded State -->
        <div class="flex flex-col items-center justify-center py-8 border-b border-white/10"
             x-show="isMobile || sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <img src="{{ asset('assets/images/LOGO SMKN 1.png') }}" class="h-16 w-auto object-contain mx-auto select-none" alt="SMKN 1 Logo">
            <h1 class="mt-4 text-white text-base font-extrabold tracking-wider uppercase">SMKN 1 Jakarta</h1>
            <p class="mt-1 text-blue-100/80 text-[11px] font-medium text-center px-4 max-w-[200px] leading-relaxed">
                Academic Management Portal
            </p>
        </div>

        <!-- Collapsed State -->
        <div class="flex flex-col items-center justify-center py-6 border-b border-white/10"
             x-show="!isMobile && !sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <img src="{{ asset('assets/images/LOGO SMKN 1.png') }}" class="h-10 w-auto object-contain mx-auto select-none" alt="SMKN 1 Logo">
        </div>

        <!-- Navigation Menus -->
        <nav class="mt-6 px-4 space-y-1.5 flex-1">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}"
               @click.prevent="navigateTo('{{ route('dashboard') }}')"
               class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
               :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            </a>

            <!-- Dynamic menus based on roles -->
            @if (Auth::user()->role === 'admin')
                <!-- 1. Kelola Pengguna (User Management) Dropdown Group -->
                <div class="flex flex-col">
                    <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; kelolaOpen = true; } else { kelolaOpen = !kelolaOpen; }"
                            class="rounded-xl text-sm font-semibold hover:bg-white/10 hover:text-white transition-all duration-300 focus:outline-none"
                            :class="[
                                isMobile || sidebarOpen ? 'w-full flex items-center justify-between px-4 py-3 gap-3' : 'w-12 mx-auto flex items-center justify-center py-3',
                                ({{ request()->routeIs('admin.manage.admins.*') || request()->routeIs('admin.manage.gurus.*') || request()->routeIs('admin.manage.murids.*') ? 'true' : 'false' }}) ? 'text-white' : 'text-white/80'
                            ]">
                        <div class="flex items-center" :class="isMobile || sidebarOpen ? 'gap-3' : 'gap-0'">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Kelola Pengguna</span>
                        </div>
                        <svg x-show="isMobile || sidebarOpen"
                             class="w-4 h-4 transition-transform duration-200"
                             :class="kelolaOpen ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Submenu Items -->
                    <div x-show="kelolaOpen && (isMobile || sidebarOpen)"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="pl-4 mt-1 mb-1 space-y-1.5 overflow-hidden flex flex-col text-left border-l border-white/10 ml-6">
                        <a href="{{ route('admin.manage.admins.index') }}"
                           @click.prevent="navigateTo('{{ route('admin.manage.admins.index') }}')"
                           class="block py-2 px-3 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.manage.admins.*') ? 'bg-[#0c2b4d] text-white font-bold' : 'text-blue-100/70 hover:text-white hover:bg-white/5' }}">
                            Kelola Admin
                        </a>
                        <a href="{{ route('admin.manage.gurus.index') }}"
                           @click.prevent="navigateTo('{{ route('admin.manage.gurus.index') }}')"
                           class="block py-2 px-3 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.manage.gurus.*') ? 'bg-[#0c2b4d] text-white font-bold' : 'text-blue-100/70 hover:text-white hover:bg-white/5' }}">
                            Kelola Guru
                        </a>
                        <a href="{{ route('admin.manage.murids.index') }}"
                           @click.prevent="navigateTo('{{ route('admin.manage.murids.index') }}')"
                           class="block py-2 px-3 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.manage.murids.*') ? 'bg-[#0c2b4d] text-white font-bold' : 'text-blue-100/70 hover:text-white hover:bg-white/5' }}">
                            Kelola Murid
                        </a>
                    </div>
                </div>

                <!-- 2. Tahun & Semester Dropdown Group -->
                <div class="flex flex-col">
                    <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; akademikOpen = true; } else { akademikOpen = !akademikOpen; }"
                            class="rounded-xl text-sm font-semibold hover:bg-white/10 hover:text-white transition-all duration-300 focus:outline-none"
                            :class="[
                                isMobile || sidebarOpen ? 'w-full flex items-center justify-between px-4 py-3 gap-3' : 'w-12 mx-auto flex items-center justify-center py-3',
                                ({{ request()->routeIs('admin.manage.semesters.*') || request()->routeIs('admin.manage.tahun_akademiks.*') ? 'true' : 'false' }}) ? 'text-white' : 'text-white/80'
                            ]">
                        <div class="flex items-center" :class="isMobile || sidebarOpen ? 'gap-3' : 'gap-0'">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Tahun & Semester</span>
                        </div>
                        <svg x-show="isMobile || sidebarOpen"
                             class="w-4 h-4 transition-transform duration-200"
                             :class="akademikOpen ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Submenu Items -->
                    <div x-show="akademikOpen && (isMobile || sidebarOpen)"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="pl-4 mt-1 mb-1 space-y-1.5 overflow-hidden flex flex-col text-left border-l border-white/10 ml-6">
                        <a href="{{ route('admin.manage.tahun_akademiks.index') }}"
                           @click.prevent="navigateTo('{{ route('admin.manage.tahun_akademiks.index') }}')"
                           class="block py-2 px-3 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.manage.tahun_akademiks.*') ? 'bg-[#0c2b4d] text-white font-bold' : 'text-blue-100/70 hover:text-white hover:bg-white/5' }}">
                            Tahun Akademik
                        </a>
                        <a href="{{ route('admin.manage.semesters.index') }}"
                           @click.prevent="navigateTo('{{ route('admin.manage.semesters.index') }}')"
                           class="block py-2 px-3 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.manage.semesters.*') ? 'bg-[#0c2b4d] text-white font-bold' : 'text-blue-100/70 hover:text-white hover:bg-white/5' }}">
                            Semester
                        </a>
                    </div>
                </div>

                <!-- 3. Jurusan -->
                <a href="{{ route('admin.manage.jurusans.index') }}"
                   @click.prevent="navigateTo('{{ route('admin.manage.jurusans.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.jurusans.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Jurusan</span>
                </a>

                <!-- 4. Kelas -->
                <a href="{{ route('admin.manage.classrooms.index') }}"
                   @click.prevent="navigateTo('{{ route('admin.manage.classrooms.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.classrooms.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Kelas</span>
                </a>

                <!-- 5. Mata Pelajaran -->
                <a href="{{ route('admin.manage.mata_pelajarans.index') }}"
                   @click.prevent="navigateTo('{{ route('admin.manage.mata_pelajarans.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.mata_pelajarans.*') && !request()->routeIs('admin.manage.penugasan-guru.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Mata Pelajaran</span>
                </a>

                <!-- 5.5 Kurikulum Kelas (Assign Mapel ke Kelas) -->
                <a href="{{ route('admin.manage.kurikulum-kelas.index') }}"
                   @click.prevent="navigateTo('{{ route('admin.manage.kurikulum-kelas.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.kurikulum-kelas.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Kurikulum Kelas</span>
                </a>

                <!-- 6. Penugasan Guru -->
                <a href="{{ route('admin.manage.penugasan-guru.index') }}"
                   @click.prevent="navigateTo('{{ route('admin.manage.penugasan-guru.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.penugasan-guru.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Penugasan Guru</span>
                </a>

                <!-- 7. Modul Pembelajaran (Admin) -->
                <a href="{{ route('admin.manage.learning-modules.index') }}"
                   @click.prevent="navigateTo('{{ route('admin.manage.learning-modules.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.learning-modules.index') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Modul Pembelajaran</span>
                </a>

                <!-- 8. Absensi Siswa (Admin) -->
                <a href="{{ route('admin.manage.absensi.index') }}"
                   @click.prevent="navigateTo('{{ route('admin.manage.absensi.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.manage.absensi.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 112 2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Absensi Siswa</span>
                </a>
            @endif

            @if (Auth::user()->role === 'guru')
                <!-- Learning Module -->
                <a href="{{ route('guru.learning-modules.index') }}"
                   @click.prevent="navigateTo('{{ route('guru.learning-modules.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('guru.learning-modules.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Learning Module</span>
                </a>

                <!-- Rekap Absensi -->
                <a href="{{ route('guru.absensi.index') }}"
                   @click.prevent="navigateTo('{{ route('guru.absensi.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('guru.absensi.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 112 2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Rekap Absensi</span>
                </a>

                <!-- Bank Soal -->
                <a href="{{ route('guru.bank-soal.index') }}"
                   @click.prevent="navigateTo('{{ route('guru.bank-soal.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('guru.bank-soal.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 112 2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Bank Soal</span>
                </a>
            @endif

            @if (Auth::user()->role === 'murid')
                <!-- Learning Module -->
                <a href="{{ route('murid.learning-modules.index') }}"
                   @click.prevent="navigateTo('{{ route('murid.learning-modules.index') }}')"
                   class="flex items-center rounded-xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('murid.learning-modules.*') ? 'bg-[#0c2b4d] text-white shadow-inner font-bold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                   :class="isMobile || sidebarOpen ? 'px-4 py-3 justify-start gap-3' : 'px-0 py-3 justify-center gap-0 w-12 mx-auto'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span x-show="isMobile || sidebarOpen" class="whitespace-nowrap">Learning Module</span>
                </a>
            @endif
        </nav>
    </div>
</aside>
