<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.manage.murids.index') }}" 
               class="text-gray-500 hover:text-[#0c2b4d] hover:bg-gray-100 p-1.5 rounded-lg transition-colors flex items-center justify-center"
               title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Murid') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto font-sans">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-150 p-8">
            <form method="POST" action="{{ route('admin.manage.murids.store') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NISN -->
                <div>
                    <label for="nisn" class="block text-sm font-medium text-gray-700">NISN (Nomor Induk Siswa Nasional)</label>
                    <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('nisn') border-red-500 @enderror">
                    @error('nisn')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class Room -->
                <div>
                    <label for="class_room" class="block text-sm font-medium text-gray-700">Kelas</label>
                    <input type="text" name="class_room" id="class_room" value="{{ old('class_room') }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('class_room') border-red-500 @enderror">
                    @error('class_room')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm">
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-[#0c2b4d] hover:bg-[#07192d] text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm cursor-pointer select-none">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
