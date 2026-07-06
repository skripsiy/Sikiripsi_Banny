<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Learning Module') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        showEditModal: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
        searchQuery: '',
        editData: {
            id: '{{ old('id') ?? '' }}',
            mata_pelajaran_id: '{{ old('mata_pelajaran_id') ?? '' }}',
            tahun_akademik_id: '{{ old('tahun_akademik_id') ?? '' }}',
            classroom_id: '{{ old('classroom_id') ?? '' }}',
            title: {{ json_encode(old('title') ?? '') }},
            description: {{ json_encode(old('description') ?? '') }}
        },
        editUrl: '{{ old('id') ? route('guru.learning-modules.update', old('id')) : '' }}'
    }">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 sm:gap-0 mb-6">
            <div>
                <h3 class="text-base font-bold text-gray-800">Daftar Modul Pembelajaran Anda</h3>
                <p class="text-xs text-gray-400 mt-0.5">Kelola seluruh materi, tugas, kuis, ujian, dan absensi modul Anda.</p>
            </div>
            @if ($mata_pelajarans->isNotEmpty())
                <button @click="showCreateModal = true" 
                        class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer select-none w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Modul
                </button>
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
                <input type="text" x-model="searchQuery" placeholder="Cari berdasarkan judul, deskripsi, atau mata pelajaran..." 
                       class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs text-gray-800 focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all placeholder-gray-400">
                <!-- Clear Search Button -->
                <button x-show="searchQuery !== ''" @click="searchQuery = ''" style="display: none;"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Filter Dropdown -->
            <div class="flex items-center gap-3 w-full md:w-auto justify-stretch">
                <form method="GET" action="{{ route('guru.learning-modules.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                    <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Tahun Ajaran:
                    </span>
                    <select name="tahun_akademik_id" id="filter_tahun_akademik_id" onchange="this.form.submit()"
                            class="px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] transition-all shadow-sm cursor-pointer w-full sm:w-auto sm:min-w-[150px]">
                        <option value="all" {{ $selectedAcademicYearId == 'all' ? 'selected' : '' }}>Semua Tahun Ajaran</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $selectedAcademicYearId == $ay->id ? 'selected' : '' }}>
                                {{ $ay->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                </form>
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
                                 {{ json_encode(strtolower($module->mataPelajaran->nama_pelajaran ?? '')) }}.includes(searchQuery.toLowerCase())"
                         @click="window.location.href='{{ route('guru.learning-modules.show', $module->id) }}'">
                        
                        <!-- Gradient Banner Header -->
                        <div class="h-28 bg-gradient-to-br {{ $selectedGradient }} p-4 relative flex flex-col justify-between select-none">
                            <div class="flex items-start justify-between w-full">
                                <!-- Subject Badge -->
                                @if($module->mataPelajaran)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/20 text-white backdrop-blur-sm border border-white/10 uppercase tracking-wider">
                                        {{ $module->mataPelajaran->nama_pelajaran }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/10 text-white/80 backdrop-blur-sm border border-white/5 uppercase tracking-wider">
                                        Tidak Diketahui
                                    </span>
                                @endif

                                <!-- Action Icons (Top Right) -->
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <!-- Edit Button (Icon) -->
                                    <button @click.stop="
                                        showEditModal = true;
                                        editData = {
                                            id: '{{ $module->id }}',
                                            mata_pelajaran_id: '{{ $module->mata_pelajaran_id }}',
                                            tahun_akademik_id: '{{ $module->tahun_akademik_id }}',
                                            classroom_id: '{{ $module->classroom_id }}',
                                            title: {{ json_encode($module->title) }},
                                            description: {{ json_encode($module->description) }}
                                        };
                                        editUrl = '{{ route('guru.learning-modules.update', $module->id) }}';
                                     " 
                                     title="Edit Modul"
                                     class="text-white/85 hover:text-white hover:bg-white/25 p-1.5 rounded-lg transition-all duration-150 cursor-pointer select-none">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Hapus Button (Icon) -->
                                    <form action="{{ route('guru.learning-modules.destroy', $module->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul pembelajaran ini?');" class="inline" @click.stop>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Modul"
                                                class="text-white/85 hover:text-red-200 hover:bg-red-500/25 p-1.5 rounded-lg transition-all duration-150 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-auto flex gap-1.5 flex-wrap">
                                <span class="text-[10px] font-bold text-white/90 bg-white/20 px-2 py-0.5 rounded-md backdrop-blur-sm tracking-wide">
                                    {{ $module->tahunAkademik->tahun_ajaran ?? '-' }}
                                </span>
                                @if($module->classroom)
                                    <span class="text-[10px] font-bold text-white bg-emerald-500/80 px-2 py-0.5 rounded-md tracking-wide">
                                        {{ $module->classroom->nama_kelas }}
                                    </span>
                                @endif
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
                                <p class="text-xs text-gray-405 leading-relaxed line-clamp-2">
                                    {{ $module->description }}
                                </p>
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
                        @if ($mata_pelajarans->isEmpty())
                            <p class="text-sm text-gray-400 max-w-sm mb-6">Anda belum ditugaskan ke mata pelajaran apapun. Silakan hubungi Administrator untuk penugasan mata pelajaran.</p>
                        @else
                            <p class="text-sm text-gray-400 max-w-sm mb-6">Mulai tambahkan modul pembelajaran untuk mata pelajaran yang Anda ampu agar murid dapat mengakses materi.</p>
                            <button @click="showCreateModal = true" 
                                    class="inline-flex items-center gap-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                                + Tambah Modul
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($mata_pelajarans->isNotEmpty())
            <!-- Create Modal -->
            <div x-show="showCreateModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all">
                    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                    <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                    <div class="p-4 sm:p-6 lg:p-8">
                        @include('guru.learning_modules.create')
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div x-show="showEditModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all">
                    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                    <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                    <div class="p-4 sm:p-6 lg:p-8">
                        @include('guru.learning_modules.edit')
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
