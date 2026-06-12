<x-app-layout>
    <x-slot name="header">
        {{ __('Pengumpulan Tugas: ' . $tuga->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showGradeModal: false,
        gradeData: {
            submission_id: '',
            student_name: '',
            nilai: '',
            catatan_guru: ''
        },
        gradeUrl: '',
        searchQuery: ''
    }">

        <!-- Back Button (outside header) -->
        <div class="mb-4">
            <a href="{{ route('guru.learning-modules.tugas.index', $learningModule->id) }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Tugas
            </a>
        </div>

        <!-- Tugas Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-6 rounded-2xl shadow-lg border border-gray-150 mb-6">
            <div class="flex flex-wrap gap-2">
                <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    {{ $learningModule->mataPelajaran->nama_pelajaran }}
                </span>
                <span class="bg-rose-500/20 text-rose-200 border border-rose-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Batas Waktu: {{ $tuga->due_date->translatedFormat('d F Y H:i') }}
                </span>
            </div>
            <h2 class="text-xl font-bold mt-2.5">{{ $tuga->title }}</h2>
            <p class="text-xs text-blue-100/70 mt-1 max-w-xl">{{ $tuga->instructions }}</p>
        </div>

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-250 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-250 text-red-800 rounded-2xl text-xs font-bold">
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
                <div>
                    <h3 class="text-base font-bold text-gray-800">Daftar Pengumpulan Murid</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar seluruh murid di kelas yang mengikuti modul ini.</p>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama murid..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-250 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs text-gray-800 transition-all">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto border border-gray-150 rounded-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-150 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Nama Murid</th>
                            <th class="px-6 py-4">NISN / Kelas</th>
                            <th class="px-6 py-4">File Tugas</th>
                            <th class="px-6 py-4">Status & Nilai</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @foreach($murids as $murid)
                            @php
                                $sub = $submissions->get($murid->id);
                            @endphp
                            <tr x-show="searchQuery === '' || '{{ strtolower(addslashes($murid->user->name ?? '')) }}'.includes(searchQuery.toLowerCase())"
                                class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-gray-800">{{ $murid->user->name ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-450 mt-0.5">{{ $murid->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-700">NISN: {{ $murid->nisn }}</div>
                                    <div class="text-[10px] text-gray-450 mt-0.5">{{ $murid->classroom->nama_kelas ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($sub)
                                        <div class="flex flex-col gap-1">
                                            <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank"
                                               class="inline-flex items-center gap-1.5 text-[#0c2b4d] hover:underline font-bold">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Unduh Jawaban
                                            </a>
                                            @if($sub->catatan_murid)
                                                <div class="text-[10px] text-gray-500 italic max-w-[200px] truncate" title="{{ $sub->catatan_murid }}">
                                                    "{{ $sub->catatan_murid }}"
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Belum mengumpulkan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($sub)
                                        <div class="flex flex-col gap-1 items-start">
                                            @if($sub->nilai !== null)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold uppercase text-[9px] tracking-wider">
                                                    Nilai: {{ $sub->nilai }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 font-bold uppercase text-[9px] tracking-wider">
                                                    Belum Dinilai
                                                </span>
                                            @endif
                                            <span class="text-[9px] text-gray-400">Tgl: {{ $sub->submitted_at->translatedFormat('d M Y H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-200 font-bold uppercase text-[9px] tracking-wider">
                                            Belum Ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($sub)
                                        <button @click="
                                            showGradeModal = true;
                                            gradeData = {
                                                submission_id: '{{ $sub->id }}',
                                                student_name: '{{ addslashes($murid->user->name ?? '') }}',
                                                nilai: '{{ $sub->nilai }}',
                                                catatan_guru: {{ json_encode($sub->catatan_guru ?? '') }}
                                            };
                                            gradeUrl = '{{ route('guru.learning-modules.tugas.grade', [$learningModule->id, $sub->id]) }}';
                                        " class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-3 py-1.5 rounded-xl font-bold text-[10px] transition-all cursor-pointer select-none">
                                            {{ $sub->nilai !== null ? 'Ubah Nilai' : 'Beri Nilai' }}
                                        </button>
                                    @else
                                        <button disabled class="bg-gray-100 text-gray-400 px-3 py-1.5 rounded-xl font-bold text-[10px] cursor-not-allowed select-none">
                                            Beri Nilai
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grade Modal -->
        <div x-show="showGradeModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showGradeModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showGradeModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showGradeModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-lg mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-6">
                    <h3 class="text-base font-extrabold text-gray-800 mb-1">Penilaian Tugas</h3>
                    <p class="text-xs text-gray-500 mb-6">Berikan nilai untuk murid: <strong class="text-gray-800" x-text="gradeData.student_name"></strong></p>

                    <form :action="gradeUrl" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nilai Tugas (0-100)</label>
                            <input type="number" name="nilai" min="0" max="100" required x-model="gradeData.nilai"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Catatan Feedback / Catatan Guru</label>
                            <textarea name="catatan_guru" rows="3" placeholder="Tulis feedback konstruktif untuk murid..." x-model="gradeData.catatan_guru"
                                      class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs font-semibold"></textarea>
                        </div>

                        <div class="flex justify-end gap-2.5 pt-4 border-t border-gray-100">
                            <button type="button" @click="showGradeModal = false"
                                    class="px-4 py-2 border border-gray-250 text-gray-550 rounded-xl text-xs font-bold hover:bg-gray-50 transition-colors select-none cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-xs font-bold transition-all shadow-sm select-none cursor-pointer">
                                Simpan Nilai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
