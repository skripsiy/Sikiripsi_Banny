<form action="{{ route('admin.manage.tahun_akademiks.store') }}" method="POST" x-data="{
    tahunAwal: {{ old('tahun_awal', date('Y')) }},
    get tahunAkhir() {
        return parseInt(this.tahunAwal) + 1;
    }
}">
    @csrf
    <h3 class="text-lg font-bold text-gray-900 mb-6">Tambah Tahun Akademik</h3>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
        <div>
            <label for="tahun_awal" class="block text-sm font-bold text-gray-700 mb-2">Tahun Awal <span class="text-red-500">*</span></label>
            <select name="tahun_awal" id="tahun_awal" x-model="tahunAwal" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#0c2b4d] focus:border-transparent text-sm font-medium text-gray-800 transition-all cursor-pointer">
                @for ($y = date('Y'); $y <= date('Y') + 10; $y++)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
            @error('tahun_awal')
                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tahun_akhir" class="block text-sm font-bold text-gray-700 mb-2">Tahun Akhir <span class="text-red-500">*</span></label>
            <input type="number" name="tahun_akhir" id="tahun_akhir" :value="tahunAkhir" readonly required
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-semibold text-gray-600 focus:outline-none cursor-not-allowed">
            @error('tahun_akhir')
                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-8">
        <button type="button" @click="showCreateModal = false"
                class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-bold transition-all border border-gray-200 cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="px-5 py-2.5 bg-[#0c2b4d] hover:bg-[#07192d] text-white rounded-xl text-sm font-bold transition-all shadow-sm cursor-pointer select-none">
            Simpan
        </button>
    </div>
</form>
