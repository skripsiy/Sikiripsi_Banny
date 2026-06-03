<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Edit Detail Kelas</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan perbarui detail data kelas sekolah.</p>
    </div>
</div>

<form method="POST" :action="editUrl" class="space-y-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" :value="editData.id">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="edit_nama_kelas" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Kelas</label>
            <input type="text" name="nama_kelas" id="edit_nama_kelas" x-model="editData.nama_kelas" required
                   placeholder="Contoh: XII RPL 1"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm uppercase">
            @if(old('_method') === 'PUT')
                @error('nama_kelas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="edit_is_active" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status Aktif</label>
            <select name="is_active" id="edit_is_active" x-model="editData.is_active" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
            @if(old('_method') === 'PUT')
                @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div>
        <label for="edit_jurusan_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jurusan</label>
        <select name="jurusan_id" id="edit_jurusan_id" x-model="editData.jurusan_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="" disabled>Pilih Jurusan</option>
            @foreach($jurusans as $jurusan)
                <option value="{{ $jurusan->id }}">
                    {{ $jurusan->nama_jurusan }} ({{ $jurusan->kode_jurusan }})
                </option>
            @endforeach
        </select>
        @if(old('_method') === 'PUT')
            @error('jurusan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="edit_tahun_ajaran_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
        <select name="tahun_ajaran_id" id="edit_tahun_ajaran_id" x-model="editData.tahun_ajaran_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="" disabled>Pilih Tahun Ajaran</option>
            @foreach($tahunAjarans as $ta)
                <option value="{{ $ta->id }}">
                    {{ $ta->tahun_ajaran }} - Semester {{ ucfirst($ta->semester) }}
                </option>
            @endforeach
        </select>
        @if(old('_method') === 'PUT')
            @error('tahun_ajaran_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showEditModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Perubahan
        </button>
    </div>
</form>
