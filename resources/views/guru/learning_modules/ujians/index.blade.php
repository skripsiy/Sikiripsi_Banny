<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Ujian: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateUjianModal: false,
        showEditUjianModal: false,
        editUjianData: { id: '', title: '', instructions: '', duration_minutes: '', due_date: '' },
        editUjianUrl: '',
        searchQuery: ''
    }">

        <!-- Back Button (outside header) -->
        <div class="mb-4">
            <a href="{{ route('guru.learning-modules.show', $learningModule->id) }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Dashboard Modul
            </a>
        </div>

        <!-- Module Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $learningModule->subject->nama_pelajaran }} ({{ $learningModule->subject->kode_pelajaran }})
                    </span>
                    <span class="bg-purple-500/20 text-purple-200 border border-purple-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Kelola Ujian
                    </span>
                </div>
                <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">{{ $learningModule->description }}</p>
            </div>
        </div>

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Content Area -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h3 class="text-base font-bold text-gray-800">Daftar Ujian</h3>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Cari ujian..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs text-gray-800 transition-all">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <button @click="showCreateUjianModal = true"
                            class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer select-none whitespace-nowrap">
                        + Tambah Ujian
                    </button>
                </div>
            </div>

            @if($ujians->isNotEmpty())
                <div class="divide-y divide-gray-100 border border-gray-255 rounded-2xl overflow-hidden bg-white">
                    @foreach($ujians as $u)
                        <div x-show="searchQuery === '' || '{{ strtolower(addslashes($u->title)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($u->instructions)) }}'.includes(searchQuery.toLowerCase())"
                             class="p-4 hover:bg-gray-50/30 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative group/item">
                            
                            <div class="flex items-start gap-3.5 min-w-0 flex-grow">
                                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-gray-800 text-sm truncate">{{ $u->title }}</h4>
                                        <span class="inline-flex text-[9px] bg-red-50 text-red-700 border border-red-150 px-2 py-0.5 rounded font-semibold whitespace-nowrap select-none">
                                            Batas: {{ $u->due_date->translatedFormat('d M Y H:i') }}
                                        </span>
                                        <span class="inline-flex text-[9px] bg-blue-50 text-blue-750 border border-blue-150 px-2 py-0.5 rounded font-semibold whitespace-nowrap select-none">
                                            Durasi: {{ $u->duration_minutes }} Menit
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed whitespace-pre-wrap">{{ $u->instructions }}</p>
                                </div>
                            </div>

                            <!-- Actions (Edit/Delete) -->
                            <div class="flex items-center gap-1.5 self-end sm:self-center flex-shrink-0">
                                <!-- Manage Questions -->
                                <a href="{{ route('guru.learning-modules.ujians.soals', [$learningModule->id, $u->id]) }}" 
                                   class="text-[#0c2b4d] hover:bg-blue-50 px-2.5 py-1.5 rounded-lg border border-gray-200 text-[10px] font-bold transition-all flex items-center gap-1" title="Kelola Soal">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Kelola Soal</span>
                                </a>

                                <!-- Results -->
                                <a href="{{ route('guru.learning-modules.ujians.results', [$learningModule->id, $u->id]) }}" 
                                   class="text-green-700 hover:bg-green-50 px-2.5 py-1.5 rounded-lg border border-gray-200 text-[10px] font-bold transition-all flex items-center gap-1" title="Lihat Hasil">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Hasil</span>
                                </a>

                                <button @click="
                                    showEditUjianModal = true;
                                    editUjianData = {
                                        id: '{{ $u->id }}',
                                        title: {{ json_encode($u->title) }},
                                        instructions: {{ json_encode($u->instructions) }},
                                        duration_minutes: '{{ $u->duration_minutes }}',
                                        due_date: '{{ $u->due_date->format('Y-m-d\TH:i') }}'
                                    };
                                    editUjianUrl = '{{ route('guru.learning-modules.ujians.update', [$learningModule->id, $u->id]) }}';
                                 " class="text-gray-400 hover:text-blue-650 transition-colors p-2 rounded-lg hover:bg-blue-50 cursor-pointer" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('guru.learning-modules.ujians.destroy', [$learningModule->id, $u->id]) }}" method="POST" onsubmit="return confirm('Hapus ujian ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50 cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-400 text-sm">Belum ada ujian ditambahkan.</div>
            @endif
        </div>

        <!-- Create Ujian Modal -->
        <div x-show="showCreateUjianModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showCreateUjianModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showCreateUjianModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showCreateUjianModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-8">
                    @include('guru.learning_modules.ujian_fields', ['actionUrl' => route('guru.learning-modules.ujians.store', $learningModule->id), 'isEdit' => false])
                </div>
            </div>
        </div>

        <!-- Edit Ujian Modal -->
        <div x-show="showEditUjianModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showEditUjianModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showEditUjianModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showEditUjianModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-8">
                    @include('guru.learning_modules.ujian_fields', ['actionUrl' => '', 'isEdit' => true])
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
