<x-app-layout>
    <x-slot name="header">
        {{ __('Ajukan Izin: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-xl mx-auto font-sans">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('murid.learning-modules.izin.index', $learningModule->id) }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Riwayat Izin
            </a>
        </div>

        <!-- Session Status & Errors -->
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-250 text-red-800 text-xs font-bold rounded-2xl flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-250 text-red-800 rounded-2xl text-xs font-bold">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
            <div class="p-6">
                <h3 class="text-base font-extrabold text-gray-800 mb-1">Form Pengajuan Izin Digital</h3>
                <p class="text-xs text-gray-400 mb-6">Lengkapi formulir di bawah ini dengan menyertakan bukti resmi untuk ketidakhadiran Anda.</p>

                <form action="{{ route('murid.learning-modules.izin.store', $learningModule->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Absensi</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs font-semibold">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis Izin</label>
                        <select name="jenis_izin" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs font-semibold bg-white cursor-pointer">
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Alasan Ketidakhadiran</label>
                        <textarea name="alasan" rows="3" required placeholder="Tuliskan alasan lengkap ketidakhadiran Anda..."
                                  class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-xs font-semibold"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Upload Bukti Dokumen (Maks. 5MB, PDF/JPG/PNG)</label>
                        <p class="text-[10px] text-gray-400 mb-1.5">* Contoh: Surat dokter, surat izin orang tua bermaterai/bertanda tangan, atau dokumen pendukung lainnya.</p>
                        <input type="file" name="file" required
                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#0c2b4d] hover:file:bg-blue-100 transition-all cursor-pointer border border-gray-200 rounded-xl p-1 bg-white">
                    </div>

                    <div class="flex justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <a href="{{ route('murid.learning-modules.izin.index', $learningModule->id) }}"
                           class="px-4 py-2 border border-gray-250 text-gray-550 rounded-xl text-xs font-bold hover:bg-gray-50 transition-colors select-none cursor-pointer text-center">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-5 py-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-xs font-bold transition-all shadow-sm select-none cursor-pointer">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
