<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.manage.tahun-ajarans.index') }}" 
               class="text-gray-500 hover:text-[#0c2b4d] hover:bg-gray-100 p-1.5 rounded-lg transition-colors flex items-center justify-center"
               title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Tahun Ajaran') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto font-sans">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <!-- Decorative Top Gradient Line -->
            <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>

            <div class="p-8">
                <!-- Header Inside Card -->
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Formulir Tahun Ajaran</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Silakan isi detail data tahun ajaran baru sekolah.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.manage.tahun-ajarans.store') }}" class="space-y-6">
                    @csrf

                    <!-- Tahun Ajaran -->
                    <div>
                        <label for="tahun_ajaran" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" id="tahun_ajaran" value="{{ old('tahun_ajaran') }}" required autofocus
                               placeholder="Contoh: 2025/2026"
                               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm @error('tahun_ajaran') border-red-500 @enderror">
                        <p class="text-xs text-gray-400 mt-2">Harus tepat 9 karakter dengan format YYYY/YYYY.</p>
                        @error('tahun_ajaran')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Semester -->
                    <div>
                        <label for="semester" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Semester</label>
                        <select name="semester" id="semester" required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm @error('semester') border-red-500 @enderror">
                            <option value="ganjil" {{ old('semester') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap" {{ old('semester') == 'genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Active Status -->
                    <div>
                        <label for="is_active" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Aktif</label>
                        <select name="is_active" id="is_active" required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm @error('is_active') border-red-500 @enderror">
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-2">Jika diset Aktif, tahun ajaran aktif lainnya akan otomatis dinonaktifkan.</p>
                        @error('is_active')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end pt-4">
                        <button type="submit" 
                                class="w-full sm:w-auto bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] active:scale-[0.98] text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 text-center select-none cursor-pointer text-sm">
                            Simpan Tahun Ajaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
