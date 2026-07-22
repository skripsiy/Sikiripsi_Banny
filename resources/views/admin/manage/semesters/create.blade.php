@php
    $ayMap = $academicYears->mapWithKeys(function($ay) {
        $parts = explode('/', $ay->tahun_ajaran);
        $min = count($parts) === 2 ? $parts[0] . '-01-01' : '';
        $max = count($parts) === 2 ? $parts[1] . '-12-31' : '';
        return [$ay->id => ['min' => $min, 'max' => $max]];
    });
@endphp

<div x-data="{
    selectedAy: '{{ old('tahun_akademik_id', '') }}',
    ayBounds: {{ json_encode($ayMap) }},
    get minDate() {
        return this.ayBounds[this.selectedAy]?.min || '';
    },
    get maxDate() {
        return this.ayBounds[this.selectedAy]?.max || '';
    }
}">
    <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
        <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">Tambah Semester</h3>
            <p class="text-xs text-gray-400 mt-0.5">Silakan isi detail data semester baru sekolah.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.manage.semesters.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="create_tahun_akademik_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Akademik <span class="text-red-500">*</span></label>
            <select name="tahun_akademik_id" id="create_tahun_akademik_id" x-model="selectedAy" required autofocus
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
                <option value="" disabled selected>Pilih Tahun Akademik</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">
                        {{ $year->tahun_ajaran }}
                    </option>
                @endforeach
            </select>
            @if(!old('_method'))
                @error('tahun_akademik_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_semester" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Semester <span class="text-red-500">*</span></label>
            <select name="semester" id="create_semester" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
                <option value="ganjil" {{ (!old('_method') && old('semester') === 'ganjil') ? 'selected' : '' }}>Ganjil</option>
                <option value="genap" {{ (!old('_method') && old('semester') === 'genap') ? 'selected' : '' }}>Genap</option>
            </select>
            @if(!old('_method'))
                @error('semester') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="create_start_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" id="create_start_date" value="{{ !old('_method') ? old('start_date') : '' }}" required
                       :min="minDate" :max="maxDate"
                       class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
                @if(!old('_method'))
                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @endif
            </div>
            <div>
                <label for="create_end_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Selesai <span class="text-red-500">*</span></label>
                <input type="date" name="end_date" id="create_end_date" value="{{ !old('_method') ? old('end_date') : '' }}" required
                       :min="minDate" :max="maxDate"
                       class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
                @if(!old('_method'))
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                Simpan Semester
            </button>
        </div>
    </form>
</div>
