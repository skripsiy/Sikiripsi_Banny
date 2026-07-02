<x-app-layout>
    <x-slot name="header">
        {{ __('Daftar Izin Digital: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showRejectModal: false,
        rejectData: {
            student_name: '',
            catatan_guru: ''
        },
        rejectUrl: ''
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
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6">
            <div class="flex flex-wrap gap-2">
                <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    {{ $learningModule->mataPelajaran->nama_pelajaran }}
                </span>
                <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Kelola Perizinan Digital
                </span>
            </div>
            <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
            <p class="text-xs text-blue-100/70 mt-1 max-w-xl">Meninjau permohonan izin atau sakit yang diajukan oleh murid. Izin yang disetujui akan memperbarui absensi secara otomatis.</p>
        </div>

        <!-- Session Status & Errors -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-250 text-emerald-800 text-xs font-bold rounded-2xl flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-250 text-red-800 text-xs font-bold rounded-2xl flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Content Area -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-3 sm:p-6">
            <h3 class="text-base font-bold text-gray-800 mb-6">Daftar Pengajuan Izin Murid</h3>

            @if($izinRequests->isNotEmpty())
                <div class="overflow-x-auto border border-gray-150 rounded-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Murid / Kelas</th>
                                <th class="px-6 py-4">Tanggal Absen</th>
                                <th class="px-6 py-4">Jenis Izin</th>
                                <th class="px-6 py-4">Alasan</th>
                                <th class="px-6 py-4">Bukti Lampiran</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-750 font-medium">
                            @foreach($izinRequests as $izin)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-gray-800">{{ $izin->murid->user->name ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-450 mt-0.5">{{ $izin->murid->classroom->nama_kelas ?? '-' }} (NISN: {{ $izin->murid->nisn }})</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800">
                                        <div>{{ $izin->date->translatedFormat('l') }}</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">{{ $izin->date->translatedFormat('d F Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($izin->jenis_izin === 'sakit')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] uppercase tracking-wider font-extrabold bg-orange-50 text-orange-700 border border-orange-200">
                                                Sakit
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] uppercase tracking-wider font-extrabold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                Izin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate" title="{{ $izin->alasan }}">
                                        {{ $izin->alasan }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ asset('storage/' . $izin->bukti_file_path) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-[#0c2b4d] hover:underline font-bold bg-gray-50 border border-gray-200 px-2.5 py-1.5 rounded-xl transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Unduh Bukti
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($izin->status === 'approved')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] uppercase tracking-wider font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Disetujui
                                            </span>
                                        @elseif($izin->status === 'rejected')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] uppercase tracking-wider font-extrabold bg-red-50 text-red-700 border border-red-200">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] uppercase tracking-wider font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($izin->status === 'pending')
                                            <div class="flex items-center justify-end gap-1.5">
                                                <form action="{{ route('guru.learning-modules.izin.approve', [$learningModule->id, $izin->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-xl font-bold text-[10px] transition-all cursor-pointer shadow-sm select-none">
                                                        Setujui
                                                    </button>
                                                </form>

                                                <button @click="
                                                    showRejectModal = true;
                                                    rejectData = {
                                                        student_name: '{{ addslashes($izin->murid->user->name ?? '') }}',
                                                        catatan_guru: ''
                                                    };
                                                    rejectUrl = '{{ route('guru.learning-modules.izin.reject', [$learningModule->id, $izin->id]) }}';
                                                " class="bg-red-650 hover:bg-red-700 text-white px-3 py-1.5 rounded-xl font-bold text-[10px] transition-all cursor-pointer shadow-sm select-none">
                                                    Tolak
                                                </button>
                                            </div>
                                        @else
                                            @if($izin->status === 'rejected' && $izin->catatan_guru)
                                                <span class="text-gray-400 text-[10px] block truncate max-w-[120px]" title="{{ $izin->catatan_guru }}">"{{ $izin->catatan_guru }}"</span>
                                            @else
                                                <span class="text-gray-400 italic">Sudah diproses</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-sm text-gray-450 italic border border-dashed border-gray-200 rounded-2xl select-none">
                    Belum ada pengajuan izin dari murid.
                </div>
            @endif
        </div>

        <!-- Reject Modal -->
        <div x-show="showRejectModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showRejectModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showRejectModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showRejectModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-lg mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-red-600 to-red-800"></div>
                <div class="p-6">
                    <h3 class="text-base font-extrabold text-gray-800 mb-1">Tolak Pengajuan Izin</h3>
                    <p class="text-xs text-gray-500 mb-6">Tolak permohonan izin dari murid: <strong class="text-gray-805" x-text="rejectData.student_name"></strong></p>

                    <form :action="rejectUrl" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Alasan Penolakan / Keterangan</label>
                            <textarea name="catatan_guru" rows="3" required placeholder="Tulis alasan mengapa pengajuan izin ditolak..." x-model="rejectData.catatan_guru"
                                      class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-xs font-semibold"></textarea>
                        </div>

                        <div class="flex justify-end gap-2.5 pt-4 border-t border-gray-100">
                            <button type="button" @click="showRejectModal = false"
                                    class="px-4 py-2 border border-gray-250 text-gray-550 rounded-xl text-xs font-bold hover:bg-gray-50 transition-colors select-none cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-750 text-white rounded-xl text-xs font-bold transition-all shadow-sm select-none cursor-pointer">
                                Tolak Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
