<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Modul Pembelajaran</h3>
        <p class="text-xs text-gray-400 mt-0.5">Buat modul pembelajaran baru untuk guru dan kelas terpilih.</p>
    </div>
</div>

<div x-data="{
    selectedGuruId: '{{ old('guru_id', '') }}',
    selectedTaId: '{{ old('tahun_akademik_id', '') }}',
    selectedSubjectId: '{{ old('mata_pelajaran_id', '') }}',
    selectedClassId: '{{ old('classroom_id', '') }}',
    allGurus: {{ json_encode($gurus->map(function($g) {
        return [
            'id' => (string)$g->id,
            'name' => $g->user?->name ?? 'N/A',
            'nip' => $g->nip ?? '-',
            'assigned_subject_ids' => $g->mataPelajarans->pluck('id')->map(fn($id) => (string)$id)->values()->toArray()
        ];
    })) }},
    allSubjects: {{ json_encode($mataPelajarans->map(function($mp) {
        return [
            'id' => (string)$mp->id,
            'nama_pelajaran' => $mp->nama_pelajaran,
            'kode_pelajaran' => $mp->kode_pelajaran,
        ];
    })) }},
    allClassrooms: {{ json_encode($classrooms->map(function($cls) {
        return [
            'id' => (string)$cls->id,
            'nama_kelas' => $cls->nama_kelas,
            'tahun_akademik_id' => (string)$cls->tahun_akademik_id,
            'assigned_subject_ids' => $cls->mataPelajarans->pluck('id')->map(fn($id) => (string)$id)->values()->toArray()
        ];
    })) }},
    get availableSubjects() {
        if (!this.selectedGuruId) return this.allSubjects;
        const guru = this.allGurus.find(g => g.id === String(this.selectedGuruId));
        if (!guru || !guru.assigned_subject_ids) return this.allSubjects;
        return this.allSubjects.filter(s => guru.assigned_subject_ids.includes(s.id));
    },
    get availableClassrooms() {
        return this.allClassrooms.filter(c => {
            const matchesTa = !this.selectedTaId || c.tahun_akademik_id === String(this.selectedTaId);
            const matchesSubject = !this.selectedSubjectId || c.assigned_subject_ids.includes(String(this.selectedSubjectId));
            return matchesTa && matchesSubject;
        });
    },
    onGuruChange() {
        if (this.selectedSubjectId && !this.availableSubjects.some(s => s.id === String(this.selectedSubjectId))) {
            this.selectedSubjectId = '';
        }
        this.onFilterChange();
    },
    onFilterChange() {
        if (this.selectedClassId && !this.availableClassrooms.some(c => c.id === String(this.selectedClassId))) {
            this.selectedClassId = '';
        }
    }
}">
    <form method="POST" action="{{ route('admin.manage.learning-modules.store') }}" class="space-y-4">
        @csrf

        <!-- Guru Pengampu -->
        <div>
            <label for="create_guru_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Guru Pengampu <span class="text-red-500">*</span></label>
            <select name="guru_id" id="create_guru_id" x-model="selectedGuruId" @change="onGuruChange()" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm cursor-pointer">
                <option value="" disabled {{ !old('guru_id') ? 'selected' : '' }}>-- Pilih Guru --</option>
                @foreach ($gurus as $gr)
                    <option value="{{ $gr->id }}">
                        {{ $gr->user->name ?? 'N/A' }} (NIP: {{ $gr->nip ?? '-' }})
                    </option>
                @endforeach
            </select>
            @error('guru_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>

        <!-- Mata Pelajaran (Filtered dynamically by selected Guru) -->
        <div>
            <label for="create_mata_pelajaran_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Mata Pelajaran <span class="text-red-500">*</span></label>
            <select name="mata_pelajaran_id" id="create_mata_pelajaran_id" x-model="selectedSubjectId" @change="onFilterChange()" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm cursor-pointer">
                <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                <template x-for="mp in availableSubjects" :key="mp.id">
                    <option :value="mp.id" x-text="mp.nama_pelajaran + ' (' + mp.kode_pelajaran + ')'"></option>
                </template>
            </select>
            <p x-show="selectedGuruId && availableSubjects.length === 0" class="text-amber-600 text-xs mt-1 font-medium">
                Guru ini belum ditugaskan mengampu mata pelajaran apa pun dalam Penugasan Guru.
            </p>
            @error('mata_pelajaran_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>

        <!-- Tahun Akademik -->
        <div>
            <label for="create_tahun_akademik_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Akademik <span class="text-red-500">*</span></label>
            <select name="tahun_akademik_id" id="create_tahun_akademik_id" x-model="selectedTaId" @change="onFilterChange()" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm cursor-pointer">
                <option value="" disabled>-- Pilih Tahun Akademik --</option>
                @foreach ($academicYears as $ta)
                    <option value="{{ $ta->id }}">
                        {{ $ta->tahun_ajaran }}
                    </option>
                @endforeach
            </select>
            @error('tahun_akademik_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>

        <!-- Kelas (Filtered dynamically by Mata Pelajaran & Tahun Akademik) -->
        <div>
            <label for="create_classroom_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kelas <span class="text-red-500">*</span></label>
            <select name="classroom_id" id="create_classroom_id" x-model="selectedClassId" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm cursor-pointer">
                <option value="" disabled>-- Pilih Kelas --</option>
                <template x-for="cls in availableClassrooms" :key="cls.id">
                    <option :value="cls.id" x-text="cls.nama_kelas"></option>
                </template>
            </select>
            <p x-show="(selectedSubjectId || selectedTaId) && availableClassrooms.length === 0" class="text-amber-600 text-xs mt-1 font-medium">
                Tidak ada kelas yang di-assign mata pelajaran ini dalam Kurikulum Kelas pada Tahun Akademik terpilih.
            </p>
            @error('classroom_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
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
</div>
