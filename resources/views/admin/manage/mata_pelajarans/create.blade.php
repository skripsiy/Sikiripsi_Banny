<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Mata Pelajaran</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan isi detail data mata pelajaran baru sekolah.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.manage.mata_pelajarans.store') }}" class="space-y-4">
    @csrf

    <div>
        <label for="create_kode_pelajaran" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kode Pelajaran</label>
        <input type="text" name="kode_pelajaran" id="create_kode_pelajaran" x-model="createKodePelajaran" readonly
               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-500 font-semibold transition-all shadow-sm cursor-not-allowed uppercase">
    </div>

    <div>
        <label for="create_nama_pelajaran" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Mata Pelajaran</label>
        <input type="text" name="nama_pelajaran" id="create_nama_pelajaran" value="{{ !old('_method') ? old('nama_pelajaran') : '' }}" required autofocus
               placeholder="Contoh: Pemrograman Web"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        @if(!old('_method'))
            @error('nama_pelajaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_jurusan_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jurusan (Spesifik/Umum)</label>
        <select name="jurusan_id" id="create_jurusan_id"
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="" {{ !old('jurusan_id') ? 'selected' : '' }}>Umum / Semua Jurusan</option>
            @foreach($jurusans as $jurusan)
                <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                    {{ $jurusan->nama_jurusan }} ({{ $jurusan->kode_jurusan }})
                </option>
            @endforeach
        </select>
        @if(!old('_method'))
            @error('jurusan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Mata Pelajaran
        </button>
    </div>
</form>
