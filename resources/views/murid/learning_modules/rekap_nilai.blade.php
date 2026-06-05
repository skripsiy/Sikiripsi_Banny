<x-app-layout>
    <x-slot name="header">
        {{ __('Rekap Nilai Tugas: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('murid.learning-modules.show', $learningModule->id) }}" 
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
                        {{ $learningModule->subject->nama_pelajaran }} ({{ $learningModule->subject->kode_pelajaran }})
                    </span>
                    <span class="bg-rose-500/20 text-rose-200 border border-rose-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Laporan Nilai Tugas
                    </span>
                </div>
                <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">{{ $learningModule->description }}</p>
            </div>
        </div>

        <!-- Summary Stats Cards -->
        @php
            $totalTugas = $tugas->count();
            $submittedCount = 0;
            $gradedCount = 0;
            $totalNilai = 0;

            foreach($tugas as $tgs) {
                if($tgs->submissions->isNotEmpty()) {
                    $submittedCount++;
                    $sub = $tgs->submissions->first();
                    if($sub->nilai !== null) {
                        $gradedCount++;
                        $totalNilai += $sub->nilai;
                    }
                }
            }

            $averageNilai = $gradedCount > 0 ? round($totalNilai / $gradedCount, 1) : 0;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Assignments Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Tugas</span>
                    <h3 class="text-3xl font-extrabold text-[#0c2b4d] mt-1">{{ $totalTugas }}</h3>
                </div>
                <div class="p-3 bg-blue-50 text-[#0c2b4d] rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path>
                    </svg>
                </div>
            </div>

            <!-- Submitted Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tugas Dikumpulkan</span>
                    <h3 class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $submittedCount }} <span class="text-sm font-semibold text-gray-400">/ {{ $totalTugas }}</span></h3>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Average Grade Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nilai Rata-rata</span>
                    <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $averageNilai }} <span class="text-xs text-gray-400">/ 100</span></h3>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4">Laporan Rincian Tugas</h3>
            
            @if($tugas->isNotEmpty())
                <div class="overflow-x-auto border border-gray-150 rounded-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Tugas</th>
                                <th class="px-6 py-4">Batas Waktu</th>
                                <th class="px-6 py-4">Status Pengumpulan</th>
                                <th class="px-6 py-4">Nilai</th>
                                <th class="px-6 py-4">Catatan Guru / Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @foreach($tugas as $tgs)
                                @php
                                    $submission = $tgs->submissions->first();
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-[#0c2b4d]">{{ $tgs->title }}</div>
                                        <div class="text-[10px] text-gray-405 mt-0.5 max-w-[250px] truncate" title="{{ $tgs->instructions }}">
                                            {{ $tgs->instructions }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $tgs->due_date ? $tgs->due_date->translatedFormat('d F Y H:i') : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($submission)
                                            <div class="flex flex-col gap-1 items-start">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-150 font-bold uppercase text-[9px] tracking-wider">
                                                    Dikumpulkan
                                                </span>
                                                <span class="text-[9px] text-gray-400">Tgl: {{ $submission->submitted_at->translatedFormat('d M H:i') }}</span>
                                            </div>
                                        @else
                                            @if($tgs->due_date && $tgs->due_date->isPast())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-150 font-bold uppercase text-[9px] tracking-wider">
                                                    Terlewat (Tidak Kumpul)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-50 text-gray-550 border border-gray-200 font-bold uppercase text-[9px] tracking-wider">
                                                    Belum Dikumpulkan
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($submission && $submission->nilai !== null)
                                            <span class="inline-flex items-center px-3 py-1 bg-emerald-500 text-white rounded-lg font-extrabold text-sm shadow-sm select-none">
                                                {{ $submission->nilai }}
                                            </span>
                                        @elseif($submission)
                                            <span class="text-amber-600 font-semibold italic">Menunggu penilaian</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($submission && $submission->catatan_guru)
                                            <p class="text-gray-700 bg-amber-50/50 p-2.5 rounded-xl border border-amber-100 italic max-w-sm leading-relaxed">
                                                "{{ $submission->catatan_guru }}"
                                            </p>
                                        @else
                                            <span class="text-gray-400 italic">Tidak ada catatan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10 text-gray-400 italic">Belum ada tugas di modul ini.</div>
            @endif
        </div>

    </div>
</x-app-layout>
