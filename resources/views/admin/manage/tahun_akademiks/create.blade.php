<form action="{{ route('admin.manage.tahun_akademiks.store') }}" method="POST">
    @csrf
    <h3 class="text-lg font-bold text-gray-900 mb-6">Tambah Tahun Akademik</h3>
    
    <div class="mb-5">
        <label for="tahun_ajaran" class="block text-sm font-bold text-gray-700 mb-2">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" id="tahun_ajaran" placeholder="Contoh: 2025/2026" value="{{ old('tahun_ajaran') }}" required
               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#0c2b4d] focus:border-transparent text-sm font-medium text-gray-800 placeholder-gray-400 transition-all">
        @error('tahun_ajaran')
            <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-bold text-blue-800">Otomatis Dibuat</p>
                <p class="text-xs text-blue-600 mt-1 leading-relaxed">
                    Tahun Akademik baru akan langsung berstatus <strong>Aktif</strong>. 
                    Semester <strong>Ganjil</strong> dan <strong>Genap</strong> juga akan otomatis dibuat dengan tanggal default yang dapat diedit nanti.
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-8">
        <button type="button" @click="showCreateModal = false"
                class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-bold transition-all border border-gray-200 cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="px-5 py-2.5 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-sm font-bold transition-all shadow-sm cursor-pointer select-none">
            Simpan
        </button>
    </div>
</form>
