<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Atur Guru Pengampu</h3>
        <p class="text-xs text-gray-400 mt-0.5">Tentukan guru pengampu untuk mata pelajaran <span class="font-semibold text-gray-700" x-text="assignData.nama_pelajaran"></span> (<span x-text="assignData.kode_pelajaran"></span>).</p>
    </div>
</div>

<form method="POST" :action="assignUrl" class="space-y-4">
    @csrf

    <div class="max-h-60 overflow-y-auto border border-gray-100 rounded-xl p-4 bg-gray-50/50 space-y-3">
        @if($gurus->isNotEmpty())
            @foreach($gurus as $guru)
                <label class="flex items-center gap-3 p-2 hover:bg-white rounded-lg border border-transparent hover:border-gray-200 transition-all cursor-pointer">
                    <input type="checkbox" name="guru_ids[]" :value="{{ $guru->id }}"
                           x-model="assignData.guru_ids"
                           class="w-4 h-4 rounded text-[#0c2b4d] border-gray-300 focus:ring-[#0c2b4d]">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-800">{{ $guru->user->name }}</span>
                        <span class="text-xs text-gray-400">NUPTK: {{ $guru->nuptk ?? '-' }} | Spesialisasi: {{ $guru->subject_specialty ?? '-' }}</span>
                    </div>
                </label>
            @endforeach
        @else
            <div class="text-center py-6 text-gray-400 text-sm">
                Belum ada data guru terdaftar.
            </div>
        @endif
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showAssignModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Pengampu
        </button>
    </div>
</form>
