<x-app-layout>
    <x-slot name="header">
        {{ __('Riwayat Absensi: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans">

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
                    <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Riwayat Absensi Saya
                    </span>
                    @if($selectedSemester)
                        <span class="bg-indigo-500/20 text-indigo-200 border border-indigo-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
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
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-6">
                <h3 class="text-base font-bold text-gray-800">Log Kehadiran Pribadi</h3>
                <a href="{{ route('murid.learning-modules.izin.index', $learningModule->id) }}"
                   class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer select-none">
                    Ajukan Izin Digital
                </a>
            </div>

            @if ($absensis->isNotEmpty())
                <div class="overflow-x-auto border border-gray-150 rounded-2xl">
                    <table class="min-w-full divide-y divide-gray-150">
                        <thead class="bg-gray-50/75">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-16 select-none">No</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider select-none">Hari</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider select-none">Tanggal</th>
                                <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-400 uppercase tracking-wider w-48 select-none">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-xs font-semibold">
                            @foreach ($absensis as $index => $absensi)
                                <tr class="hover:bg-gray-50/40 transition-colors">
                                    <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $absensi->date->translatedFormat('l') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        {{ $absensi->date->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($absensi->status === 'hadir')
                                            <span class="inline-flex px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-extrabold bg-green-50 text-green-700 border border-green-200">
                                                Hadir
                                            </span>
                                        @elseif ($absensi->status === 'sakit')
                                            <span class="inline-flex px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-extrabold bg-orange-50 text-orange-700 border border-orange-200">
                                                Sakit
                                            </span>
                                        @elseif ($absensi->status === 'izin')
                                            <span class="inline-flex px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-extrabold bg-yellow-55 text-yellow-700 border border-yellow-250">
                                                Izin
                                            </span>
                                        @elseif ($absensi->status === 'alpa')
                                            <span class="inline-flex px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-extrabold bg-red-50 text-red-700 border border-red-200">
                                                Alpa
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-extrabold bg-gray-50 text-gray-500 border border-gray-200">
                                                {{ ucfirst($absensi->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-sm text-gray-450 italic border border-dashed border-gray-200 rounded-2xl">
                    Belum ada riwayat absensi yang tercatat untuk modul ini.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
