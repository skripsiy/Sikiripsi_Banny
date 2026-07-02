<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Jurusan') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        showEditModal: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
        editData: {
            id: '{{ old('id') ?? '' }}',
            kode_jurusan: {{ json_encode(old('kode_jurusan') ?? '') }},
            nama_jurusan: {{ json_encode(old('nama_jurusan') ?? '') }},
            deskripsi: {{ json_encode(old('deskripsi') ?? '') }},
            is_active: '{{ old('is_active') !== null ? (old('is_active') ? '1' : '0') : '' }}'
        },
        editUrl: '{{ old('id') ? route('admin.manage.jurusans.update', old('id')) : '' }}'
    }">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-6">
            <h3 class="text-base font-bold text-gray-800">Daftar Jurusan</h3>
            <button @click="showCreateModal = true" 
                    class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                + Tambah Jurusan
            </button>
        </div>
        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <!-- Decorative Top Gradient Line -->
            <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>

            <div class="p-3 sm:p-6">
                <div class="overflow-x-auto font-sans">
                    @if ($jurusans->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/75">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Jurusan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($jurusans as $jurusan)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $jurusan->kode_jurusan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ $jurusan->nama_jurusan }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $jurusan->deskripsi ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($jurusan->is_active)
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                            <div class="flex items-center justify-center gap-3">
                                                <button @click="
                                                    showEditModal = true;
                                                    editData = {
                                                        id: '{{ $jurusan->id }}',
                                                        kode_jurusan: {{ json_encode($jurusan->kode_jurusan) }},
                                                        nama_jurusan: {{ json_encode($jurusan->nama_jurusan) }},
                                                        deskripsi: {{ json_encode($jurusan->deskripsi ?? '') }},
                                                        is_active: '{{ $jurusan->is_active ? '1' : '0' }}'
                                                    };
                                                    editUrl = '{{ route('admin.manage.jurusans.update', $jurusan->id) }}';
                                                 " 
                                                 class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer select-none">
                                                    Edit
                                                </button>
                                                
                                                <form action="{{ route('admin.manage.jurusans.destroy', $jurusan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?');" class="inline">
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
                        <!-- Premium Empty State Placement -->
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-20 h-20 bg-blue-50 text-gray-300 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Jurusan</h4>
                            <p class="text-sm text-gray-400 max-w-sm mb-6">Data jurusan yang Anda tambahkan untuk keperluan akademik sekolah akan muncul di sini.</p>
                            <button @click="showCreateModal = true" 
                                    class="inline-flex items-center gap-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                                + Tambah Jurusan
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
                    @include('admin.manage.jurusans.create')
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
                    @include('admin.manage.jurusans.edit')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
