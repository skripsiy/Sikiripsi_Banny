<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Murid') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans">
        <!-- Header Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <h3 class="text-base font-semibold text-gray-700">Daftar Akun Murid</h3>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Import Form -->
                <form action="{{ route('admin.manage.murids.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 bg-white px-4 py-1.5 border border-gray-200 rounded-lg shadow-sm">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required 
                           class="text-xs text-gray-500 file:mr-3 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow-sm transition-colors cursor-pointer select-none">
                        Import Excel
                    </button>
                </form>

                <!-- Download Template -->
                <a href="{{ route('admin.manage.murids.template') }}" 
                   class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold transition-colors shadow-sm">
                    Download Template
                </a>

                <!-- Add Button -->
                <a href="{{ route('admin.manage.murids.create') }}" 
                   class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2 rounded-lg text-xs font-semibold transition-colors shadow-sm">
                    + Tambah Murid
                </a>
            </div>
        </div>

        <!-- Import Validation Errors -->
        @if (session('import_errors'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg shadow-sm">
                <p class="font-semibold text-sm mb-2">Gagal mengimpor data. Ditemukan beberapa kesalahan berikut:</p>
                <ul class="list-disc pl-5 text-xs space-y-1">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-150">
            <div class="p-6 text-gray-900">
                <div class="overflow-x-auto font-sans">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NISN</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($murids as $userObj)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $userObj->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $userObj->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $userObj->murid->nisn ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $userObj->murid->class_room ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('admin.manage.murids.edit', $userObj->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors font-semibold">Edit</a>
                                            
                                            <form action="{{ route('admin.manage.murids.destroy', $userObj->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors font-semibold">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">Tidak ada data murid.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
