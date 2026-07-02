<x-app-layout>
    <x-slot name="header">
        {{ __('Hasil Ujian: ' . $ujian->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        activeAttempt: null,
        showGradeModal: false,
        gradeAttemptData: {
            id: '',
            name: '',
            answers: []
        }
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

        <!-- Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $learningModule->mataPelajaran->nama_pelajaran }}
                    </span>
                    <span class="bg-amber-500/20 text-amber-200 border border-amber-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Hasil Pengerjaan Ujian
                    </span>
                </div>
                <h2 class="text-xl font-bold mt-2.5">Rekap Nilai & Jawaban - {{ $ujian->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">Lihat hasil pengerjaan ujian murid, nilai otomatis, dan berikan penilaian manual untuk soal esai.</p>
            </div>
        </div>

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- Results Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if($attempts->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-[10px] font-bold uppercase tracking-wider border-b border-gray-100">
                                <th class="py-4 px-6 w-12 text-center">No</th>
                                <th class="py-4 px-6">Nama Murid</th>
                                <th class="py-4 px-6">Waktu Mulai</th>
                                <th class="py-4 px-6">Waktu Selesai</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-center">Nilai / Skor</th>
                                <th class="py-4 px-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700 font-medium font-sans">
                            @foreach($attempts as $index => $att)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6 text-center text-gray-400 font-bold">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-800">{{ $att->murid->user->name }}</div>
                                        <div class="text-[10px] text-gray-400">NISN: {{ $att->murid->nisn }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500">
                                        {{ $att->started_at->translatedFormat('d M Y H:i') }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-500">
                                        {{ $att->finished_at ? $att->finished_at->translatedFormat('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold border uppercase {{ $att->status === 'graded' ? 'bg-green-50 text-green-700 border-green-150' : ($att->status === 'submitted' ? 'bg-blue-50 text-blue-700 border-blue-150' : 'bg-gray-50 text-gray-400 border-gray-150') }}">
                                            {{ $att->status === 'graded' ? 'Dinilai' : ($att->status === 'submitted' ? 'Dikirim' : 'Sedang Mengerjakan') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="text-sm font-extrabold {{ $att->skor >= 75 ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $att->skor !== null ? number_format($att->skor, 2) : '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($att->status !== 'in_progress')
                                            <button @click="
                                                showGradeModal = true;
                                                gradeAttemptData = {
                                                    id: '{{ $att->id }}',
                                                    name: {{ json_encode($att->murid->user->name) }},
                                                    actionUrl: '{{ route('guru.learning-modules.ujians.grade-essay', [$learningModule->id, $ujian->id, $att->id]) }}',
                                                    answers: [
                                                        @foreach($att->answers as $ans)
                                                            {
                                                                id: '{{ $ans->id }}',
                                                                pertanyaan: {{ json_encode($ans->bankSoal->pertanyaan) }},
                                                                tipe: '{{ $ans->bankSoal->tipe }}',
                                                                jawaban_pg: '{{ $ans->jawaban_pg }}',
                                                                jawaban_essay: {{ json_encode($ans->jawaban_essay) }},
                                                                correct_option: '{{ $ans->bankSoal->tipe === 'pg' ? ($ans->bankSoal->options->firstWhere('is_correct', true)->label ?? '') : '' }}',
                                                                is_correct: {{ $ans->is_correct ? 'true' : 'false' }},
                                                                skor_manual: '{{ $ans->skor_manual }}',
                                                                bobot: {{ $ujian->soals->firstWhere('id', $ans->bank_soal_id)->pivot->bobot ?? 1 }}
                                                            },
                                                        @endforeach
                                                    ]
                                                }
                                            " class="bg-gray-100 hover:bg-[#0c2b4d] hover:text-white text-gray-700 px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all cursor-pointer">
                                                Detail & Nilai
                                            </button>
                                        @else
                                            <span class="text-[11px] text-gray-400 italic">Murid sedang ujian</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-14 text-gray-405 italic">
                    Belum ada murid yang memulai ujian ini.
                </div>
            @endif
        </div>

        <!-- Grade & Detail Modal -->
        <div x-show="showGradeModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showGradeModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showGradeModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            
            <div x-show="showGradeModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-2xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                
                <div class="p-4 sm:p-6 lg:p-8 max-h-[85vh] overflow-y-auto">
                    <h3 class="text-base font-bold text-gray-800 mb-1">Lembar Jawaban Murid</h3>
                    <p class="text-[11px] text-gray-400 mb-6" x-text="'Review jawaban untuk: ' + gradeAttemptData.name"></p>
                    
                    <form x-bind:action="gradeAttemptData.actionUrl" method="POST" class="space-y-6">
                        @csrf
                        
                        <template x-for="(ans, index) in gradeAttemptData.answers" :key="ans.id">
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-150 space-y-3">
                                <div class="flex justify-between items-start gap-4">
                                    <span class="text-xs font-bold text-gray-500" x-text="'Soal ' + (index + 1)"></span>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-bold border uppercase tracking-wide"
                                          :class="ans.tipe === 'pg' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-purple-50 text-purple-700 border-purple-100'"
                                          x-text="ans.tipe === 'pg' ? 'Pilihan Ganda' : 'Essay'"></span>
                                </div>

                                <p class="text-xs text-gray-800 font-bold whitespace-pre-wrap" x-text="ans.pertanyaan"></p>

                                <div class="p-3 bg-white border border-gray-100 rounded-lg text-xs">
                                    <div class="text-[11px] text-gray-400 font-bold mb-1">Jawaban Murid:</div>
                                    <template x-if="ans.tipe === 'pg'">
                                        <div>
                                            <span class="font-extrabold text-sm" :class="ans.is_correct ? 'text-green-600' : 'text-red-500'" x-text="ans.jawaban_pg || 'Tidak Dijawab'"></span>
                                            <span class="text-gray-400 ml-1.5" x-text="'(Kunci: ' + ans.correct_option + ')'"></span>
                                        </div>
                                    </template>
                                    <template x-if="ans.tipe === 'essay'">
                                        <p class="text-gray-800 whitespace-pre-wrap" x-text="ans.jawaban_essay || 'Tidak Dijawab'"></p>
                                    </template>
                                </div>

                                <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
                                    <span class="text-gray-450" x-text="'Bobot Soal: ' + ans.bobot"></span>
                                    
                                    <div class="flex items-center gap-2">
                                        <template x-if="ans.tipe === 'pg'">
                                            <span class="font-bold text-gray-700" x-text="ans.is_correct ? 'Skor: ' + ans.bobot : 'Skor: 0'"></span>
                                        </template>
                                        <template x-if="ans.tipe === 'essay'">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-500">Input Skor (0 - <span x-text="ans.bobot"></span>):</span>
                                                <input type="number" :name="'skor[' + ans.id + ']'" x-bind:value="ans.skor_manual !== '' ? ans.skor_manual : 0"
                                                       required step="0.01" min="0" :max="ans.bobot"
                                                       class="w-16 px-2 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-center text-gray-750 focus:outline-none focus:border-[#0c2b4d]">
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showGradeModal = false"
                                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                Tutup
                            </button>
                            <button type="submit"
                                    class="px-4 py-2.5 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
                                Simpan Nilai Essay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
