<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Absensi: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans">

        <!-- Back Button (outside header) -->
        <div class="mb-4">
            <a href="{{ route('guru.learning-modules.show', $learningModule->id) }}" 
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
                        Absensi Murid
                    </span>
                </div>
                <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">{{ $learningModule->description }}</p>
            </div>
        </div>

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- Pending Leave Requests Notification -->
        @if($totalPendingIzinCount > 0)
            <div class="mb-6 p-4 bg-amber-50 border border-amber-250 text-amber-850 rounded-2xl text-xs font-semibold shadow-sm flex items-center gap-2">
                <span class="text-base flex-shrink-0">⚠️</span>
                <span>
                    @if($pendingIzinForDate->isNotEmpty())
                        <strong>Pemberitahuan:</strong> Terdapat murid yang mengajukan perizinan tidak masuk sekolah pada <strong>hari ini</strong> ({{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}). Silakan tinjau permohonan izin sebelum melakukan absensi.
                    @else
                        <strong>Pemberitahuan:</strong> Terdapat permohonan izin dari murid di modul ini yang belum ditinjau.
                    @endif
                </span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Content Area -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-bold text-gray-800">Absensi Kehadiran Siswa</h3>
                    <a href="{{ route('guru.learning-modules.izin.index', $learningModule->id) }}"
                       class="inline-flex items-center gap-1.5 bg-[#0c2b4d] hover:bg-[#07192d] text-white px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all shadow-sm cursor-pointer select-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Review Izin Digital
                        @if($totalPendingIzinCount > 0)
                            <span class="bg-amber-500 text-white text-[9px] px-1.5 py-0.5 rounded-full font-extrabold animate-pulse">
                                {{ $totalPendingIzinCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('guru.learning-modules.rekap-absensi', $learningModule->id) }}"
                       class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all shadow-sm cursor-pointer select-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Rekap Kehadiran
                    </a>
                </div>

                <!-- Date selector -->
                <form method="GET" action="{{ route('guru.learning-modules.absensi.index', $learningModule->id) }}" class="flex items-center gap-2">
                    <label for="absensi_date" class="text-xs font-bold text-gray-500 uppercase tracking-wider select-none">Tanggal:</label>
                    <input type="date" name="date" id="absensi_date" value="{{ $date }}" onchange="this.form.submit()"
                           class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:border-[#0c2b4d]">
                </form>
            </div>

            @if ($murids->isNotEmpty())
                <form method="POST" action="{{ route('guru.learning-modules.absensi.store', $learningModule->id) }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div class="overflow-x-auto border border-gray-100 rounded-2xl">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/75">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase w-12">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kelas</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-72">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                @foreach ($murids as $index => $murid)
                                    @php
                                        $currentStatus = $absensis->get($murid->id)->status ?? 'hadir';
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 {{ $pendingIzinForDate->has($murid->id) ? 'bg-amber-50/40' : '' }}">
                                        <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 font-semibold text-gray-900">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span>{{ $murid->user->name ?? 'N/A' }}</span>
                                                @if($pendingIzinForDate->has($murid->id))
                                                    @php
                                                        $izinReq = $pendingIzinForDate->get($murid->id);
                                                    @endphp
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-extrabold border bg-amber-50 text-amber-700 border-amber-200 animate-pulse">
                                                        ⚠️ Mengajukan Izin ({{ ucfirst($izinReq->jenis_izin) }})
                                                    </span>
                                                @endif
                                            </div>
                                            @if($murid->no_telepon_orang_tua)
                                                <span class="block text-[10px] text-gray-400 font-normal">WA Ortu: {{ $murid->no_telepon_orang_tua }}</span>
                                            @else
                                                <span class="block text-[10px] text-red-400 font-normal italic">WA Ortu belum diatur</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-500">{{ $murid->classroom->nama_kelas ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-4 text-xs font-bold">
                                                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                    <input type="radio" name="status[{{ $murid->id }}]" value="hadir" {{ $currentStatus === 'hadir' ? 'checked' : '' }}
                                                           class="w-3.5 h-3.5 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                    <span class="text-gray-700">Hadir</span>
                                                </label>
                                                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                    <input type="radio" name="status[{{ $murid->id }}]" value="sakit" {{ $currentStatus === 'sakit' ? 'checked' : '' }}
                                                           class="w-3.5 h-3.5 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                    <span class="text-orange-600">Sakit</span>
                                                </label>
                                                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                    <input type="radio" name="status[{{ $murid->id }}]" value="izin" {{ $currentStatus === 'izin' ? 'checked' : '' }}
                                                           class="w-3.5 h-3.5 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                                    <span class="text-yellow-600">Izin</span>
                                                </label>
                                                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                    <input type="radio" name="status[{{ $murid->id }}]" value="alpa" {{ $currentStatus === 'alpa' ? 'checked' : '' }}
                                                           class="w-3.5 h-3.5 text-red-600 border-gray-300 focus:ring-red-500">
                                                    <span class="text-red-600">Alpa</span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4 p-4 bg-blue-50/50 border border-blue-100 rounded-2xl">
                        <span class="text-[11px] text-blue-700 leading-normal max-w-lg">
                            <span class="font-bold">Info:</span> Menyimpan absensi dengan status "Alpa" akan mengirim notifikasi otomatis via WhatsApp ke nomor telepon orang tua murid yang terdaftar.
                        </span>
                        <button type="submit"
                                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-6 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer whitespace-nowrap">
                            Simpan Absensi
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-10 text-gray-400 text-sm">Tidak ada murid terdaftar di jurusan ini.</div>
            @endif
        </div>

    </div>
</x-app-layout>
