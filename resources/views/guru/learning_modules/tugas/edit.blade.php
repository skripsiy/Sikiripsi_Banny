<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Edit Tugas</h3>
        <p class="text-xs text-gray-400 mt-0.5">Ubah informasi tugas di bawah.</p>
    </div>
</div>

<form method="POST" :action="editUrl" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" :value="editData.id">

    <div>
        <label for="edit_tugas_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Judul Tugas</label>
        <input type="text" name="title" id="edit_tugas_title" required x-model="editData.title"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        @if(old('_method') === 'PUT')
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="edit_tugas_instructions" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Instruksi Tugas</label>
        <textarea name="instructions" id="edit_tugas_instructions" rows="5" required x-model="editData.instructions"
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm"></textarea>
        @if(old('_method') === 'PUT')
            @error('instructions') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="edit_tugas_due_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Batas Pengumpulan (Due Date)</label>
        <input type="datetime-local" name="due_date" id="edit_tugas_due_date" required x-model="editData.due_date"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        @if(old('_method') === 'PUT')
            @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="edit_tugas_file" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Berkas Petunjuk (Maks 10MB)</label>
        <input type="file" name="file" id="edit_tugas_file"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        <p class="text-[10px] text-gray-400 mt-1">Biarkan kosong jika tidak ingin mengubah berkas petunjuk.</p>
        @if(old('_method') === 'PUT')
            @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
