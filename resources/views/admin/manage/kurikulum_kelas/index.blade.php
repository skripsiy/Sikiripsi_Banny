<x-app-layout>
    <x-slot name="header">
        {{ __('Kurikulum Kelas (Assign Mapel ke Kelas)') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showAssignModal: {{ $errors->any() && !session('import_errors') ? 'true' : 'false' }},
        showImportModal: false,
        selectedClassroom: null,
        selectedSubjectIds: [],
        openSyncModal(cls) {
            this.selectedClassroom = cls;
            this.selectedSubjectIds = cls.mata_pelajarans.map(m => m.id);
            this.showAssignModal = true;
        }
    }">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-gray-800">Plotting Kurikulum Mata Pelajaran Per Kelas</h3>
                <p class="text-xs text-gray-400 mt-1">Tentukan mata pelajaran apa saja yang dipelajari oleh setiap kelas pada struktur kurikulum sekolah.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.manage.kurikulum-kelas.template') }}"
                   class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Template
                </a>
                <button @click="showImportModal = true"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import Excel
                </button>
                <button @click="openSyncModal({ id: '', nama_kelas: '', mata_pelajarans: [] })"
                        class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
                    + Assign Mapel ke Kelas
                </button>
            </div>
        </div>

        <!-- Status & Alert Messages -->
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium shadow-sm flex items-center justify-between">
                <span>{{ session('status') }}</span>
                <button @click="show = false" class="text-green-600 hover:text-green-800 transition-colors p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium shadow-sm flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="text-red-600 hover:text-red-800 transition-colors p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif
        @if (session('import_errors'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm shadow-sm space-y-1">
                <p class="font-bold mb-2">Gagal mengimpor beberapa data:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach (session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Table Card -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-blue-600 to-[#0c2b4d]"></div>
            <div class="p-3 sm:p-6">
                <div class="overflow-x-auto">
                    @if ($classrooms->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/75">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jurusan & TA</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Daftar Mata Pelajaran Ter-assign</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Mapel</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($classrooms as $index => $cls)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="block text-sm font-bold text-gray-900">{{ $cls->nama_kelas }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="block text-xs font-semibold text-gray-700">{{ $cls->jurusan->nama_jurusan ?? '-' }}</span>
                                            <span class="block text-[10px] text-gray-400 font-normal mt-0.5">{{ $cls->tahunAkademik->tahun_ajaran ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($cls->mataPelajarans->isNotEmpty())
                                                <div class="flex flex-wrap gap-1.5 max-w-xl">
                                                    @foreach ($cls->mataPelajarans as $mp)
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#0c2b4d] border border-blue-100">
                                                            {{ $mp->nama_pelajaran }}
                                                            <span class="ml-1 text-[9px] text-blue-500 font-mono">({{ $mp->kode_pelajaran }})</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Belum ada mata pelajaran yang di-assign</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold">
                                            <span class="px-3 py-1 rounded-full text-xs font-extrabold {{ $cls->mataPelajarans->count() > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $cls->mataPelajarans->count() }} Mapel
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="openSyncModal({{ json_encode($cls) }})"
                                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer">
                                                    Atur Mapel
                                                </button>
                                                @if ($cls->mataPelajarans->isNotEmpty())
                                                    <form action="{{ route('admin.manage.kurikulum-kelas.destroy', $cls->id) }}" method="POST" onsubmit="return confirm('Kosongkan penugasan mata pelajaran untuk kelas {{ $cls->nama_kelas }}?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer">
                                                            Kosongkan
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Data Kelas</h4>
                            <p class="text-sm text-gray-400 max-w-sm">Tambahkan data kelas terlebih dahulu pada menu Kelas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Sync / Assign Mapel ke Kelas -->
        <div x-show="showAssignModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showAssignModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showAssignModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <div x-show="showAssignModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-2xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-[#0c2b4d]"></div>
                <div class="p-4 sm:p-6 lg:p-8">
                    <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                        <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800" x-text="selectedClassroom && selectedClassroom.id ? 'Atur Mapel Kelas: ' + selectedClassroom.nama_kelas : 'Assign Mata Pelajaran ke Kelas'"></h3>
                            <p class="text-xs text-gray-400 mt-0.5">Centang mata pelajaran yang dipelajari oleh kelas ini.</p>
                        </div>
                    </div>

                    <form method="POST" :action="selectedClassroom && selectedClassroom.id ? '{{ url('admin/manage/kurikulum-kelas') }}/' + selectedClassroom.id : '{{ route('admin.manage.kurikulum-kelas.store') }}'" class="space-y-5">
                        @csrf
                        <template x-if="selectedClassroom && selectedClassroom.id">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Select Classroom (If creating new) -->
                        <div x-show="!selectedClassroom || !selectedClassroom.id">
                            <label for="classroom_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Kelas <span class="text-red-500">*</span></label>
                            <select name="classroom_id" id="classroom_id" :required="!selectedClassroom || !selectedClassroom.id"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
                                <option value="" disabled selected>-- Pilih Kelas --</option>
                                @foreach ($classrooms as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->nama_kelas }} ({{ $cls->tahunAkademik->tahun_ajaran ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('classroom_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Multi-select Mata Pelajaran Checkboxes -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Mata Pelajaran <span class="text-red-500">*</span></label>
                                <div class="flex gap-2">
                                    <button type="button" @click="selectedSubjectIds = {{ json_encode($mataPelajarans->pluck('id')) }}" class="text-[11px] text-blue-600 hover:underline font-semibold cursor-pointer">Pilih Semua</button>
                                    <span class="text-gray-300 text-xs">|</span>
                                    <button type="button" @click="selectedSubjectIds = []" class="text-[11px] text-red-600 hover:underline font-semibold cursor-pointer">Hapus Semua</button>
                                </div>
                            </div>

                            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50/50 space-y-2 custom-scrollbar">
                                @foreach ($mataPelajarans as $mapel)
                                    <label class="flex items-center gap-3 p-2 bg-white rounded-lg border border-gray-150 hover:border-blue-300 transition-all cursor-pointer select-none">
                                        <input type="checkbox" name="mata_pelajaran_ids[]" value="{{ $mapel->id }}"
                                               x-model="selectedSubjectIds"
                                               class="w-4 h-4 text-[#0c2b4d] border-gray-300 rounded focus:ring-[#0c2b4d]">
                                        <div class="min-w-0 flex-1 flex justify-between items-center">
                                            <span class="text-xs font-bold text-gray-800">{{ $mapel->nama_pelajaran }}</span>
                                            <span class="text-[10px] font-mono font-semibold px-2 py-0.5 bg-blue-50 text-blue-700 rounded border border-blue-100">{{ $mapel->kode_pelajaran }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('mata_pelajaran_ids') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showAssignModal = false"
                                    class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer">
                                Simpan Kurikulum
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div x-show="showImportModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showImportModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-md mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-emerald-600"></div>
                <div class="p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-2">Import Kurikulum Kelas dari Excel</h3>
                    <p class="text-xs text-gray-400 mb-4">Gunakan template format Excel yang disediakan untuk mengimpor data penugasan mapel kelas secara sekaligus.</p>

                    <form action="{{ route('admin.manage.kurikulum-kelas.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                                   class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-200 rounded-xl cursor-pointer">
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showImportModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-colors">
                                Import Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
