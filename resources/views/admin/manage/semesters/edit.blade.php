<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Edit Semester</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan perbarui detail data semester sekolah.</p>
    </div>
</div>

<form method="POST" :action="editUrl" class="space-y-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" :value="editData.id">

    <div>
        <label for="edit_tahun_akademik_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Akademik <span class="text-red-500">*</span></label>
        <select name="tahun_akademik_id" id="edit_tahun_akademik_id" x-model="editData.tahun_akademik_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="" disabled>Pilih Tahun Akademik</option>
            @foreach($academicYears as $year)
                <option value="{{ $year->id }}">{{ $year->tahun_ajaran }}</option>
            @endforeach
        </select>
        @if(old('_method') === 'PUT')
            @error('tahun_akademik_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="edit_semester" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Semester <span class="text-red-500">*</span></label>
        <select name="semester" id="edit_semester" x-model="editData.semester" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="ganjil">Ganjil</option>
            <option value="genap">Genap</option>
        </select>
        @if(old('_method') === 'PUT')
            @error('semester') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="edit_start_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
            <input type="date" name="start_date" id="edit_start_date" x-model="editData.start_date" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
        <div>
            <label for="edit_end_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Selesai <span class="text-red-500">*</span></label>
            <input type="date" name="end_date" id="edit_end_date" x-model="editData.end_date" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div>
        <label for="edit_is_active" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status Aktif <span class="text-red-500">*</span></label>
        <select name="is_active" id="edit_is_active" x-model="editData.is_active" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
        @if(old('_method') === 'PUT')
            @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
