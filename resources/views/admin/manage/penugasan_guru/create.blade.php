<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Penugasan Guru</h3>
        <p class="text-xs text-gray-400 mt-0.5">Tugaskan guru pengampu untuk mata pelajaran.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.manage.penugasan-guru.store') }}" class="space-y-4">
    @csrf

    <!-- Subject -->
    <div>
        <label for="create_mata_pelajaran_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Mata Pelajaran <span class="text-red-500">*</span></label>
        <select name="mata_pelajaran_id" id="create_mata_pelajaran_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="" disabled {{ !old('mata_pelajaran_id') ? 'selected' : '' }}>-- Pilih Mata Pelajaran --</option>
            @foreach ($mata_pelajarans as $subject)
                <option value="{{ $subject->id }}" {{ old('mata_pelajaran_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->nama_pelajaran }} ({{ $subject->kode_pelajaran }}) 
                    @if($subject->jurusan)
                        - {{ $subject->jurusan->kode_jurusan }}
                    @else
                        - Umum
                    @endif
                </option>
            @endforeach
        </select>
        @if(!old('_method'))
            @error('mata_pelajaran_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <!-- Guru -->
    <div>
        <label for="create_guru_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Guru Pengampu <span class="text-red-500">*</span></label>
        <select name="guru_id" id="create_guru_id" required
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            <option value="" disabled {{ !old('guru_id') ? 'selected' : '' }}>-- Pilih Guru --</option>
            @foreach ($gurus as $guru)
                <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                     {{ $guru->user->name ?? 'N/A' }} (NIP: {{ $guru->nip ?? '-' }})
                </option>
            @endforeach
        </select>
        @if(!old('_method'))
            @error('guru_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#163f6b] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Penugasan
        </button>
    </div>
</form>
