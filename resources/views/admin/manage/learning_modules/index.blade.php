<x-app-layout>
    <x-slot name="header">
        {{ __('Modul Pembelajaran Guru') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateModal: {{ $errors->any() ? 'true' : 'false' }}
    }">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <div>
                <h3 class="text-base font-bold text-gray-800">Daftar Modul Pembelajaran Guru</h3>
                <p class="text-xs text-gray-400 mt-1">Daftar modul pembelajaran yang dibuat berdasarkan Penugasan Guru & Plotting Kurikulum Kelas.</p>
            </div>
            <button @click="showCreateModal = true"
                    class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 shadow-sm cursor-pointer select-none">
                + Tambah Modul Pembelajaran
            </button>
        </div>

        <!-- Status Notification -->
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

        <!-- Filter Panel -->
        <form method="GET" action="{{ route('admin.manage.learning-modules.index') }}" class="bg-white rounded-2xl border border-gray-100 p-5 mb-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filter Kelas -->
                <div>
                    <label for="classroom_id" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Kelas</label>
                    <select name="classroom_id" id="classroom_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all cursor-pointer">
                        <option value="">Semua Kelas</option>
                        @foreach ($classrooms as $cls)
                            <option value="{{ $cls->id }}" {{ $selectedClassroomId == $cls->id ? 'selected' : '' }}>
                                {{ $cls->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Semester -->
                <div>
                    <label for="semester_id" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Semester</label>
                    <select name="semester_id" id="semester_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all cursor-pointer">
                        <option value="">Semua Semester / Tahun Ajaran</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $selectedSemesterId == $sem->id ? 'selected' : '' }}>
                                {{ $sem->tahunAkademik->tahun_ajaran }} - Semester {{ ucfirst($sem->semester) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Mata Pelajaran -->
                <div>
                    <label for="mata_pelajaran_id" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" id="mata_pelajaran_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all cursor-pointer">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach ($mataPelajarans as $mapel)
                            <option value="{{ $mapel->id }}" {{ $selectedMataPelajaranId == $mapel->id ? 'selected' : '' }}>
                                {{ $mapel->nama_pelajaran }} ({{ $mapel->kode_pelajaran }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Guru -->
                <div>
                    <label for="guru_id" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Guru Pengampu</label>
                    <select name="guru_id" id="guru_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all cursor-pointer">
                        <option value="">Semua Guru</option>
                        @foreach ($gurus as $gr)
                            <option value="{{ $gr->id }}" {{ $selectedGuruId == $gr->id ? 'selected' : '' }}>
                                {{ $gr->user->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Clear Filters Link -->
            @if($selectedClassroomId || $selectedSemesterId || $selectedMataPelajaranId || $selectedGuruId)
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.manage.learning-modules.index') }}"
                       class="text-xs text-red-600 hover:text-red-800 font-semibold flex items-center gap-1 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Bersihkan Filter
                    </a>
                </div>
            @endif
        </form>

        <!-- Module Table Card -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <!-- Decorative Top Gradient Line -->
            <div class="h-1.5 bg-gradient-to-r from-blue-600 to-[#0c2b4d]"></div>

            <div class="p-3 sm:p-6">
                <div class="overflow-x-auto">
                    @if ($learningModules->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/75">
                                <tr>
                                    <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas & TA</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Guru Pengampu</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($learningModules as $index => $module)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="block text-sm font-semibold text-gray-900">{{ $module->mataPelajaran->nama_pelajaran ?? 'N/A' }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-100">
                                                {{ $module->mataPelajaran->kode_pelajaran ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="block text-sm font-medium text-gray-700">{{ $module->classroom->nama_kelas ?? 'N/A' }}</span>
                                            <span class="block text-xs text-gray-400 font-normal mt-0.5">{{ $module->tahunAkademik->tahun_ajaran ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="block text-sm font-semibold text-gray-900">{{ $module->guru->user->name ?? 'N/A' }}</span>
                                            <span class="block text-[10px] text-gray-400 font-normal mt-0.5">NIP: {{ $module->guru->nip ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <form action="{{ route('admin.manage.learning-modules.destroy', $module->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul pembelajaran ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer select-none">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-20 h-20 bg-blue-50 text-gray-300 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-4">Belum Ada Modul Pembelajaran</h4>
                            <button @click="showCreateModal = true"
                                    class="inline-flex items-center gap-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer select-none">
                                + Tambah Modul Pembelajaran
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreateModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showCreateModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-[#0c2b4d]"></div>
                <div class="p-4 sm:p-6 lg:p-8">
                    @include('admin.manage.learning_modules.create')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
