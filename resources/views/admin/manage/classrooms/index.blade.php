<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Kelas') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        showEditModal: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
        editData: {
            id: '{{ old('id') ?? '' }}',
            nama_kelas: {{ json_encode(old('nama_kelas') ?? '') }},
            jurusan_id: '{{ old('jurusan_id') ?? '' }}',
            tahun_akademik_id: '{{ old('tahun_akademik_id') ?? '' }}',
            is_active: '{{ old('is_active') !== null ? (old('is_active') ? '1' : '0') : '' }}'
        },
        editUrl: '{{ old('id') ? route('admin.manage.classrooms.update', old('id')) : '' }}',
        searchQuery: '',
        filterJurusan: 'all'
    }">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-6">
            <h3 class="text-base font-bold text-gray-800">Daftar Kelas</h3>
            <button @click="showCreateModal = true"
                    class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                + Tambah Kelas
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

        <!-- Search & Filter Panel -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Search Bar -->
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text" x-model="searchQuery" placeholder="Cari..."
                       class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs text-gray-800 focus:outline-none focus:bg-white focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] transition-all placeholder-gray-400">
                <button x-show="searchQuery !== ''" @click="searchQuery = ''" style="display: none;"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Jurusan:</span>
                <select x-model="filterJurusan"
                        class="w-full sm:w-48 truncate px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] transition-all shadow-sm cursor-pointer">
                    <option value="all">Semua Jurusan</option>
                    @foreach ($jurusans as $jur)
                        <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <!-- Decorative Top Gradient Line -->
            <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>

            <div class="p-3 sm:p-6">
                <div class="overflow-x-auto font-sans">
                    @if ($classrooms->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/75">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jurusan</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($classrooms as $classroom)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-150"
                                        x-show="(searchQuery === '' ||
                                                 {{ json_encode(strtolower($classroom->nama_kelas)) }}.includes(searchQuery.toLowerCase()) ||
                                                 {{ json_encode(strtolower($classroom->jurusan->nama_jurusan ?? '')) }}.includes(searchQuery.toLowerCase()) ||
                                                 {{ json_encode(strtolower($classroom->tahunAkademik->tahun_ajaran ?? '')) }}.includes(searchQuery.toLowerCase())) &&
                                                (filterJurusan === 'all' || filterJurusan === '{{ $classroom->jurusan_id ?? '' }}')">
                                        <td class="px-6 py-2.5 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $classroom->nama_kelas }}</td>
                                        <td class="px-6 py-2.5 whitespace-nowrap text-sm text-gray-700 font-medium">
                                            {{ $classroom->jurusan->nama_jurusan ?? '-' }} ({{ $classroom->jurusan->kode_jurusan ?? '-' }})
                                        </td>
                                        <td class="px-6 py-2.5 whitespace-nowrap text-sm text-gray-500">
                                            {{ $classroom->tahunAkademik->tahun_ajaran ?? '-' }}
                                        </td>
                                        <td class="px-6 py-2.5 whitespace-nowrap text-sm">
                                            @if ($classroom->is_active)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-green-50 text-green-700 border border-green-200 shadow-sm">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-gray-50 text-gray-500 border border-gray-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-2.5 whitespace-nowrap text-sm text-center font-medium">
                                            <div class="flex items-center justify-center gap-3">
                                                <button @click="
                                                    showEditModal = true;
                                                    editData = {
                                                        id: '{{ $classroom->id }}',
                                                        nama_kelas: {{ json_encode($classroom->nama_kelas) }},
                                                        jurusan_id: '{{ $classroom->jurusan_id }}',
                                                        tahun_akademik_id: '{{ $classroom->tahun_akademik_id }}',
                                                        is_active: '{{ $classroom->is_active ? '1' : '0' }}'
                                                    };
                                                    editUrl = '{{ route('admin.manage.classrooms.update', $classroom->id) }}';
                                                 "
                                                 class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer select-none">
                                                    Edit
                                                </button>

                                                <form action="{{ route('admin.manage.classrooms.destroy', $classroom->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Kelas</h4>
                            <p class="text-sm text-gray-400 max-w-sm mb-6">Data kelas yang Anda tambahkan untuk keperluan akademik sekolah akan muncul di sini.</p>
                            <button @click="showCreateModal = true"
                                    class="inline-flex items-center gap-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                                + Tambah Kelas
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
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-4 sm:p-6 lg:p-8">
                    @include('admin.manage.classrooms.create')
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showEditModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-4 sm:p-6 lg:p-8">
                    @include('admin.manage.classrooms.edit')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
