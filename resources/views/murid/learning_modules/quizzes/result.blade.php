<x-app-layout>
    <x-slot name="header">
        {{ __('Hasil Kuis: ' . $quiz->title) }}
    </x-slot>

    <div class="max-w-full mx-auto font-sans px-4 sm:px-6 lg:px-8">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('murid.learning-modules.quizzes.index', $learningModule->id) }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Kuis
            </a>
        </div>

        <!-- Prominent Score Banner Card -->
        <div class="bg-white rounded-3xl border border-gray-150 p-6 md:p-8 shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-6 relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 rounded-full opacity-50 select-none pointer-events-none"></div>
            
            <div class="space-y-3 text-left z-10">
                <span class="inline-flex px-3 py-1 bg-blue-50 text-[#0c2b4d] rounded-full text-[10px] font-bold border border-blue-100 uppercase tracking-widest">
                    {{ $learningModule->mataPelajaran->nama_pelajaran }}
                </span>
                
                <div>
                    <h2 class="text-lg font-extrabold text-gray-800">Hasil Ujian - {{ $quiz->title }}</h2>
                    <p class="text-xs text-gray-400 mt-1">Kuis Anda telah selesai dikerjakan dan dikirimkan.</p>
                </div>

                <div class="pt-1">
                    <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $attempt->status === 'graded' ? 'bg-green-50 text-green-700 border-green-150' : 'bg-blue-50 text-blue-700 border-blue-150' }}">
                        {{ $attempt->status === 'graded' ? 'Sudah Dinilai' : 'Menunggu Penilaian Essay' }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col items-center md:items-end justify-center select-none z-10">
                <span class="text-5xl font-black tracking-tight {{ $attempt->skor >= 75 ? 'text-green-600' : 'text-red-500' }}">
                    {{ number_format($attempt->skor ?? 0, 2) }}
                </span>
                <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest mt-1">Skor Akhir</span>
            </div>
        </div>

        <!-- Notification Status -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- List of Answers Review -->
        <div class="space-y-6">
            <h3 class="text-sm font-bold text-gray-850 uppercase tracking-wider pl-1 border-l-4 border-[#0c2b4d]">Review Soal & Jawaban</h3>

            @foreach($soals as $index => $soal)
                @php
                    $ans = $attempt->answers->firstWhere('bank_soal_id', $soal->id);
                @endphp
                <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-sm space-y-4">
                    
                    <!-- Question Header Info -->
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-xs font-extrabold text-gray-400" x-text="'Soal ' + ({{ $index }} + 1)"></span>
                        
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-bold border uppercase tracking-wider {{ $soal->tipe === 'pg' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-purple-50 text-purple-700 border-purple-100' }}">
                                {{ $soal->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}
                            </span>
                            
                            @if($soal->tipe === 'pg')
                                @if($ans && $ans->is_correct)
                                    <span class="bg-green-50 border border-green-150 text-green-700 text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Benar</span>
                                @else
                                    <span class="bg-red-50 border border-red-150 text-red-700 text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Salah</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="space-y-3">
                        <p class="text-xs text-gray-850 font-bold leading-relaxed whitespace-pre-wrap">{!! nl2br(e($soal->pertanyaan)) !!}</p>
                        @if($soal->gambar_path)
                            <div class="rounded-xl overflow-hidden border border-gray-150 max-w-sm bg-gray-50 p-1.5">
                                <img src="{{ asset('storage/' . $soal->gambar_path) }}" class="w-full h-auto object-contain rounded-lg" alt="Gambar Soal">
                            </div>
                        @endif
                    </div>

                    <!-- Options (PG) or Answers Display -->
                    <div class="mt-3 pl-1 space-y-3">
                        @if($soal->tipe === 'pg')
                            <div class="space-y-2">
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Opsi Jawaban:</div>
                                @foreach($soal->options as $opt)
                                    <div class="flex items-center gap-2 text-xs font-semibold {{ $opt->is_correct ? 'text-green-600 font-extrabold' : 'text-gray-500' }}">
                                        @if($ans && $ans->jawaban_pg === $opt->label)
                                            <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] border {{ $ans->is_correct ? 'bg-green-500 text-white border-green-500' : 'bg-red-500 text-white border-red-500' }}">✓</span>
                                        @else
                                            <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center text-[10px]">{{ $opt->label }}</span>
                                        @endif
                                        <span>{{ $opt->label }}. {{ $opt->teks_opsi }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Essay display -->
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Jawaban Anda:</div>
                            <p class="text-xs text-gray-800 leading-relaxed whitespace-pre-wrap font-medium">{{ $ans && $ans->jawaban_essay ? $ans->jawaban_essay : 'Tidak menjawab' }}</p>
                        @endif
                    </div>

                    <!-- Points section -->
                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                        <span>Bobot: {{ $soal->pivot->bobot ?? 1 }}</span>
                        
                        @if($soal->tipe === 'pg')
                            <span class="{{ $ans && $ans->is_correct ? 'text-green-600' : 'text-red-500' }}">
                                Skor: {{ $ans && $ans->is_correct ? ($soal->pivot->bobot ?? 1) : 0 }}
                            </span>
                        @else
                            @if($ans && $ans->skor_manual !== null)
                                <span class="text-green-600">
                                    Skor: {{ number_format($ans->skor_manual, 2) }}
                                </span>
                            @else
                                <span class="text-amber-600 italic">Menunggu penilaian</span>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>
