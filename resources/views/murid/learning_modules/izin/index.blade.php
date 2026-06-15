<x-app-layout>
    <x-slot name="header">
        {{ __('Izin Digital: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{ showCreateModal: {{ $errors->any() || session('error') || session('open_create_modal') ? 'true' : 'false' }} }">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('murid.learning-modules.absensi.index', $learningModule->id) }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Absensi Saya
            </a>
        </div>

        <!-- Module Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $learningModule->mataPelajaran->nama_pelajaran }}
                    </span>
                    <span class="bg-rose-500/20 text-rose-200 border border-rose-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Izin Digital
                    </span>
                </div>
                <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">Digitalisasi permohonan sakit atau izin ketidakhadiran siswa dalam pembelajaran secara transparan.</p>
            </div>
            <div>
                <button type="button" @click="showCreateModal = true"
                   class="bg-white hover:bg-gray-100 text-[#0c2b4d] font-bold px-4 py-2.5 rounded-xl text-xs transition-all shadow-md select-none cursor-pointer">
                    + Ajukan Permohonan Izin
                </button>
            </div>
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

        <!-- Main Content Area -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-6">Riwayat Permohonan Izin</h3>

            @if($izinRequests->isNotEmpty())
                <div class="overflow-x-auto border border-gray-150 rounded-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-150 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Tanggal Ketidakhadiran</th>
                                <th class="px-6 py-4">Jenis Izin</th>
                                <th class="px-6 py-4">Alasan</th>
                                <th class="px-6 py-4">Dokumen Bukti</th>
                                <th class="px-6 py-4">Status Persetujuan</th>
                                <th class="px-6 py-4">Feedback Guru</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700 font-semibold">
                            @foreach($izinRequests as $izin)
                                <tr class="hover:bg-gray-50/50 transition-colors">
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
                                           class="inline-flex items-center gap-1 text-[#0c2b4d] hover:underline font-bold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Lihat Bukti
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
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($izin->catatan_guru)
                                            <span class="text-gray-650 italic">"{{ $izin->catatan_guru }}"</span>
                                        @else
                                            <span class="text-gray-400 italic">Belum ada catatan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-sm text-gray-450 italic border border-dashed border-gray-200 rounded-2xl select-none">
                    Belum ada riwayat permohonan izin.
                </div>
            @endif
        </div>

        <!-- Modal Overlay -->
        <div x-show="showCreateModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            
            <!-- Modal Card -->
            <div @click.away="showCreateModal = false"
                 class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                
                <div class="p-6">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="text-base font-extrabold text-gray-800">Form Pengajuan Izin Digital</h3>
                        <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-650 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mb-6">Lengkapi formulir di bawah ini dengan menyertakan bukti resmi untuk ketidakhadiran Anda.</p>

                    <!-- Session Errors inside Modal -->
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-250 text-red-800 rounded-2xl text-xs font-bold">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-50 border border-red-250 text-red-800 text-xs font-bold rounded-2xl flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('murid.learning-modules.izin.store', $learningModule->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Absensi</label>
                            <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis Izin</label>
                            <select name="jenis_izin" required
                                    class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs font-semibold bg-white cursor-pointer">
                                <option value="sakit" {{ old('jenis_izin') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="izin" {{ old('jenis_izin') === 'izin' ? 'selected' : '' }}>Izin</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Alasan Ketidakhadiran</label>
                            <textarea name="alasan" rows="3" required placeholder="Tuliskan alasan lengkap ketidakhadiran Anda..."
                                      class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs font-semibold">{{ old('alasan') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Upload Bukti Dokumen (Maks. 5MB, PDF/JPG/PNG)</label>
                            <p class="text-[10px] text-gray-400 mb-1.5">* Contoh: Surat dokter, surat izin orang tua bermaterai/bertanda tangan, atau dokumen pendukung lainnya.</p>
                            <input type="file" name="file" required
                                   class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#0c2b4d] hover:file:bg-blue-100 transition-all cursor-pointer border border-gray-200 rounded-xl p-1 bg-white">
                        </div>

                        <div class="flex justify-end gap-2.5 pt-4 border-t border-gray-100">
                            <button type="button" @click="showCreateModal = false"
                                    class="px-4 py-2 border border-gray-250 text-gray-550 rounded-xl text-xs font-bold hover:bg-gray-50 transition-colors select-none cursor-pointer text-center">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-xs font-bold transition-all shadow-sm select-none cursor-pointer">
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
