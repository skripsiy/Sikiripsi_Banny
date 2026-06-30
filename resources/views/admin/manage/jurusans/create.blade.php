<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Jurusan</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan isi detail data jurusan baru sekolah.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.manage.jurusans.store') }}" class="space-y-4">
    @csrf

    <div>
        <label for="create_kode_jurusan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kode Jurusan <span class="text-red-500">*</span></label>
        <input type="text" name="kode_jurusan" id="create_kode_jurusan" value="{{ !old('_method') ? old('kode_jurusan') : '' }}" required autofocus
               placeholder="Contoh: RPL"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm uppercase">
        <p class="text-[10px] text-gray-400 mt-1">Maksimal 20 karakter, akan otomatis menjadi huruf kapital.</p>
        @if(!old('_method'))
            @error('kode_jurusan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_nama_jurusan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Jurusan <span class="text-red-500">*</span></label>
        <input type="text" name="nama_jurusan" id="create_nama_jurusan" value="{{ !old('_method') ? old('nama_jurusan') : '' }}" required
               placeholder="Contoh: Rekayasa Perangkat Lunak"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        @if(!old('_method'))
            @error('nama_jurusan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_deskripsi" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Deskripsi <span class="text-gray-400 normal-case font-normal">(Opsional)</span></label>
        <textarea name="deskripsi" id="create_deskripsi" rows="3"
                  placeholder="Deskripsi singkat mengenai jurusan ini..."
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm resize-none">{{ !old('_method') ? old('deskripsi') : '' }}</textarea>
        <p class="text-[10px] text-gray-400 mt-1">Maksimal 1000 karakter.</p>
        @if(!old('_method'))
            @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Jurusan
        </button>
    </div>
</form>
