<x-app-layout>
    <x-slot name="header">
        {{ __('Ujian Evaluasi: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        searchQuery: '',
        showConfirmModal: false,
        confirmActionUrl: ''
    }">

        <!-- Back Button (outside header) -->
        <div class="mb-4">
            <a href="{{ route('murid.learning-modules.show', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Dashboard Modul
            </a>
        </div>

        <!-- Module Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $learningModule->mataPelajaran->nama_pelajaran }} ({{ $learningModule->mataPelajaran->kode_pelajaran }})
                    </span>
                    <span class="bg-purple-500/20 text-purple-200 border border-purple-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Ujian Evaluasi
                    </span>
                    @if($selectedSemester)
                        <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Semester: {{ ucfirst($selectedSemester->semester) }}
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">{{ $learningModule->description }}</p>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Daftar Ujian Evaluasi</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Ikuti petunjuk dan pastikan Anda mengerjakan ujian sesuai batas waktu.</p>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Cari ujian..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-250 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs text-gray-800 transition-all">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            @if($ujians->isNotEmpty())
                <div class="divide-y divide-gray-100 border border-gray-200 rounded-2xl overflow-hidden bg-white">
                    @foreach($ujians as $ujian)
                        <div x-show="searchQuery === '' || '{{ strtolower(addslashes($ujian->title)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($ujian->instructions)) }}'.includes(searchQuery.toLowerCase())"
                             class="p-5 hover:bg-gray-50/20 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative">
                            
                            <div class="flex items-start gap-4 min-w-0 flex-grow">
                                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 mt-0.5 border border-purple-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <h4 class="font-extrabold text-gray-800 text-sm">{{ $ujian->title }}</h4>
                                    <p class="text-xs text-gray-650 mt-2 leading-relaxed whitespace-pre-wrap font-medium">{{ $ujian->instructions }}</p>
                                    
                                     <div class="mt-3 flex items-center gap-2">
                                         <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-md select-none">
                                             <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                             </svg>
                                             <span>Durasi: {{ $ujian->duration_minutes }} Menit</span>
                                         </span>
                                         <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100 px-2 py-0.5 rounded-md select-none">
                                             <span>Jumlah Soal: {{ $ujian->soals_count }} Soal</span>
                                         </span>

                                         <!-- Attempt status badges -->
                                         @if($att = $attempts[$ujian->id] ?? null)
                                             @if($att->status === 'graded')
                                                 <span class="inline-flex items-center gap-1 text-[10px] font-extrabold bg-green-50 text-green-700 border border-green-100 px-2 py-0.5 rounded-md">
                                                     Nilai: {{ number_format($att->skor, 2) }}
                                                 </span>
                                             @elseif($att->status === 'submitted')
                                                 <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-md">
                                                     Sudah Dikumpulkan
                                                 </span>
                                             @else
                                                 <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-50 text-amber-700 border-amber-100 px-2 py-0.5 rounded-md">
                                                     Sedang Dikerjakan
                                                 </span>
                                             @endif
                                         @endif
                                     </div>
                                 </div>
                             </div>

                             <div class="flex flex-col sm:items-end gap-1.5 self-end sm:self-center flex-shrink-0">
                                 @if($ujian->due_date)
                                     @php
                                         $isPast = $ujian->due_date->isPast();
                                     @endphp
                                     <span class="inline-flex text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-xl font-bold border whitespace-nowrap {{ $isPast ? 'bg-red-50 text-red-700 border-red-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                         Batas: {{ $ujian->due_date->translatedFormat('d F Y H:i') }} ({{ $isPast ? 'Terlewat' : 'Aktif' }})
                                     </span>
                                 @endif

                                 <!-- Action Buttons -->
                                 <div class="mt-2.5">
                                     @if($att = $attempts[$ujian->id] ?? null)
                                         @if($att->status === 'in_progress' && !$att->isExpired())
                                             <a href="{{ route('murid.learning-modules.ujians.take', [$learningModule->id, $ujian->id]) }}"
                                                class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer select-none whitespace-nowrap inline-block">
                                                 Lanjutkan Ujian
                                             </a>
                                         @else
                                             <a href="{{ route('murid.learning-modules.ujians.result', [$learningModule->id, $ujian->id]) }}"
                                                class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer select-none whitespace-nowrap inline-block">
                                                 Lihat Hasil
                                             </a>
                                         @endif
                                     @else
                                         @if($ujian->soals_count === 0)
                                             <span class="text-xs text-gray-400 italic">Belum ada soal</span>
                                         @elseif($ujian->due_date && $ujian->due_date->isPast())
                                             <span class="text-xs text-red-500 font-bold">Waktu Habis</span>
                                         @else
                                            <button type="button" @click="confirmActionUrl = '{{ route('murid.learning-modules.ujians.start', [$learningModule->id, $ujian->id]) }}'; showConfirmModal = true"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer select-none whitespace-nowrap">
                                                Mulai Kerjakan
                                            </button>
                                         @endif
                                     @endif
                                 </div>
                             </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-sm text-gray-450 italic border border-dashed border-gray-200 rounded-2xl">
                    Belum ada ujian evaluasi yang ditambahkan.
                </div>
            @endif
        </div>

        <!-- Confirmation Modal -->
        <div x-show="showConfirmModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showConfirmModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-md mx-auto z-10 border border-gray-150">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-6 text-center">
                    <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-purple-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-800 mb-2">Mulai Ujian?</h3>
                    <p class="text-xs text-gray-500 leading-relaxed px-4">
                        Apakah Anda yakin ingin memulai ujian ini sekarang? Waktu pengerjaan (timer) akan langsung berjalan dan tidak dapat dihentikan.
                    </p>
                    
                    <form x-bind:action="confirmActionUrl" method="POST" class="mt-6 flex justify-center gap-3">
                        @csrf
                        <button type="button" @click="showConfirmModal = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
                            Mulai Kerjakan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
