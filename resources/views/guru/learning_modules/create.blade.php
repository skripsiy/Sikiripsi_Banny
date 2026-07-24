<div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Modul Pembelajaran</h3>
        <p class="text-xs text-gray-400 mt-0.5">Pilihan Mata Pelajaran & Kelas otomatis menyesuaikan dengan Penugasan Guru & Kurikulum Kelas dari Admin.</p>
    </div>
</div>

<div x-data="{
    selectedTaId: '{{ old('tahun_akademik_id', '') }}',
    selectedSubjectId: '{{ old('mata_pelajaran_id', '') }}',
    selectedClassId: '{{ old('classroom_id', '') }}',
    adminAssignedModules: {{ json_encode(($adminAssignedModules ?? collect())->map(function($m) {
        return [
            'id' => (string)$m->id,
            'mata_pelajaran_id' => (string)$m->mata_pelajaran_id,
            'nama_pelajaran' => $m->mataPelajaran?->nama_pelajaran ?? 'N/A',
            'kode_pelajaran' => $m->mataPelajaran?->kode_pelajaran ?? '',
            'tahun_akademik_id' => (string)$m->tahun_akademik_id,
            'tahun_ajaran' => $m->tahunAkademik?->tahun_ajaran ?? '',
            'classroom_id' => (string)$m->classroom_id,
            'nama_kelas' => $m->classroom?->nama_kelas ?? 'N/A'
        ];
    })) }},
    get availableSubjects() {
        if (!this.adminAssignedModules || this.adminAssignedModules.length === 0) return [];
        const seen = new Set();
        const list = [];
        this.adminAssignedModules.forEach(m => {
            if (!seen.has(m.mata_pelajaran_id)) {
                seen.add(m.mata_pelajaran_id);
                list.push({ id: m.mata_pelajaran_id, nama_pelajaran: m.nama_pelajaran, kode_pelajaran: m.kode_pelajaran });
            }
        });
        return list;
    },
    get availableAcademicYears() {
        if (!this.adminAssignedModules || this.adminAssignedModules.length === 0) return [];
        const seen = new Set();
        const list = [];
        this.adminAssignedModules.forEach(m => {
            if (!this.selectedSubjectId || m.mata_pelajaran_id === String(this.selectedSubjectId)) {
                if (!seen.has(m.tahun_akademik_id)) {
                    seen.add(m.tahun_akademik_id);
                    list.push({ id: m.tahun_akademik_id, tahun_ajaran: m.tahun_ajaran });
                }
            }
        });
        return list;
    },
    get availableClassrooms() {
        if (!this.adminAssignedModules || this.adminAssignedModules.length === 0) return [];
        const seen = new Set();
        const list = [];
        this.adminAssignedModules.forEach(m => {
            const matchesSubject = !this.selectedSubjectId || m.mata_pelajaran_id === String(this.selectedSubjectId);
            const matchesTa = !this.selectedTaId || m.tahun_akademik_id === String(this.selectedTaId);
            if (matchesSubject && matchesTa) {
                if (!seen.has(m.classroom_id)) {
                    seen.add(m.classroom_id);
                    list.push({ id: m.classroom_id, nama_kelas: m.nama_kelas });
                }
            }
        });
        return list;
    },
    onFilterChange() {
        if (this.selectedTaId && !this.availableAcademicYears.some(a => a.id === String(this.selectedTaId))) {
            this.selectedTaId = '';
        }
        if (this.selectedClassId && !this.availableClassrooms.some(c => c.id === String(this.selectedClassId))) {
            this.selectedClassId = '';
        }
    }
}">
    <form method="POST" action="{{ route('guru.learning-modules.store') }}" class="space-y-4">
        @csrf

        <!-- Mata Pelajaran -->
        <div>
            <label for="create_mata_pelajaran_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Mata Pelajaran <span class="text-red-500">*</span></label>
            <select name="mata_pelajaran_id" id="create_mata_pelajaran_id" x-model="selectedSubjectId" @change="onFilterChange()" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm cursor-pointer">
                <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                <template x-for="subj in availableSubjects" :key="subj.id">
                    <option :value="subj.id" x-text="subj.nama_pelajaran + ' (' + subj.kode_pelajaran + ')'"></option>
                </template>
            </select>

            @if(!old('_method'))
                @error('mata_pelajaran_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            @endif
        </div>

        <!-- Tahun Ajaran -->
        <div>
            <label for="create_tahun_akademik_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Ajaran <span class="text-red-500">*</span></label>
            <select name="tahun_akademik_id" id="create_tahun_akademik_id" x-model="selectedTaId" @change="onFilterChange()" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm cursor-pointer">
                <option value="" disabled selected>-- Pilih Tahun Ajaran --</option>
                <template x-for="ta in availableAcademicYears" :key="ta.id">
                    <option :value="ta.id" x-text="ta.tahun_ajaran"></option>
                </template>
            </select>
            @if(!old('_method'))
                @error('tahun_akademik_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            @endif
        </div>

        <!-- Kelas -->
        <div>
            <label for="create_classroom_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kelas <span class="text-red-500">*</span></label>
            <select name="classroom_id" id="create_classroom_id" x-model="selectedClassId" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm cursor-pointer">
                <option value="" disabled selected>-- Pilih Kelas --</option>
                <template x-for="cls in availableClassrooms" :key="cls.id">
                    <option :value="cls.id" x-text="cls.nama_kelas"></option>
                </template>
            </select>
            @if(!old('_method'))
                @error('classroom_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            @endif
        </div>

        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
            <button type="button" @click="showCreateModal = false"
                    class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
                Batal
            </button>
            <button type="submit"
                    :disabled="adminAssignedModules.length === 0"
                    :class="adminAssignedModules.length === 0 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'"
                    class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm select-none">
                Simpan Modul
            </button>
        </div>
    </form>
</div>
