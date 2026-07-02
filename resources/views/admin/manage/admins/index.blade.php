<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Admin') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        showEditModal: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
        editData: {
            id: '{{ old('id') ?? '' }}',
            name: {{ json_encode(old('name') ?? '') }},
            username: {{ json_encode(old('username') ?? '') }},
            fullname: {{ json_encode(old('fullname') ?? '') }},
            email: {{ json_encode(old('email') ?? '') }},
            nip: {{ json_encode(old('nip') ?? '') }}
        },
        editUrl: '{{ old('id') ? route('admin.manage.admins.update', old('id')) : '' }}'
    }">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 mb-6">
            <h3 class="text-base font-bold text-gray-800">Daftar Akun Admin</h3>
            <button @click="showCreateModal = true" 
                    class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                + Tambah Admin
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
                    @if ($admins->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/75">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Username</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NIP</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($admins as $userObj)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $userObj->admin->fullname ?? $userObj->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $userObj->username ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $userObj->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $userObj->admin->nip ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                            <div class="flex items-center justify-center gap-3">
                                                <button @click="
                                                    showEditModal = true;
                                                    editData = {
                                                        id: '{{ $userObj->id }}',
                                                        name: {{ json_encode($userObj->name) }},
                                                        username: {{ json_encode($userObj->username ?? '') }},
                                                        fullname: {{ json_encode($userObj->admin->fullname ?? '') }},
                                                        email: {{ json_encode($userObj->email) }},
                                                        nip: {{ json_encode($userObj->admin->nip ?? '') }}
                                                    };
                                                    editUrl = '{{ route('admin.manage.admins.update', $userObj->id) }}';
                                                 " 
                                                 class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer select-none">
                                                    Edit
                                                </button>
                                                
                                                @if(auth()->id() !== $userObj->id)
                                                    <form action="{{ route('admin.manage.admins.destroy', $userObj->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-300 px-3 py-1.5 rounded-lg text-xs font-bold select-none cursor-not-allowed bg-gray-50 border border-gray-100">
                                                        Hapus
                                                    </span>
                                                @endif
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
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0110.089 18a11.374 11.374 0 01-9.333-2.978m9.333 2.978v-.114c0-2.637-1.893-4.833-4.417-5.32M14.286 9.75a3 3 0 11-3-3 3 3 0 013 3zm-6 0a3 3 0 11-3-3 3 3 0 013 3z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Akun Admin</h4>
                            <p class="text-sm text-gray-400 max-w-sm mb-6">Akun administrator yang mengelola sistem akan terdaftar di sini.</p>
                            <button @click="showCreateModal = true" 
                                    class="inline-flex items-center gap-2 bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                                + Tambah Admin
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
                    @include('admin.manage.admins.create')
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
                    @include('admin.manage.admins.edit')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
