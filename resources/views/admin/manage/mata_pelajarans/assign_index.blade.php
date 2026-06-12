<x-app-layout>
    <x-slot name="header">
        {{ __('Penugasan Guru') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showModal: false,
        isEdit: false,
        formData: {
            id: '',
            mata_pelajaran_id: '',
            guru_id: ''
        },
        submitUrl: ''
    }">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-base font-bold text-gray-800">Daftar Penugasan Guru Pengampu</h3>
            <button @click="
                isEdit = false;
                formData = { id: '', mata_pelajaran_id: '', guru_id: '' };
                submitUrl = '{{ route('admin.manage.penugasan-guru.store') }}';
                showModal = true;
            " class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md cursor-pointer select-none">
                + Tambah Penugasan Guru
            </button>
        </div>

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <!-- Decorative Top Gradient Line -->
            <div class="h-1.5 bg-gradient-to-r from-emerald-600 to-emerald-700"></div>

            <div class="p-6">
                <div class="overflow-x-auto font-sans">
                    @if ($assignments->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/75">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Kode</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Mata Pelajaran</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jurusan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Guru Pengampu</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($assignments as $index => $assignment)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $assignment->mataPelajaran->kode_pelajaran }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ $assignment->mataPelajaran->nama_pelajaran }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($assignment->mataPelajaran->jurusan)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-800 border border-blue-100">
                                                    {{ $assignment->mataPelajaran->jurusan->nama_jurusan }} ({{ $assignment->mataPelajaran->jurusan->kode_jurusan }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-800 border border-purple-100">
                                                    Umum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                                            {{ $assignment->guru->user->name ?? 'N/A' }}
                                            <span class="block text-[10px] text-gray-400 font-normal mt-0.5">NUPTK: {{ $assignment->guru->nuptk ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="
                                                    isEdit = true;
                                                    formData = {
                                                        id: '{{ $assignment->id }}',
                                                        mata_pelajaran_id: '{{ $assignment->mata_pelajaran_id }}',
                                                        guru_id: '{{ $assignment->guru_id }}'
                                                    };
                                                    submitUrl = '{{ route('admin.manage.penugasan-guru.update', $assignment->id) }}';
                                                    showModal = true;
                                                 " 
                                                 class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3.5 py-2 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer select-none">
                                                    Edit
                                                </button>

                                                <form action="{{ route('admin.manage.penugasan-guru.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Hapus penugasan guru ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3.5 py-2 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer select-none">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Penugasan Guru</h4>
                            <p class="text-sm text-gray-400 max-w-sm">Tambahkan penugasan guru pengampu mata pelajaran dengan mengklik tombol Tambah di atas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <!-- Modal Background Backdrop -->
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <!-- Modal Content Panel -->
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-[#0c2b4d]"></div>
                <div class="p-8">
                    <!-- Modal Title Header -->
                    <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                        <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94-3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Penugasan Guru' : 'Tambah Penugasan Guru'"></h3>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="isEdit ? 'Ubah data penugasan guru pengampu.' : 'Tugaskan guru pengampu untuk mata pelajaran.'"></p>
                        </div>
                    </div>

                    <!-- Form -->
                    <form method="POST" :action="submitUrl" class="space-y-4">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Subject -->
                        <div>
                            <label for="mata_pelajaran_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" id="mata_pelajaran_id" x-model="formData.mata_pelajaran_id" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-[#0c2b4d] focus:bg-white transition-all">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach ($mata_pelajarans as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->nama_pelajaran }} ({{ $subject->kode_pelajaran }}) 
                                        @if($subject->jurusan)
                                            - {{ $subject->jurusan->kode_jurusan }}
                                        @else
                                            - Umum
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Guru -->
                        <div>
                            <label for="guru_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Guru Pengampu</label>
                            <select name="guru_id" id="guru_id" x-model="formData.guru_id" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-[#0c2b4d] focus:bg-white transition-all">
                                <option value="">-- Pilih Guru --</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}">
                                        {{ $guru->user->name ?? 'N/A' }} (NUPTK: {{ $guru->nuptk ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false"
                                    class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
                                Batal
                            </button>
                            <button type="submit"
                                    class="bg-gradient-to-r from-[#0c2b4d] to-[#163f6b] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
                                Simpan Penugasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
