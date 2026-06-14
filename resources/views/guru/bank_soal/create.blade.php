<h3 class="text-base font-bold text-gray-800 mb-6">Tambah Soal Baru</h3>

<form action="{{ route('guru.bank-soal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    
    <!-- Mapel -->
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Mata Pelajaran</label>
        <select name="mata_pelajaran_id" required
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] transition-all">
            <option value="" disabled selected>Pilih Mata Pelajaran</option>
            @foreach($mata_pelajarans as $sub)
                <option value="{{ $sub->id }}" {{ old('mata_pelajaran_id', $selectedSubjectId) == $sub->id ? 'selected' : '' }}>
                    {{ $sub->nama_pelajaran }}
                </option>
            @endforeach
        </select>
        @if(!old('_method'))
            @error('mata_pelajaran_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <!-- Tipe -->
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Tipe Soal</label>
        <select name="tipe" x-model="formTipe" required
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] transition-all">
            <option value="pg">Pilihan Ganda</option>
            <option value="essay">Essay</option>
        </select>
        @if(!old('_method'))
            @error('tipe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <!-- Pertanyaan -->
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Pertanyaan</label>
        <textarea name="pertanyaan" required rows="3" placeholder="Ketik soal disini..."
                  class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:bg-white focus:border-[#0c2b4d] transition-all placeholder-gray-400">{{ !old('_method') ? old('pertanyaan') : '' }}</textarea>
        @if(!old('_method'))
            @error('pertanyaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <!-- Gambar -->
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Gambar Pendukung (Opsional)</label>
        <input type="file" name="gambar" accept="image/*"
               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-600 focus:outline-none focus:bg-white transition-all">
        <p class="text-[10px] text-gray-400 mt-1">Hanya gambar (.jpg, .jpeg, .png), maks 2MB.</p>
        @if(!old('_method'))
            @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <!-- Form PG options -->
    <div x-show="formTipe === 'pg'" class="space-y-3 p-4 bg-gray-50/50 rounded-xl border border-gray-100">
        <h4 class="text-xs font-bold text-gray-700 mb-1">Opsi Jawaban & Kunci PG</h4>
        
        @foreach(['A', 'B', 'C', 'D'] as $label)
            <div class="flex items-center gap-2">
                <input type="radio" name="correct_option" value="{{ $label }}" x-bind:required="formTipe === 'pg'"
                       {{ !old('_method') && old('correct_option') === $label ? 'checked' : '' }}
                       class="w-4 h-4 text-[#0c2b4d] focus:ring-[#0c2b4d] border-gray-300">
                <span class="text-xs font-bold text-gray-500 w-4">{{ $label }}.</span>
                <input type="text" name="teks_opsi[{{ $label }}]" placeholder="Isi opsi {{ $label }}" x-bind:required="formTipe === 'pg'"
                       value="{{ !old('_method') ? old('teks_opsi.'.$label) : '' }}"
                       class="flex-grow px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-[#0c2b4d] transition-all">
            </div>
        @endforeach
        @if(!old('_method'))
            @error('correct_option') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('teks_opsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <!-- Pembahasan -->
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Kunci / Pembahasan (Opsional)</label>
        <textarea name="pembahasan" rows="2" placeholder="Catatan pembahasan atau kunci..."
                  class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:bg-white focus:border-[#0c2b4d] transition-all placeholder-gray-400">{{ !old('_method') ? old('pembahasan') : '' }}</textarea>
        @if(!old('_method'))
            @error('pembahasan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <!-- Submit Buttons -->
    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
            Batal
        </button>
        <button type="submit"
                class="px-4 py-2.5 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
            Simpan Soal
        </button>
    </div>
</form>
