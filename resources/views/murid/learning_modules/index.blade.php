<x-app-layout>
    <x-slot name="header">
        {{ __('Learning Modules') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        searchQuery: ''
    }">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-base font-bold text-gray-800">Modul Pembelajaran Anda</h3>
                <p class="text-xs text-gray-400 mt-0.5">Akses materi pelajaran, tugas, kuis, ujian, dan riwayat absensi Anda.</p>
            </div>
            @if($classroom)
                <span class="bg-blue-50 text-blue-700 border border-blue-100 px-3.5 py-1.5 rounded-xl text-xs font-bold shadow-sm">
                    Kelas: {{ $classroom->nama_kelas }} ({{ $classroom->tahunAjaran->tahun_ajaran ?? '-' }} - {{ ucfirst($classroom->tahunAjaran->semester ?? '') }})
                </span>
            @endif
        </div>

        <!-- Search & Filter Panel -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Search Bar -->
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text" x-model="searchQuery" placeholder="Cari berdasarkan judul, deskripsi, guru, atau mata pelajaran..." 
                       class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs text-gray-800 focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all placeholder-gray-400">
                <!-- Clear Search Button -->
                <button x-show="searchQuery !== ''" @click="searchQuery = ''" style="display: none;"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($learningModules->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($learningModules as $module)
                    @php
                        $gradients = [
                            'from-blue-600 to-indigo-700',
                            'from-teal-500 to-emerald-600',
                            'from-purple-600 to-indigo-700',
                            'from-rose-500 to-pink-600',
                            'from-amber-500 to-orange-600',
                        ];
                        $gradientIndex = $module->id % count($gradients);
                        $selectedGradient = $gradients[$gradientIndex];
                    @endphp
                    <div class="group relative bg-white border border-gray-100 hover:border-gray-200 rounded-2xl hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col cursor-pointer shadow-sm overflow-hidden"
                         x-show="searchQuery === '' || 
                                 {{ json_encode(strtolower($module->title)) }}.includes(searchQuery.toLowerCase()) || 
                                 {{ json_encode(strtolower($module->description)) }}.includes(searchQuery.toLowerCase()) || 
                                 {{ json_encode(strtolower($module->guru->user->name ?? '')) }}.includes(searchQuery.toLowerCase()) || 
                                 {{ json_encode(strtolower($module->subject->nama_pelajaran ?? '')) }}.includes(searchQuery.toLowerCase())"
                         @click="window.location.href='{{ route('murid.learning-modules.show', $module->id) }}'">
                        
                        <!-- Gradient Banner Header -->
                        <div class="h-28 bg-gradient-to-br {{ $selectedGradient }} p-4 relative flex flex-col justify-between select-none">
                            <div class="flex items-start justify-between w-full">
                                <!-- Subject Badge -->
                                @if($module->subject)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/20 text-white backdrop-blur-sm border border-white/10 uppercase tracking-wider">
                                        {{ $module->subject->nama_pelajaran }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/10 text-white/80 backdrop-blur-sm border border-white/5 uppercase tracking-wider">
                                        Tidak Diketahui
                                    </span>
                                @endif
                            </div>

                            <!-- Academic Year info -->
                            <div class="mt-auto">
                                <span class="text-[10px] font-bold text-white/90 bg-white/20 px-2 py-0.5 rounded-md backdrop-blur-sm tracking-wide">
                                    {{ $module->tahunAjaran->tahun_ajaran ?? '-' }} ({{ ucfirst($module->tahunAjaran->semester ?? '') }})
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex flex-col justify-between flex-grow">
                            <div>
                                <!-- Title -->
                                <h4 class="text-sm font-bold text-gray-800 group-hover:text-[#0c2b4d] transition-colors line-clamp-1 mb-2">
                                    {{ $module->title }}
                                </h4>

                                <!-- Description -->
                                <p class="text-xs text-gray-405 leading-relaxed line-clamp-2 mb-4">
                                    {{ $module->description }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-medium">
                                <span class="text-xs text-gray-700 font-extrabold truncate max-w-[140px] flex items-center gap-1" title="{{ $module->guru->user->name ?? 'Guru' }}">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                    <span class="truncate">{{ $module->guru->user->name ?? 'Guru' }}</span>
                                </span>
                                <span class="text-gray-450 whitespace-nowrap bg-gray-50 px-2 py-1 rounded-lg border border-gray-100 text-[10px]">
                                    {{ $module->materis_count }} Mat • {{ $module->tugas_count }} Tug • {{ $module->quizzes_count }} Kuis
                                </span>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <!-- Premium Empty State Placement -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                <!-- Decorative Top Gradient Line -->
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>

                <div class="p-6">
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-20 h-20 bg-blue-50 text-gray-300 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Modul Pembelajaran</h4>
                        <p class="text-sm text-gray-400 max-w-sm">Saat ini belum ada modul pembelajaran yang aktif untuk kelas dan jurusan Anda.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
