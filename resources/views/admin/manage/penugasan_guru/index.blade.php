<x-app-layout>
    <x-slot name="header">
        {{ __('Penugasan Guru') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        showEditModal: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
        editData: {
            id: '{{ old('id') ?? '' }}',
            mata_pelajaran_id: '{{ old('mata_pelajaran_id') ?? '' }}',
            guru_id: '{{ old('guru_id') ?? '' }}'
        },
        editUrl: '{{ old('id') ? route('admin.manage.penugasan-guru.update', old('id')) : '' }}',
        searchQuery: '',
        filterGuru: 'all'
    }">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-6">
            <h3 class="text-base font-bold text-gray-800">Daftar Penugasan Guru Pengampu</h3>
            <button @click="showCreateModal = true" 
                    class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                + Tambah Penugasan Guru
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
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Search Bar -->
            <div class="relative w-full md:w-96">
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

            <!-- Guru Filter -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Guru:</span>
                <select x-model="filterGuru"
                        class="px-4 py-2.5 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] transition-all shadow-sm cursor-pointer w-full md:w-auto">
                    <option value="all">Semua Guru</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->user->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <!-- Decorative Top Gradient Line -->
            <div class="h-1.5 bg-gradient-to-r from-emerald-600 to-emerald-700"></div>

            <div class="p-3 sm:p-6">
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
                                    <tr class="hover:bg-gray-50/50 transition-all duration-150"
                                        x-show="(searchQuery === '' || 
                                                 {{ json_encode(strtolower($assignment->mataPelajaran?->kode_pelajaran ?? '')) }}.includes(searchQuery.toLowerCase()) || 
                                                 {{ json_encode(strtolower($assignment->mataPelajaran?->nama_pelajaran ?? '')) }}.includes(searchQuery.toLowerCase()) || 
                                                 {{ json_encode(strtolower($assignment->mataPelajaran?->jurusan->nama_jurusan ?? '')) }}.includes(searchQuery.toLowerCase()) || 
                                                 {{ json_encode(strtolower($assignment->guru->user->name ?? '')) }}.includes(searchQuery.toLowerCase()) || 
                                                 {{ json_encode(strtolower($assignment->guru->nip ?? '')) }}.includes(searchQuery.toLowerCase())) &&
                                                (filterGuru === 'all' || filterGuru === '{{ $assignment->guru_id ?? '' }}')">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ $assignment->mataPelajaran?->kode_pelajaran ?? '-' }}
                                            @if($assignment->mataPelajaran?->trashed())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-700 border border-red-100 ml-1">
                                                    Terhapus
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ $assignment->mataPelajaran?->nama_pelajaran ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($assignment->mataPelajaran?->jurusan)
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
                                            <span class="block text-[10px] text-gray-400 font-normal mt-0.5">NIP: {{ $assignment->guru->nip ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                 @if($assignment->mataPelajaran?->trashed())
                                                    <button disabled 
                                                            title="Mata pelajaran telah dihapus, tidak dapat diubah" 
                                                            class="text-gray-400 bg-gray-100 px-3.5 py-2 rounded-lg text-xs font-bold cursor-not-allowed select-none opacity-60">
                                                        Edit
                                                    </button>
                                                @else
                                                    <button @click="
                                                        showEditModal = true;
                                                        editData = {
                                                            id: '{{ $assignment->id }}',
                                                            mata_pelajaran_id: '{{ $assignment->mata_pelajaran_id }}',
                                                            guru_id: '{{ $assignment->guru_id }}'
                                                        };
                                                        editUrl = '{{ route('admin.manage.penugasan-guru.update', $assignment->id) }}';
                                                     " 
                                                     class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3.5 py-2 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer select-none">
                                                        Edit
                                                    </button>
                                                @endif

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
                            <p class="text-sm text-gray-400 max-w-sm mb-6">Tambahkan penugasan guru pengampu mata pelajaran dengan mengklik tombol Tambah di atas.</p>
                            <button @click="showCreateModal = true" 
                                    class="inline-flex items-center gap-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                                + Tambah Penugasan Guru
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
                    @include('admin.manage.penugasan_guru.create')
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showEditModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-[#0c2b4d]"></div>
                <div class="p-4 sm:p-6 lg:p-8">
                    @include('admin.manage.penugasan_guru.edit')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
