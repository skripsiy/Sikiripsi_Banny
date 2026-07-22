<x-app-layout>
    <x-slot name="header">
        {{ __('Rekapitulasi Absensi Kelas') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans">
        <!-- Back Button & Title -->
        <div class="mb-6">
            <a href="{{ route('admin.manage.absensi.index') }}" 
               class="text-xs text-gray-500 hover:text-gray-700 font-bold flex items-center gap-1.5 transition-all mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Kelas
            </a>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Kelas: {{ $classroom->nama_kelas }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tahun Ajaran: {{ $classroom->tahunAkademik->tahun_ajaran }} | Jurusan: {{ $classroom->jurusan->nama_jurusan }}</p>
                </div>
            </div>
        </div>

        <!-- Filter Panel -->
        <form method="GET" action="{{ route('admin.manage.absensi.show', $classroom->id) }}" class="bg-white rounded-2xl border border-gray-100 p-5 mb-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Filter Semester -->
                <div>
                    <label for="semester_id" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Semester</label>
                    <select name="semester_id" id="semester_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all cursor-pointer">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $selectedSemester?->id == $sem->id ? 'selected' : '' }}>
                                Semester {{ ucfirst($sem->semester) }} ({{ $sem->tahunAkademik->tahun_ajaran }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Mata Pelajaran -->
                <div>
                    <label for="mata_pelajaran_id" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" id="mata_pelajaran_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all cursor-pointer">
                        <option value="">Semua Mata Pelajaran (Gabungan)</option>
                        @foreach ($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ $selectedSubjectId == $subj->id ? 'selected' : '' }}>
                                {{ $subj->nama_pelajaran }} ({{ $subj->kode_pelajaran }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <!-- Attendance Recap Card -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <!-- Decorative Top Gradient Line -->
            <div class="h-1.5 bg-gradient-to-r from-teal-500 to-[#0c2b4d]"></div>

            <div class="p-3 sm:p-6">
                <div class="overflow-x-auto">
                    @if ($rekap->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/75">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NISN</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-green-600 uppercase tracking-wider">Hadir</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-yellow-600 uppercase tracking-wider">Sakit</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-blue-600 uppercase tracking-wider">Izin</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-red-600 uppercase tracking-wider">Alpa</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Pertemuan</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-40">Persentase Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 font-sans">
                                @foreach ($rekap as $index => $item)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ $item->murid->namaLengkap ?? $item->murid->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->murid->nisn ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-700">
                                            <span class="px-2 py-1 bg-green-50 rounded-lg">{{ $item->hadir }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-yellow-700">
                                            <span class="px-2 py-1 bg-yellow-50 rounded-lg">{{ $item->sakit }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-blue-700">
                                            <span class="px-2 py-1 bg-blue-50 rounded-lg">{{ $item->izin }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-red-700">
                                            <span class="px-2 py-1 bg-red-50 rounded-lg">{{ $item->alpa }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-500">
                                            <span>{{ $item->pertemuan }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($item->percentage >= 80)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-green-50 text-green-700 border border-green-200">
                                                    {{ $item->percentage }}%
                                                </span>
                                            @elseif($item->percentage >= 50)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                    {{ $item->percentage }}%
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-red-50 text-red-700 border border-red-200 animate-pulse">
                                                    {{ $item->percentage }}%
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-20 h-20 bg-blue-50 text-gray-300 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Siswa</h4>
                            <p class="text-sm text-gray-400 max-w-sm">Data siswa di kelas ini belum tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
