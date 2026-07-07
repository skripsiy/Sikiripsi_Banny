<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Tugas: ' . $tuga->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-5">
            <a href="{{ route('murid.learning-modules.tugas.index', [$learningModule->id, 'semester_id' => request('semester_id')]) }}"
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-extrabold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Tugas
            </a>
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

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- Left Column: Task Details (Spans 2 columns on lg screens) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Task Header & Instructions -->
                <div class="bg-white shadow-md rounded-2xl border border-gray-150 p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-2 mb-4 select-none">
                        <span class="bg-blue-50 text-[#0c2b4d] border border-blue-100 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ $learningModule->mataPelajaran->nama_pelajaran }}
                        </span>
                        <span class="bg-gray-100 text-gray-600 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            Tahun Ajaran: {{ $learningModule->tahunAkademik->tahun_ajaran }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-black text-gray-850 leading-tight mb-2">{{ $tuga->title }}</h1>
                    
                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 mb-6 border-b border-gray-100 pb-4 select-none">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Dipublikasikan: <strong>{{ $tuga->created_at->translatedFormat('d F Y H:i') }}</strong>
                        </span>
                        @if($tuga->due_date)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                Tenggat: <strong class="text-rose-600 font-bold">{{ $tuga->due_date->translatedFormat('d F Y H:i') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest select-none">Petunjuk & Deskripsi Tugas</h3>
                        <div class="wysiwyg-content text-xs text-gray-650 leading-relaxed font-semibold">
                            {!! $tuga->instructions !!}
                        </div>
                    </div>

                    @if ($tuga->file_path)
                        <div class="mt-6 pt-5 border-t border-gray-100 select-none">
                            <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider block mb-2">Lampiran Dokumen:</span>
                            <a href="{{ asset('storage/' . $tuga->file_path) }}" target="_blank"
                               class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 text-xs font-bold bg-emerald-50 hover:bg-emerald-100/50 border border-emerald-100 px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                                </svg>
                                Unduh Lampiran Petunjuk Tugas
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Submission Status & Action Form (Spans 1 column on lg screens) -->
            <div class="space-y-6">
                <!-- Status & Grade Panel -->
                <div class="bg-white shadow-md rounded-2xl border border-gray-150 p-6 space-y-4">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest select-none">Status Pengumpulan</h3>

                    @if($submission)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <span class="text-xs text-gray-500 font-semibold">Status</span>
                                @if($submission->nilai !== null)
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-green-50 text-green-700 border border-green-200 uppercase tracking-wider">
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-[#0c2b4d] border border-blue-150 uppercase tracking-wider">
                                        Menunggu Dinilai
                                    </span>
                                @endif
                            </div>

                            @if($submission->nilai !== null)
                                <div class="bg-gradient-to-br from-green-50 to-emerald-50/30 rounded-xl p-4 border border-green-150 flex items-center justify-between select-none">
                                    <div>
                                        <span class="text-[9px] text-green-700 font-bold uppercase tracking-wider block">Nilai Tugas</span>
                                        <span class="text-2xl font-black text-green-800">{{ $submission->nilai }}<span class="text-xs text-green-600 font-normal"> / 100</span></span>
                                    </div>
                                    <div class="w-10 h-10 bg-green-100 text-green-700 rounded-full flex items-center justify-center border border-green-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"></path>
                                        </svg>
                                    </div>
                                </div>
                            @endif

                            <div class="text-[11px] text-gray-500 space-y-1 font-medium">
                                <div class="flex justify-between">
                                    <span>Dikirim Pada:</span>
                                    <span class="text-gray-700 font-bold">{{ $submission->submitted_at->translatedFormat('d M Y H:i') }}</span>
                                </div>
                                @if($submission->file_path)
                                    <div class="flex justify-between items-center pt-2">
                                        <span>File Jawaban:</span>
                                        <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank"
                                           class="text-[#0c2b4d] hover:underline font-bold flex items-center gap-0.5">
                                            Unduh File
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @if($submission->catatan_murid)
                                <div class="bg-gray-50/60 rounded-xl p-3 border border-gray-150 mt-2">
                                    <span class="text-[9px] text-gray-400 font-bold block uppercase tracking-wider mb-1">Catatan Anda</span>
                                    <p class="text-gray-750 italic leading-relaxed whitespace-pre-wrap text-[11px] font-medium">{{ $submission->catatan_murid }}</p>
                                </div>
                            @endif

                            @if($submission->catatan_guru)
                                <div class="bg-amber-50 rounded-xl p-3 border border-amber-200 mt-2">
                                    <span class="text-[9px] text-amber-600 font-bold block uppercase tracking-wider mb-1 select-none">Catatan Guru</span>
                                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap text-[11px] font-semibold">{{ $submission->catatan_guru }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Unsubmitted Status -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 font-semibold">Status</span>
                                @if($tuga->due_date && $tuga->due_date->isPast())
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-red-50 text-red-700 border border-red-150 uppercase tracking-wider">
                                        Terlewat
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-gray-50 text-gray-500 border border-gray-250 uppercase tracking-wider">
                                        Belum Dikumpulkan
                                    </span>
                                @endif
                            </div>

                            @if($tuga->due_date && $tuga->due_date->isPast())
                                <div class="text-[11px] text-red-700 font-medium bg-red-50 border border-red-100 rounded-xl p-3 leading-relaxed flex gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span>Batas waktu pengumpulan tugas sudah terlewat. Anda tidak dapat lagi mengumpulkan tugas ini.</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Form Upload Tugas (Only if not submitted and active) -->
                @if(!$submission && (!$tuga->due_date || !$tuga->due_date->isPast()))
                    <div class="bg-white shadow-md rounded-2xl border border-gray-150 p-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 select-none">Kumpulkan Tugas</h3>
                        
                        <form action="{{ route('murid.learning-modules.tugas.submit', [$learningModule->id, $tuga->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-gray-550 uppercase tracking-wider mb-2 select-none">
                                    Unggah Berkas (PDF, DOC, DOCX, PPT, PPTX. Maks. 10MB)
                                </label>
                                <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx"
                                       class="block w-full text-xs text-gray-550 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-[#0c2b4d] hover:file:bg-blue-100/80 transition-all cursor-pointer border border-gray-200 rounded-xl p-1 bg-white focus:outline-none w-full">
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-bold text-gray-550 uppercase tracking-wider mb-2 select-none">
                                    Catatan / Jawaban Tambahan
                                </label>
                                <textarea name="catatan_murid" rows="4" placeholder="Tulis jawaban teks atau catatan pendukung di sini..."
                                          class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs text-gray-800 font-semibold leading-relaxed transition-all shadow-sm"></textarea>
                            </div>

                            <div class="pt-1">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[#0c2b4d] hover:bg-[#081d33] text-white text-xs font-bold rounded-xl transition-all shadow-md cursor-pointer select-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Kumpulkan Tugas
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
