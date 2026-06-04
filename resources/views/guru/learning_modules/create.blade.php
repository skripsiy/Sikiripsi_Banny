<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Modul Pembelajaran</h3>
        <p class="text-xs text-gray-400 mt-0.5">Lengkapi form di bawah untuk membagikan materi pelajaran.</p>
    </div>
</div>

<form method="POST" action="{{ route('guru.learning-modules.store') }}" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div>
        <label for="create_subject_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
        <select name="subject_id" id="create_subject_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" {{ (!old('_method') && old('subject_id') == $subject->id) ? 'selected' : '' }}>
                    {{ $subject->nama_pelajaran }} ({{ $subject->kode_pelajaran }})
                </option>
            @endforeach
        </select>
        @if(!old('_method'))
            @error('subject_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_tahun_ajaran_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
        <select name="tahun_ajaran_id" id="create_tahun_ajaran_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="">-- Pilih Tahun Ajaran --</option>
            @foreach ($tahunAjarans as $ta)
                <option value="{{ $ta->id }}" {{ (!old('_method') && (old('tahun_ajaran_id') == $ta->id || ($selectedTahunAjaranId == $ta->id && !old('tahun_ajaran_id')))) ? 'selected' : '' }}>
                    {{ $ta->tahun_ajaran }} ({{ ucfirst($ta->semester) }})
                </option>
            @endforeach
        </select>
        @if(!old('_method'))
            @error('tahun_ajaran_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Judul Modul</label>
        <input type="text" name="title" id="create_title" value="{{ !old('_method') ? old('title') : '' }}" required
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        @if(!old('_method'))
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div>
        <label for="create_description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Deskripsi Modul</label>
        <textarea name="description" id="create_description" rows="4" required
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">{{ !old('_method') ? old('description') : '' }}</textarea>
        @if(!old('_method'))
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>



    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Modul
        </button>
    </div>
</form>
