<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Edit Modul Pembelajaran</h3>
        <p class="text-xs text-gray-400 mt-0.5">Perbarui detail modul pembelajaran yang Anda pilih.</p>
    </div>
</div>

<form method="POST" :action="editUrl" class="space-y-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" :value="editData.id">

    <div>
        <label for="edit_mata_pelajaran_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
        <select name="mata_pelajaran_id" id="edit_mata_pelajaran_id" x-model="editData.mata_pelajaran_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach ($mata_pelajarans as $subject)
                <option value="{{ $subject->id }}">
                    {{ $subject->nama_pelajaran }} ({{ $subject->kode_pelajaran }})
                </option>
            @endforeach
        </select>
        @if(old('_method') === 'PUT')
            @error('mata_pelajaran_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="edit_tahun_akademik_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
        <select name="tahun_akademik_id" id="edit_tahun_akademik_id" x-model="editData.tahun_akademik_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="">-- Pilih Tahun Ajaran --</option>
            @foreach ($semesters as $ta)
                <option value="{{ $ta->id }}">
                    {{ $ta->tahun_ajaran }}
                </option>
            @endforeach
        </select>
        @if(old('_method') === 'PUT')
            @error('tahun_akademik_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="edit_classroom_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kelas</label>
        <select name="classroom_id" id="edit_classroom_id" x-model="editData.classroom_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="">-- Pilih Kelas --</option>
            @foreach ($classrooms as $cls)
                <option value="{{ $cls->id }}">
                    {{ $cls->nama_kelas }}
                </option>
            @endforeach
        </select>
        @if(old('_method') === 'PUT')
            @error('classroom_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>



    <div>
        <label for="edit_description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Deskripsi Modul</label>
        <textarea name="description" id="edit_description" rows="4" x-model="editData.description" required
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm"></textarea>
        @if(old('_method') === 'PUT')
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
