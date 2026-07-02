<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Soal Ujian: ' . $ujian->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showAddSoalModal: false,
        searchQuery: ''
    }">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('guru.learning-modules.ujians.index', $learningModule->id) }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Ujian
            </a>
        </div>

        <!-- Ujian Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $learningModule->mataPelajaran->nama_pelajaran }}
                    </span>
                    <span class="bg-amber-500/20 text-amber-200 border border-amber-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Durasi: {{ $ujian->duration_minutes }} Menit
                    </span>
                    <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $attachedSoals->count() }} Soal Ditautkan
                    </span>
                </div>
                <h2 class="text-xl font-bold mt-2.5">Kelola Pertanyaan - {{ $ujian->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">{{ $ujian->instructions }}</p>
            </div>
            
            <button @click="showAddSoalModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Pilih Dari Bank Soal
            </button>
        </div>

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium shadow-sm">
                <ul class="list-disc pl-5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Attached Questions List & Reorder Form -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-800 mb-1">Daftar Soal Ujian Aktif</h3>
            <p class="text-xs text-gray-400 mb-6">Sesuaikan urutan tampil dan bobot nilai untuk masing-masing soal di bawah ini.</p>

            @if($attachedSoals->isNotEmpty())
                <form action="{{ route('guru.learning-modules.ujians.soals.order', [$learningModule->id, $ujian->id]) }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6">
                        @foreach($attachedSoals as $index => $soal)
                            <div class="p-5 border border-gray-150 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-blue-250 transition-colors">
                                <div class="flex items-start gap-4 flex-grow min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-gray-50 border border-gray-200 text-gray-400 text-xs font-bold flex items-center justify-center flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="min-w-0">
                                        <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold border uppercase tracking-wider mb-2 {{ $soal->tipe === 'pg' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-purple-50 text-purple-700 border-purple-100' }}">
                                            {{ $soal->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}
                                        </span>
                                        <p class="text-xs text-gray-800 font-bold whitespace-pre-wrap leading-relaxed">{!! nl2br(e($soal->pertanyaan)) !!}</p>
                                        
                                        @if($soal->tipe === 'pg')
                                            <div class="grid grid-cols-2 gap-2 mt-2 pl-4 border-l-2 border-gray-100 text-[11px] text-gray-400">
                                                @foreach($soal->options as $opt)
                                                    <span class="{{ $opt->is_correct ? 'text-green-600 font-bold' : '' }}">
                                                        {{ $opt->label }}. {{ $opt->teks_opsi }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 w-full md:w-auto justify-end flex-shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-gray-100">
                                    <!-- Urutan Input -->
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Urutan:</span>
                                        <input type="number" name="soals[{{ $soal->id }}][urutan]" value="{{ $soal->pivot->urutan }}" required min="1"
                                               class="w-14 px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 text-center focus:outline-none focus:bg-white focus:border-[#0c2b4d]">
                                    </div>

                                    <!-- Bobot Input -->
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Bobot:</span>
                                        <input type="number" name="soals[{{ $soal->id }}][bobot]" value="{{ $soal->pivot->bobot }}" required min="1"
                                               class="w-14 px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 text-center focus:outline-none focus:bg-white focus:border-[#0c2b4d]">
                                    </div>

                                    <!-- Detach Button -->
                                    <button type="button" 
                                            onclick="if(confirm('Hapus soal ini dari ujian?')) { document.getElementById('detach-form-{{ $soal->id }}').submit(); }"
                                            class="text-gray-400 hover:text-red-655 hover:bg-red-50 p-2 rounded-lg transition-all cursor-pointer" title="Lepaskan Soal">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                        <button type="submit"
                                class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer select-none">
                            Simpan Perubahan Urutan & Bobot
                        </button>
                    </div>
                </form>

                <!-- Hidden Detach Forms -->
                @foreach($attachedSoals as $soal)
                    <form id="detach-form-{{ $soal->id }}" action="{{ route('guru.learning-modules.ujians.soals.detach', [$learningModule->id, $ujian->id, $soal->id]) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            @else
                <div class="text-center py-10 text-gray-405 italic border border-dashed border-gray-200 rounded-2xl">
                    Belum ada soal ditambahkan ke ujian ini. Klik "Pilih Dari Bank Soal" untuk menambahkan.
                </div>
            @endif
        </div>

        <!-- Add Soal Modal -->
        <div x-show="showAddSoalModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showAddSoalModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showAddSoalModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            
            <div x-show="showAddSoalModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-2xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                
                <div class="p-4 sm:p-6 lg:p-8 max-h-[85vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-base font-bold text-gray-800">Pilih Pertanyaan dari Bank Soal</h3>
                        <div class="relative w-48">
                            <input type="text" x-model="searchQuery" placeholder="Cari soal..." 
                                   class="w-full pl-8 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-[11px] text-gray-700 focus:outline-none focus:border-[#0c2b4d] transition-all">
                            <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-6">Hanya menampilkan soal-soal untuk mata pelajaran <strong>{{ $learningModule->mataPelajaran->nama_pelajaran }}</strong> yang belum ditambahkan ke ujian ini.</p>
                    
                    @if($availableSoals->isNotEmpty())
                        <form action="{{ route('guru.learning-modules.ujians.soals.attach', [$learningModule->id, $ujian->id]) }}" method="POST">
                            @csrf
                            <div class="divide-y divide-gray-100 border border-gray-200 rounded-xl overflow-hidden bg-white max-h-96 overflow-y-auto mb-6">
                                @foreach($availableSoals as $soal)
                                    <label x-show="searchQuery === '' || '{{ strtolower(addslashes($soal->pertanyaan)) }}'.includes(searchQuery.toLowerCase())"
                                           class="p-4 flex items-start gap-3.5 hover:bg-gray-50/50 transition-colors cursor-pointer select-none">
                                        <input type="checkbox" name="soal_ids[]" value="{{ $soal->id }}"
                                               class="mt-1 w-4 h-4 text-[#0c2b4d] border-gray-300 rounded focus:ring-[#0c2b4d]">
                                        <div class="min-w-0">
                                            <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-bold border uppercase tracking-wide mb-1.5 {{ $soal->tipe === 'pg' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-purple-50 text-purple-700 border-purple-100' }}">
                                                {{ $soal->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}
                                            </span>
                                            <p class="text-xs text-gray-800 leading-relaxed font-bold whitespace-pre-wrap">{!! nl2br(e($soal->pertanyaan)) !!}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                <button type="button" @click="showAddSoalModal = false"
                                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-4 py-2.5 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
                                    Tambahkan Soal Terpilih
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8 text-xs text-gray-400 italic">
                            Semua soal dari Bank Soal Anda untuk mata pelajaran ini sudah ditambahkan, atau Bank Soal Anda kosong. Silakan isi Bank Soal terlebih dahulu.
                        </div>
                        <div class="flex justify-end pt-4 border-t border-gray-100 mt-6">
                            <button type="button" @click="showAddSoalModal = false"
                                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
