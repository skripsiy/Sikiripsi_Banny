<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Tugas: ' . $tuga->title) }}
    </x-slot>

    <div class="max-w-4xl mx-auto font-sans">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('murid.learning-modules.tugas.index', [$learningModule->id, 'semester_id' => request('semester_id')]) }}"
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Tugas
            </a>
        </div>

        <!-- Tugas Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6">
            <div class="flex flex-wrap gap-2 items-center">
                <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    {{ $learningModule->mataPelajaran->nama_pelajaran }}
                </span>
                @if($tuga->due_date)
                    @php
                        $isPast = $tuga->due_date->isPast();
                    @endphp
                    <span class="inline-flex text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full font-bold border whitespace-nowrap {{ $isPast ? 'bg-red-500/20 text-red-200 border-red-500/30' : 'bg-amber-500/20 text-amber-200 border-amber-500/30' }}">
                        Batas: {{ $tuga->due_date->translatedFormat('d F Y H:i') }} ({{ $isPast ? 'Terlewat' : 'Aktif' }})
                    </span>
                @endif
                
                <!-- Status Badge -->
                @if($submission)
                    @if($submission->nilai !== null)
                        <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Selesai (Nilai: {{ $submission->nilai }}/100)
                        </span>
                    @else
                        <span class="bg-indigo-500/20 text-indigo-200 border border-indigo-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Terkumpul (Belum Dinilai)
                        </span>
                    @endif
                @else
                    @if($tuga->due_date && $tuga->due_date->isPast())
                        <span class="bg-red-500/20 text-red-200 border border-red-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Tidak Mengumpulkan
                        </span>
                    @else
                        <span class="bg-gray-500/20 text-gray-200 border border-gray-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Belum Mengumpulkan
                        </span>
                    @endif
                @endif
            </div>
            <h2 class="text-xl font-bold mt-2.5">{{ $tuga->title }}</h2>
            <div class="text-xs text-blue-100/70 mt-1 flex items-center gap-1 select-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>Dipublikasikan: <strong>{{ $tuga->created_at->translatedFormat('d F Y H:i') }}</strong></span>
            </div>
        </div>

        <!-- Success/Error Alert Messages -->
        @if(session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-250 text-emerald-800 text-xs font-bold rounded-2xl flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-250 text-red-800 text-xs font-bold rounded-2xl flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-250 text-red-800 text-xs font-bold rounded-2xl">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 mb-6">
            <!-- Assignment Detail & Instructions -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-5 sm:p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3 select-none flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    Instruksi & Detail Tugas
                </h3>
                
                <div class="wysiwyg-content text-xs text-gray-650 leading-relaxed font-medium">
                    {!! $tuga->instructions !!}
                </div>

                @if ($tuga->file_path)
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <a href="{{ asset('storage/' . $tuga->file_path) }}" target="_blank"
                           class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 text-xs font-bold bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl transition-all shadow-sm select-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh File Petunjuk Tugas
                        </a>
                    </div>
                @endif
            </div>

            <!-- Submission Area -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-5 sm:p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 select-none flex items-center gap-1.5">
                    <svg class="w-4.5 h-4.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path>
                    </svg>
                    Status Pengumpulan & Jawaban Anda
                </h3>

                @if($submission)
                    <div class="space-y-4 text-xs">
                        <div class="flex flex-wrap gap-2.5 items-center bg-gray-50 p-4 rounded-xl border border-gray-150">
                            <div class="flex-grow">
                                <span class="text-[10px] text-gray-400 font-bold uppercase block tracking-wider">Status Pengumpulan:</span>
                                <div class="flex items-center gap-2 mt-1">
                                    @if($submission->nilai !== null)
                                        <span class="inline-flex items-center bg-emerald-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">
                                            Nilai: {{ $submission->nilai }}/100
                                        </span>
                                    @else
                                        <span class="inline-flex items-center bg-[#0c2b4d] text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">
                                            Sudah Dikumpulkan (Belum Dinilai)
                                        </span>
                                    @endif
                                    <span class="text-gray-500 font-medium ml-1">Dikirim pada: <strong>{{ $submission->submitted_at->translatedFormat('d F Y H:i') }}</strong></span>
                                </div>
                            </div>
                        </div>

                        @if($submission->file_path)
                            <div class="pt-1">
                                <span class="text-[10px] text-gray-400 font-bold uppercase block mb-1.5 tracking-wider">File Jawaban:</span>
                                <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-2 text-[#0c2b4d] hover:text-[#061424] font-bold text-xs bg-white border border-gray-200 px-4 py-2 rounded-xl transition-all shadow-sm">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Unduh File Jawaban Anda
                                </a>
                            </div>
                        @endif

                        @if($submission->catatan_murid)
                            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-150">
                                <span class="text-[10px] text-gray-400 font-bold block mb-1 uppercase tracking-wider select-none">Catatan Anda:</span>
                                <p class="text-gray-750 italic font-medium leading-relaxed whitespace-pre-wrap">{{ $submission->catatan_murid }}</p>
                            </div>
                        @endif

                        @if($submission->catatan_guru)
                            <div class="bg-amber-50 p-4 rounded-xl border border-amber-200">
                                <span class="text-[10px] text-amber-600 font-bold block mb-1.5 uppercase tracking-wider select-none">Feedback / Catatan Guru:</span>
                                <p class="text-gray-800 font-bold leading-relaxed whitespace-pre-wrap">{{ $submission->catatan_guru }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    @if($tuga->due_date && $tuga->due_date->isPast())
                        <div class="text-xs text-red-750 font-bold flex items-start gap-2 bg-red-50 border border-red-150 p-4 rounded-xl">
                            <svg class="w-5 h-5 flex-shrink-0 text-red-600 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <h4 class="font-bold text-red-800 text-sm select-none">Batas Waktu Terlewat</h4>
                                <p class="text-xs text-red-700 mt-0.5 font-medium leading-relaxed">Tugas ini sudah ditutup untuk pengumpulan karena batas waktu pengerjaan (deadline) telah terlewati.</p>
                            </div>
                        </div>
                    @else
                        <!-- Form Submission -->
                        <form action="{{ route('murid.learning-modules.tugas.submit', [$learningModule->id, $tuga->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-gray-550 uppercase tracking-wider mb-1.5 select-none">Unggah File Tugas (PDF, DOC, DOCX, PPT, PPTX. Maks. 10MB)</label>
                                <div class="relative flex items-center">
                                    <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx"
                                           class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#0c2b4d] hover:file:bg-blue-100/80 transition-all cursor-pointer border border-gray-250 rounded-xl p-1 bg-white focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-550 uppercase tracking-wider mb-1.5 select-none">Jawaban / Catatan Tambahan</label>
                                <textarea name="catatan_murid" rows="4" placeholder="Tulis jawaban teks atau catatan pendukung untuk guru Anda di sini..."
                                          class="w-full p-3.5 border border-gray-250 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs text-gray-800 font-semibold leading-relaxed transition-all shadow-sm"></textarea>
                            </div>
                            <div class="pt-2">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 bg-[#0c2b4d] hover:bg-[#081d33] text-white text-xs font-bold rounded-xl transition-all shadow-md cursor-pointer select-none">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Kumpulkan Tugas Sekarang
                                </button>
                            </div>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
