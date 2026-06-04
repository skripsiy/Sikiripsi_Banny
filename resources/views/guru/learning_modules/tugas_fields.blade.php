<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Tugas' : 'Tambah Tugas'">Tugas</h3>
        <p class="text-xs text-gray-400 mt-0.5" x-text="isEdit ? 'Ubah informasi tugas di bawah.' : 'Lengkapi detail instruksi tugas baru di bawah.'"></p>
    </div>
</div>

<form method="POST" :action="isEdit ? editTugasUrl : '{{ $actionUrl }}'" enctype="multipart/form-data" class="space-y-4" x-data="{ isEdit: {{ $isEdit ? 'true' : 'false' }} }">
    @csrf
    <template x-if="isEdit">
        @method('PUT')
    </template>

    <div>
        <label for="tugas_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Judul Tugas</label>
        <input type="text" name="title" id="tugas_title" required
               x-model="editTugasData.title"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
    </div>

    <div>
        <label for="tugas_instructions" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Instruksi Tugas</label>
        <textarea name="instructions" id="tugas_instructions" rows="5" required
                  x-model="editTugasData.instructions"
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm"></textarea>
    </div>

    <div>
        <label for="tugas_due_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Batas Pengumpulan (Due Date)</label>
        <input type="datetime-local" name="due_date" id="tugas_due_date" required
               x-model="editTugasData.due_date"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
    </div>

    <div>
        <label for="tugas_file" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Berkas Petunjuk (Maks 10MB)</label>
        <input type="file" name="file" id="tugas_file"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        <template x-if="isEdit">
            <p class="text-[10px] text-gray-400 mt-1">Biarkan kosong jika tidak ingin mengubah berkas petunjuk.</p>
        </template>
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="isEdit ? (showEditTugasModal = false) : (showCreateTugasModal = false)"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Tugas
        </button>
    </div>
</form>
