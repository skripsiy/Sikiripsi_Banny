<x-app-layout>
    <x-slot name="header">
        {{ __('Mengerjakan Kuis: ' . $quiz->title) }}
    </x-slot>

    <!-- Full Test Interface -->
    <div class="max-w-7xl mx-auto font-sans" x-data="{
        currentQuestionIndex: 0,
        totalQuestions: {{ $soals->count() }},
        jawaban: {},
        init() {
            // Count down timer
            const startedAt = new Date('{{ $attempt->started_at->toIso8601String() }}').getTime();
            const durationMs = {{ $quiz->duration_minutes }} * 60 * 1000;
            const expiryTime = startedAt + durationMs;

            const timerInterval = setInterval(() => {
                const now = new Date().getTime();
                const diff = expiryTime - now;

                if (diff <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timer-display').innerText = '00:00';
                    document.getElementById('quiz-form').submit();
                } else {
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    
                    const minStr = String(minutes).padStart(2, '0');
                    const secStr = String(seconds).padStart(2, '0');
                    
                    document.getElementById('timer-display').innerText = `${minStr}:${secStr}`;
                    
                    // Warning coloring
                    if (diff < 60 * 1000) {
                        document.getElementById('timer-card').classList.add('bg-red-50', 'border-red-200', 'text-red-600');
                    }
                }
            }, 1000);
        }
    }">

        <!-- Top Sticky Stats Header -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Title & Info -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-150 p-5 shadow-sm flex flex-col justify-center">
                <h2 class="text-base font-extrabold text-gray-800">{{ $quiz->title }}</h2>
                <p class="text-[11px] text-gray-400 mt-1">Harap periksa kembali semua jawaban Anda sebelum menekan tombol Submit.</p>
            </div>

            <!-- Timer -->
            <div id="timer-card" class="bg-[#0c2b4d] text-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center justify-between transition-all duration-300">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider">Sisa Waktu:</span>
                </div>
                <div id="timer-display" class="text-2xl font-extrabold tracking-widest font-mono">--:--</div>
            </div>
        </div>

        <!-- Main Body: Questions & Navigation -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- Left: Question Content Panel -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm min-h-[400px] flex flex-col justify-between">
                <form id="quiz-form" action="{{ route('murid.learning-modules.quizzes.submit', [$learningModule->id, $quiz->id]) }}" method="POST">
                    @csrf
                    
                    @foreach($soals as $index => $soal)
                        <div x-show="currentQuestionIndex === {{ $index }}" class="space-y-6" style="display: none;">
                            <!-- Question Header -->
                            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                <span class="text-xs font-extrabold text-[#0c2b4d] uppercase tracking-wider" x-text="'Soal ' + ({{ $index }} + 1) + ' dari ' + totalQuestions"></span>
                                <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold border uppercase tracking-wider {{ $soal->tipe === 'pg' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-purple-50 text-purple-700 border-purple-100' }}">
                                    {{ $soal->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}
                                </span>
                            </div>

                            <!-- Question Pertanyaan -->
                            <div class="space-y-4">
                                <p class="text-sm text-gray-800 font-extrabold leading-relaxed whitespace-pre-wrap">{!! nl2br(e($soal->pertanyaan)) !!}</p>
                                
                                @if($soal->gambar_path)
                                    <div class="rounded-xl overflow-hidden border border-gray-150 max-w-md bg-gray-50 p-2">
                                        <img src="{{ asset('storage/' . $soal->gambar_path) }}" class="w-full h-auto object-contain rounded-lg" alt="Gambar Soal">
                                    </div>
                                @endif
                            </div>

                            <!-- Answer Options -->
                            <div class="pt-4">
                                @if($soal->tipe === 'pg')
                                    <div class="space-y-3">
                                        @foreach($soal->options as $opt)
                                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:bg-blue-50/30 hover:border-blue-300 transition-all cursor-pointer relative group"
                                                   :class="jawaban[{{ $soal->id }}] === '{{ $opt->label }}' ? 'bg-blue-50/50 border-blue-500 ring-1 ring-blue-500' : ''">
                                                <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $opt->label }}"
                                                       x-model="jawaban[{{ $soal->id }}]"
                                                       class="w-4 h-4 text-[#0c2b4d] focus:ring-[#0c2b4d] border-gray-300">
                                                <span class="text-xs font-bold text-gray-500 select-none">{{ $opt->label }}.</span>
                                                <span class="text-xs text-gray-750 font-medium select-none">{{ $opt->teks_opsi }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Ketik Jawaban Anda:</label>
                                        <textarea name="jawaban[{{ $soal->id }}]" rows="6" placeholder="Tulis jawaban esai lengkap di sini..."
                                                  x-model="jawaban[{{ $soal->id }}]"
                                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:bg-white focus:border-[#0c2b4d] transition-all placeholder-gray-400"></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Nav Buttons inside form (handled by Alpine) -->
                    <div class="flex justify-between items-center border-t border-gray-100 pt-6 mt-8">
                        <button type="button" @click="if(currentQuestionIndex > 0) { currentQuestionIndex-- }"
                                x-bind:disabled="currentQuestionIndex === 0"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 disabled:opacity-50 text-gray-700 disabled:cursor-not-allowed rounded-xl text-xs font-bold transition-all cursor-pointer select-none">
                            Sebelumnya
                        </button>
                        
                        <button type="button" @click="if(currentQuestionIndex < totalQuestions - 1) { currentQuestionIndex++ }"
                                x-show="currentQuestionIndex < totalQuestions - 1"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all cursor-pointer select-none">
                            Selanjutnya
                        </button>

                        <button type="submit" x-show="currentQuestionIndex === totalQuestions - 1" style="display: none;"
                                onclick="return confirm('Apakah Anda yakin ingin menyelesaikan kuis ini? Jawaban Anda akan langsung dikirim.');"
                                class="px-5 py-2.5 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-xs font-bold transition-all cursor-pointer select-none shadow-sm">
                            Submit Jawaban
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Navigation Grid Panel -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Navigasi Soal</h3>
                
                <div class="grid grid-cols-5 gap-2.5">
                    @foreach($soals as $index => $soal)
                        <button type="button" @click="currentQuestionIndex = {{ $index }}"
                                class="h-10 rounded-xl border font-extrabold text-xs flex items-center justify-center transition-all cursor-pointer select-none"
                                :class="[
                                    currentQuestionIndex === {{ $index }} ? 'ring-2 ring-[#0c2b4d] font-black' : '',
                                    jawaban[{{ $soal->id }}] ? 'bg-green-500 text-white border-green-500 shadow-sm' : 'bg-gray-50 hover:bg-gray-100 text-gray-500 border-gray-200'
                                ]"
                                x-text="{{ $index }} + 1">
                        </button>
                    @endforeach
                </div>

                <div class="mt-6 border-t border-gray-100 pt-4 space-y-2 text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 bg-green-500 rounded-md"></span>
                        <span>Sudah Dijawab</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 bg-gray-50 border border-gray-200 rounded-md"></span>
                        <span>Belum Dijawab</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
