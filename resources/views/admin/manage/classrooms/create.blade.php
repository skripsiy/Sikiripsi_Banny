<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Kelas</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan isi detail data kelas baru sekolah.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.manage.classrooms.store') }}" class="space-y-4">
    @csrf

    <div>
        <label for="create_nama_kelas" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Kelas <span class="text-red-500">*</span></label>
        <input type="text" name="nama_kelas" id="create_nama_kelas" value="{{ !old('_method') ? old('nama_kelas') : '' }}" required autofocus
               placeholder="Contoh: XII RPL 1"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm uppercase">
        @if(!old('_method'))
            @error('nama_kelas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_jurusan_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jurusan <span class="text-red-500">*</span></label>
        <select name="jurusan_id" id="create_jurusan_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="" disabled {{ !old('jurusan_id') ? 'selected' : '' }}>Pilih Jurusan</option>
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

    <div>
        <label for="create_tahun_akademik_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Ajaran <span class="text-red-500">*</span></label>
        <select name="tahun_akademik_id" id="create_tahun_akademik_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="" disabled {{ !old('tahun_akademik_id') ? 'selected' : '' }}>Pilih Tahun Ajaran</option>
            @foreach($academicYears as $ta)
                <option value="{{ $ta->id }}" {{ old('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun_ajaran }}
                </option>
            @endforeach
        </select>
        @if(!old('_method'))
            @error('tahun_akademik_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Kelas
        </button>
    </div>
</form>
