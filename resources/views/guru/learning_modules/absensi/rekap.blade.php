<x-app-layout>
    <x-slot name="header">
        {{ __('Rekap Absensi: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        searchQuery: ''
    }">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('guru.learning-modules.absensi.index', $learningModule->id) }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Kelola Absensi
            </a>
        </div>

        <!-- Module Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $learningModule->subject->nama_pelajaran }}
                    </span>
                    <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Rekap Kehadiran
                    </span>
                </div>
                <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">Laporan statistik kehadiran seluruh siswa yang mengikuti modul pembelajaran ini.</p>
            </div>
            <div>
                <a href="{{ route('guru.learning-modules.export-absensi', $learningModule->id) }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition-all shadow-md select-none cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Ekspor ke Excel
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Laporan Akumulasi Kehadiran</h3>
                    <p class="text-xs text-gray-400 mt-0.5 font-medium">Persentase kehadiran dihitung berdasarkan total pertemuan yang telah terlaksana.</p>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama murid..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-250 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs text-gray-800 transition-all font-semibold">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            @if($rekap->isNotEmpty())
                <div class="overflow-x-auto border border-gray-150 rounded-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4 w-16">No</th>
                                <th class="px-6 py-4">Nama Siswa</th>
                                <th class="px-6 py-4">NISN / Kelas</th>
                                <th class="px-6 py-4 text-center">Hadir</th>
                                <th class="px-6 py-4 text-center">Sakit</th>
                                <th class="px-6 py-4 text-center">Izin</th>
                                <th class="px-6 py-4 text-center">Alpa</th>
                                <th class="px-6 py-4 text-center">Pertemuan</th>
                                <th class="px-6 py-4 text-right">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700 font-semibold">
                            @foreach($rekap as $index => $row)
                                <tr x-show="searchQuery === '' || '{{ strtolower(addslashes($row->murid->user->name ?? '')) }}'.includes(searchQuery.toLowerCase())"
                                    class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-gray-400 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-gray-800">{{ $row->murid->user->name ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-450 mt-0.5">{{ $row->murid->user->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-700">NISN: {{ $row->murid->nisn }}</div>
                                        <div class="text-[10px] text-gray-450 mt-0.5">{{ $row->murid->classroom->nama_kelas ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-emerald-600 bg-emerald-50/10">{{ $row->hadir }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-orange-600">{{ $row->sakit }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-yellow-600">{{ $row->izin }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-red-600 bg-red-50/10">{{ $row->alpa }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-850">{{ $row->pertemuan }}</td>
                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $perc = $row->percentage;
                                            $colorClass = 'text-gray-850';
                                            if ($perc >= 85) {
                                                $colorClass = 'text-emerald-600';
                                            } elseif ($perc >= 75) {
                                                $colorClass = 'text-yellow-600';
                                            } else {
                                                $colorClass = 'text-red-600';
                                            }
                                        @endphp
                                        <span class="inline-flex px-2.5 py-1 rounded-lg font-extrabold text-xs {{ $colorClass }}">
                                            {{ $perc }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-sm text-gray-450 italic border border-dashed border-gray-200 rounded-2xl select-none">
                    Belum ada data murid untuk modul ini.
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
