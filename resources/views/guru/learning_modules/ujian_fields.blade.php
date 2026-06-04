<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Ujian' : 'Tambah Ujian'">Ujian</h3>
        <p class="text-xs text-gray-400 mt-0.5" x-text="isEdit ? 'Ubah informasi ujian di bawah.' : 'Lengkapi detail evaluasi ujian baru di bawah.'"></p>
    </div>
</div>

<form method="POST" :action="isEdit ? editUjianUrl : '{{ $actionUrl }}'" class="space-y-4" x-data="{ isEdit: {{ $isEdit ? 'true' : 'false' }} }">
    @csrf
    <template x-if="isEdit">
        @method('PUT')
    </template>

    <div>
        <label for="ujian_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Judul Ujian</label>
        <input type="text" name="title" id="ujian_title" required
               x-model="editUjianData.title"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
    </div>

    <div>
        <label for="ujian_instructions" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Instruksi Ujian</label>
        <textarea name="instructions" id="ujian_instructions" rows="4" required
                  x-model="editUjianData.instructions"
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm"></textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="ujian_duration_minutes" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Durasi Ujian (Menit)</label>
            <input type="number" name="duration_minutes" id="ujian_duration_minutes" min="1" required
                   x-model="editUjianData.duration_minutes"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        </div>

        <div>
            <label for="ujian_due_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Batas Ujian (Due Date)</label>
            <input type="datetime-local" name="due_date" id="ujian_due_date" required
                   x-model="editUjianData.due_date"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="isEdit ? (showEditUjianModal = false) : (showCreateUjianModal = false)"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Ujian
        </button>
    </div>
</form>
