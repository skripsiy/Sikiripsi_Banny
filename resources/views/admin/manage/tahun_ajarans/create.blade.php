<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Tahun Ajaran</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan isi detail data tahun ajaran baru sekolah.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.manage.tahun-ajarans.store') }}" class="space-y-4">
    @csrf

    <div>
        <label for="create_tahun_ajaran" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" id="create_tahun_ajaran" value="{{ !old('_method') ? old('tahun_ajaran') : '' }}" required autofocus
               placeholder="Contoh: 2025/2026"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        <p class="text-[10px] text-gray-400 mt-1">Harus tepat 9 karakter dengan format YYYY/YYYY.</p>
        @if(!old('_method'))
            @error('tahun_ajaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_semester" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Semester</label>
        <select name="semester" id="create_semester" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="ganjil" {{ (!old('_method') && old('semester') == 'ganjil') ? 'selected' : '' }}>Ganjil</option>
            <option value="genap" {{ (!old('_method') && old('semester') == 'genap') ? 'selected' : '' }}>Genap</option>
        </select>
        @if(!old('_method'))
            @error('semester') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_is_active" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status Aktif</label>
        <select name="is_active" id="create_is_active" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="1" {{ (!old('_method') && old('is_active') == '1') ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ (!old('_method') && old('is_active') == '0') ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
        <p class="text-[10px] text-gray-400 mt-1">Jika diset Aktif, tahun ajaran aktif lainnya akan otomatis dinonaktifkan.</p>
        @if(!old('_method'))
            @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Tahun Ajaran
        </button>
    </div>
</form>
