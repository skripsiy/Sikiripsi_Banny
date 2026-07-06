<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Ujian</h3>
        <p class="text-xs text-gray-400 mt-0.5">Lengkapi detail evaluasi ujian baru di bawah.</p>
    </div>
</div>

<form method="POST" action="{{ route('guru.learning-modules.ujians.store', $learningModule->id) }}" class="space-y-4">
    @csrf

    <div>
        <label for="create_ujian_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Judul Ujian</label>
        <input type="text" name="title" id="create_ujian_title" required value="{{ !old('_method') ? old('title') : '' }}"
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        @if(!old('_method'))
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_ujian_instructions" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Instruksi Ujian</label>
        <div class="prose max-w-none">
            <textarea name="instructions" id="create_ujian_instructions" required
                      x-init="ClassicEditor.create($el).then(editor => {
                          editor.model.document.on('change:data', () => {
                              $el.value = editor.getData();
                              $el.dispatchEvent(new Event('input'));
                          });
                      })"
                      class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">{{ !old('_method') ? old('instructions') : '' }}</textarea>
        </div>
        @if(!old('_method'))
            @error('instructions') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_ujian_duration_minutes" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Durasi Ujian (Menit)</label>
            <input type="number" name="duration_minutes" id="create_ujian_duration_minutes" min="1" required value="{{ !old('_method') ? old('duration_minutes') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('duration_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_ujian_due_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Batas Ujian (Due Date)</label>
            <input type="datetime-local" name="due_date" id="create_ujian_due_date" required value="{{ !old('_method') ? old('due_date') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Ujian
        </button>
    </div>
</form>
