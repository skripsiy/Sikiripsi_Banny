<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Murid') }}
            </h2>
            <a href="{{ route('admin.manage.murids.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto font-sans">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-150 p-8">
            <form method="POST" action="{{ route('admin.manage.murids.update', $murid->id) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $murid->name) }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $murid->email) }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NISN -->
                <div>
                    <label for="nisn" class="block text-sm font-medium text-gray-700">NISN (Nomor Induk Siswa Nasional)</label>
                    <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $murid->murid->nisn ?? '') }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('nisn') border-red-500 @enderror">
                    @error('nisn')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class Room -->
                <div>
                    <label for="class_room" class="block text-sm font-medium text-gray-700">Kelas</label>
                    <input type="text" name="class_room" id="class_room" value="{{ old('class_room', $murid->murid->class_room ?? '') }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('class_room') border-red-500 @enderror">
                    @error('class_room')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Info Alert -->
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-xs font-medium">
                    Kosongkan password jika Anda tidak ingin mengubahnya.
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" name="password" id="password"
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm">
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
