<x-app-layout>
    <x-slot name="header">
        {{ __('') }}
    </x-slot>

    <div class="py-4 sm:py-6 max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 font-sans">

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-medium shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Welcome Card -->
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider select-none">
                    Selamat Datang
                </span>
                <h2 class="text-xl font-bold mt-2.5">Halo, {{ auth()->user()->name }}!</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">Anda masuk sebagai <strong class="text-white uppercase">{{ auth()->user()->role }}</strong>. Silakan kelola modul pembelajaran, pantau absensi, dan lihat aktivitas terbaru di sistem E-Learning & Absensi SMKN 1 Jakarta.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                @if(auth()->user()->role === 'murid')
                    <div class="bg-emerald-500/20 px-4 py-3 rounded-2xl border border-emerald-500/30 text-center select-none flex-1 sm:flex-none">
                        <span class="text-[10px] uppercase font-bold text-emerald-300 tracking-wider block">Kehadiran</span>
                        <span class="text-sm font-extrabold text-emerald-400">{{ $data['attendance_percentage'] }}%</span>
                    </div>
                @endif
                <div class="bg-white/10 px-4 py-3 rounded-2xl border border-white/10 text-center select-none flex-1 sm:flex-none w-full sm:w-auto">
                    <span class="text-[10px] uppercase font-bold text-blue-200 tracking-wider block">Hari Ini</span>
                    <span class="text-sm font-extrabold whitespace-nowrap">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'admin')
            <!-- Admin Dashboard Widgets -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Total Guru -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">Total Guru</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-[#0c2b4d] mt-1">{{ $data['total_guru'] }}</h3>
                        <a href="{{ route('admin.manage.gurus.index') }}" class="text-[11px] text-[#0c2b4d] hover:underline font-bold mt-2.5 block">Kelola Guru &rarr;</a>
                    </div>
                    <div class="p-3 bg-blue-50 text-[#0c2b4d] rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Murid -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">Total Murid</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-indigo-605 mt-1">{{ $data['total_murid'] }}</h3>
                        <a href="{{ route('admin.manage.murids.index') }}" class="text-[11px] text-indigo-600 hover:underline font-bold mt-2.5 block">Kelola Murid &rarr;</a>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Kelas -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">Kelas Aktif</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $data['total_kelas'] }}</h3>
                        <a href="{{ route('admin.manage.classrooms.index') }}" class="text-[11px] text-emerald-600 hover:underline font-bold mt-2.5 block">Kelola Kelas &rarr;</a>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Modul -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">Modul Pembelajaran</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $data['total_modul'] }}</h3>
                        <span class="text-[11px] text-gray-400 font-bold mt-2.5 block">Total modul terdaftar</span>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Admin Dashboard Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6 mt-6">
                <!-- Classroom List (spans 3 cols, aligned under first 3 stat cards) -->
                <div class="col-span-1 lg:col-span-3 bg-white rounded-2xl border border-gray-150 shadow-lg overflow-hidden">
                    <div class="h-1.5 bg-[#0c2b4d]"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#0c2b4d]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Kelas Terdaftar Terbaru
                            </h3>
                            <a href="{{ route('admin.manage.classrooms.index') }}" class="text-xs text-blue-600 font-bold hover:underline">
                                Lihat Semua &rarr;
                            </a>
                        </div>

                        @if(count($data['classrooms']) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                                            <th class="py-3 px-2">Nama Kelas</th>
                                            <th class="py-3 px-2">Jurusan</th>
                                            <th class="py-3 px-2 text-center">Jumlah Murid</th>
                                            <th class="py-3 px-2 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 text-gray-600 font-medium">
                                        @foreach($data['classrooms'] as $class)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="py-3 px-2 font-bold text-gray-850">{{ $class->nama_kelas }}</td>
                                                <td class="py-3 px-2">{{ $class->jurusan->nama_jurusan ?? '-' }}</td>
                                                <td class="py-3 px-2 text-center">
                                                    <span class="bg-blue-50 text-blue-800 px-2 py-0.5 rounded-full font-bold">
                                                        {{ $class->murids_count }} Murid
                                                    </span>
                                                </td>
                                                <td class="py-3 px-2 text-right">
                                                    @if($class->is_active)
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-500 bg-gray-50 px-2 py-0.5 rounded-full">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-6 text-center bg-gray-50 border border-gray-100 border-dashed rounded-xl">
                                <p class="text-xs text-gray-450">Belum ada data kelas terdaftar.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Teachers Card -->
                <div class="bg-white rounded-2xl border border-gray-150 shadow-lg overflow-hidden lg:self-start">
                    <div class="h-1.5 bg-indigo-600"></div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Guru Baru Terdaftar
                            </h3>
                            <a href="{{ route('admin.manage.gurus.index') }}" class="text-xs text-blue-600 font-bold hover:underline">
                                Semua &rarr;
                            </a>
                        </div>

                        @if(count($data['recent_gurus']) > 0)
                            <div class="space-y-3">
                                @foreach($data['recent_gurus'] as $guru)
                                    <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-xl transition-colors">
                                        <div class="w-8 h-8 rounded-full bg-[#0c2b4d] text-white flex items-center justify-center font-bold text-xs select-none">
                                            {{ strtoupper(substr($guru->fullname, 0, 2)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-bold text-gray-800 truncate">{{ $guru->fullname }}{{ $guru->gelar ? ', '.$guru->gelar : '' }}</h4>
                                            <span class="text-[10px] text-gray-400 font-semibold block">NIP. {{ $guru->nip }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-4 text-center bg-gray-50 border border-gray-100 border-dashed rounded-xl">
                                <p class="text-[11px] text-gray-400 font-bold">Belum ada data guru terdaftar.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @elseif(auth()->user()->role === 'guru')
            <!-- Guru Dashboard Widgets -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-6">
                <!-- Modul Pembelajaran -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Modul Aktif Anda</span>
                        <h3 class="text-3xl font-extrabold text-[#0c2b4d] mt-1">{{ $data['total_modul'] }}</h3>
                        <a href="{{ route('guru.learning-modules.index') }}" class="text-[11px] text-[#0c2b4d] hover:underline font-bold mt-2.5 block">Lihat Semua Modul &rarr;</a>
                    </div>
                    <div class="p-3 bg-blue-50 text-[#0c2b4d] rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>

                <!-- Pending Grades -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Penilaian Tugas</span>
                        <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ $data['pending_grades'] }}</h3>
                        <span class="text-[11px] text-gray-400 font-bold mt-2.5 block">Tugas murid belum dinilai</span>
                    </div>
                    <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 112 2h2a2 2 0 012-2"></path>
                        </svg>
                    </div>
                </div>

                <!-- Pending Izin Requests -->
                <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Persetujuan Izin</span>
                        <h3 class="text-3xl font-extrabold text-amber-600 mt-1">{{ $data['pending_izins'] }}</h3>
                        <span class="text-[11px] text-gray-400 font-bold mt-2.5 block">Permohonan izin belum diproses</span>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

        @elseif(auth()->user()->role === 'murid')
            <!-- Tasks List (Full Width) -->
            <div class="bg-white rounded-2xl border border-gray-150 shadow-lg overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-rose-500 to-pink-600"></div>
                <div class="p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Tugas yang Harus Dikerjakan
                    </h3>
                    @if (count($data['unsubmitted_tasks_list']) > 0)
                        <div class="space-y-4">
                            @foreach ($data['unsubmitted_tasks_list'] as $task)
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:shadow-md transition-all gap-4">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                                {{ $task->learningModule->mataPelajaran->nama_pelajaran ?? 'Pelajaran' }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-medium">
                                                {{ $task->learningModule->title }}
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-800">{{ $task->title }}</h4>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{!! strip_tags($task->instructions) !!}</p>
                                    </div>
                                    <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                                        <div class="text-left sm:text-right select-none">
                                            <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-bold">Batas Waktu</span>
                                            <span class="text-xs font-bold text-rose-600">
                                                {{ $task->due_date ? $task->due_date->translatedFormat('d F Y, H:i') : 'Tanpa Batas Waktu' }}
                                            </span>
                                        </div>
                                        <a href="{{ route('murid.learning-modules.tugas.show', [$task->learning_module_id, $task->id]) }}"
                                           class="bg-[#0c2b4d] hover:bg-[#07192d] text-white font-bold px-4 py-2 rounded-lg text-xs transition-colors cursor-pointer select-none whitespace-nowrap">
                                            Kerjakan
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 text-center bg-gray-50 border border-gray-100 border-dashed rounded-xl">
                            <svg class="w-12 h-12 text-emerald-500 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="text-sm font-bold text-gray-800 mb-0.5">Semua Tugas Selesai!</h4>
                            <p class="text-xs text-gray-400 max-w-xs">Hebat! Tidak ada tugas aktif yang belum dikumpulkan saat ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
