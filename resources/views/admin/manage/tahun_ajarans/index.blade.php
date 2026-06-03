<x-app-layout>
    <x-slot name="header">
        {{ __('Kelola Tahun Ajaran') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-base font-semibold text-gray-700">Daftar Tahun Ajaran</h3>
            <a href="{{ route('admin.manage.tahun-ajarans.create') }}" 
               class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                + Tambah Tahun Ajaran
            </a>
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-150">
            <div class="p-6 text-gray-900">
                <div class="overflow-x-auto font-sans">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Semester</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($tahunAjarans as $ta)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $ta->tahun_ajaran }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ $ta->semester }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($ta->is_active)
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-100">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-50 text-gray-600 border border-gray-100">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('admin.manage.tahun-ajarans.edit', $ta->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors font-semibold">Edit</a>
                                            
                                            <form action="{{ route('admin.manage.tahun-ajarans.destroy', $ta->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors font-semibold cursor-pointer">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">Tidak ada data tahun ajaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
