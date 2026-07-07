<x-app-layout>
    <x-slot name="header">
        {{ __('Tugas: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        searchQuery: ''
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
                    <span class="bg-rose-500/20 text-rose-200 border border-rose-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Tugas Pembelajaran
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

        <!-- Alerts -->
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

        <!-- Main Content Area -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Daftar Tugas / Homework</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perhatikan batas waktu pengerjaan tugas di bawah ini.</p>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Cari tugas..."
                               class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-250 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs text-gray-800 transition-all">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            @if($tugas->isNotEmpty())
                <div class="divide-y divide-gray-100 border border-gray-200 rounded-2xl overflow-hidden bg-white">
                    @foreach($tugas as $tgs)
                        @php
                            $submission = $tgs->submissions->first();
                            $isPast = $tgs->due_date ? $tgs->due_date->isPast() : false;
                        @endphp
                        <div x-show="searchQuery === '' || '{{ strtolower(addslashes($tgs->title)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes(strip_tags($tgs->instructions))) }}'.includes(searchQuery.toLowerCase())"
                             class="p-5 hover:bg-gray-50/20 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative group/item">

                            <div class="flex items-start gap-4 min-w-0 flex-grow">
                                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5 border border-rose-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <h4 class="font-extrabold text-gray-800 text-sm">{{ $tgs->title }}</h4>
                                    <p class="text-xs text-gray-400 mt-1 line-clamp-1 font-medium">{{ strip_tags($tgs->instructions) }}</p>

                                    <!-- Status Badges -->
                                    <div class="mt-2.5 flex flex-wrap gap-2 items-center">
                                        @if($submission)
                                            @if($submission->nilai !== null)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold bg-green-50 text-green-700 border border-green-150 px-2.5 py-0.5 rounded-lg select-none">
                                                    Nilai: {{ $submission->nilai }}/100
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-150 px-2.5 py-0.5 rounded-lg select-none">
                                                    Sudah Dikumpulkan (Belum Dinilai)
                                                </span>
                                            @endif
                                        @else
                                            @if($isPast)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-red-50 text-red-700 border border-red-150 px-2.5 py-0.5 rounded-lg select-none">
                                                    Tidak Mengumpulkan (Terlewat)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-gray-50 text-gray-500 border border-gray-200 px-2.5 py-0.5 rounded-lg select-none">
                                                    Belum Dikumpulkan
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:items-end gap-1.5 self-end sm:self-center flex-shrink-0">
                                <div class="flex flex-col gap-1 text-[10px] text-gray-400 font-medium">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                        <span>Rilis: <strong class="text-gray-600">{{ $tgs->created_at->translatedFormat('d M Y H:i') }}</strong></span>
                                    </span>
                                    @if($tgs->due_date)
                                        <span class="inline-flex text-[9px] uppercase tracking-wider px-2 py-0.5 rounded font-bold border whitespace-nowrap {{ $isPast ? 'bg-red-50 text-red-600 border-red-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                            Batas: {{ $tgs->due_date->translatedFormat('d M Y H:i') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-2.5">
                                    <a href="{{ route('murid.learning-modules.tugas.show', [$learningModule->id, $tgs->id, 'semester_id' => $selectedSemester?->id]) }}"
                                       class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-[#0c2b4d] hover:bg-[#081d33] text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer select-none">
                                        Detail & Kumpulkan
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-sm text-gray-450 italic border border-dashed border-gray-200 rounded-2xl">
                    Belum ada tugas yang ditambahkan.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
