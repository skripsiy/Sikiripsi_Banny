<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Materi' : 'Tambah Materi'">Materi</h3>
        <p class="text-xs text-gray-400 mt-0.5" x-text="isEdit ? 'Ubah informasi materi pembelajaran di bawah.' : 'Lengkapi detail materi pembelajaran baru di bawah.'"></p>
    </div>
</div>

<form method="POST" :action="isEdit ? editMateriUrl : '{{ $actionUrl }}'" enctype="multipart/form-data" class="space-y-4" x-data="{ isEdit: {{ $isEdit ? 'true' : 'false' }} }">
    @csrf
    <template x-if="isEdit">
        @method('PUT')
    </template>

    <div>
        <label for="materi_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Judul Materi</label>
        <input type="text" name="title" id="materi_title" required
               :value="isEdit ? editMateriData.title : ''"
               x-model="editMateriData.title"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
    </div>

    <div>
        <label for="materi_content" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Isi / Konten Materi</label>
        <textarea name="content" id="materi_content" rows="6" required
                  x-model="editMateriData.content"
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm"></textarea>
    </div>

    <div>
        <label for="materi_file" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Berkas Lampiran (Maks 10MB)</label>
        <input type="file" name="file" id="materi_file"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        <template x-if="isEdit">
            <p class="text-[10px] text-gray-400 mt-1">Biarkan kosong jika tidak ingin mengubah berkas yang ada.</p>
        </template>
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="isEdit ? (showEditMateriModal = false) : (showCreateMateriModal = false)"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Materi
        </button>
    </div>
</form>
