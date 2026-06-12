<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Tugas: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateTugasModal: false,
        showEditTugasModal: false,
        editTugasData: { id: '', title: '', instructions: '', due_date: '' },
        editTugasUrl: '',
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
                        {{ $learningModule->mataPelajaran->nama_pelajaran }} ({{ $learningModule->mataPelajaran->kode_pelajaran }})
                    </span>
                    <span class="bg-rose-500/20 text-rose-200 border border-rose-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Kelola Tugas
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
                <h3 class="text-base font-bold text-gray-800">Daftar Tugas</h3>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Cari tugas..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs text-gray-800 transition-all">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <button @click="showCreateTugasModal = true"
                            class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer select-none whitespace-nowrap">
                        + Tambah Tugas
                    </button>
                </div>
            </div>

            @if($tugas->isNotEmpty())
                <div class="divide-y divide-gray-100 border border-gray-255 rounded-2xl overflow-hidden bg-white">
                    @foreach($tugas as $t)
                        <div x-show="searchQuery === '' || '{{ strtolower(addslashes($t->title)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($t->instructions)) }}'.includes(searchQuery.toLowerCase())"
                             class="p-4 hover:bg-gray-50/30 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative group/item">
                            
                            <div class="flex items-start gap-3.5 min-w-0 flex-grow">
                                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-gray-800 text-sm truncate">{{ $t->title }}</h4>
                                        <span class="inline-flex text-[9px] bg-red-50 text-red-700 border border-red-150 px-2 py-0.5 rounded font-semibold whitespace-nowrap select-none">
                                            Batas: {{ $t->due_date->translatedFormat('d M Y H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed whitespace-pre-wrap">{{ $t->instructions }}</p>
                                    @if ($t->file_path)
                                        <div class="mt-2.5">
                                            <a href="{{ asset('storage/' . $t->file_path) }}" target="_blank"
                                               class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-800 text-[10px] font-bold select-none">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                                Unduh Petunjuk
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions (Edit/Delete) -->
                            <div class="flex items-center gap-2 self-end sm:self-center flex-shrink-0">
                                <a href="{{ route('guru.learning-modules.tugas.submissions', [$learningModule->id, $t->id]) }}" 
                                   class="text-[#0c2b4d] hover:text-[#061424] font-bold text-xs bg-blue-50 hover:bg-blue-100/80 px-3 py-2 rounded-xl transition-all shadow-sm flex items-center gap-1.5 cursor-pointer select-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Lihat Pengumpulan
                                </a>
                                <button @click="
                                    showEditTugasModal = true;
                                    editTugasData = {
                                        id: '{{ $t->id }}',
                                        title: {{ json_encode($t->title) }},
                                        instructions: {{ json_encode($t->instructions) }},
                                        due_date: '{{ $t->due_date->format('Y-m-d\TH:i') }}'
                                    };
                                    editTugasUrl = '{{ route('guru.learning-modules.tugas.update', [$learningModule->id, $t->id]) }}';
                                 " class="text-gray-400 hover:text-blue-650 transition-colors p-2 rounded-lg hover:bg-blue-50 cursor-pointer" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('guru.learning-modules.tugas.destroy', [$learningModule->id, $t->id]) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?');" class="inline">
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
                <div class="text-center py-10 text-gray-400 text-sm">Belum ada tugas ditambahkan.</div>
            @endif
        </div>

        <!-- Create Tugas Modal -->
        <div x-show="showCreateTugasModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showCreateTugasModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showCreateTugasModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showCreateTugasModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-8">
                    @include('guru.learning_modules.tugas_fields', ['actionUrl' => route('guru.learning-modules.tugas.store', $learningModule->id), 'isEdit' => false])
                </div>
            </div>
        </div>

        <!-- Edit Tugas Modal -->
        <div x-show="showEditTugasModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showEditTugasModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showEditTugasModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showEditTugasModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-8">
                    @include('guru.learning_modules.tugas_fields', ['actionUrl' => '', 'isEdit' => true])
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
