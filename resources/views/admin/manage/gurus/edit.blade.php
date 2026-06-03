<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.manage.gurus.index') }}" 
               class="text-gray-500 hover:text-[#0c2b4d] hover:bg-gray-100 p-1.5 rounded-lg transition-colors flex items-center justify-center"
               title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Guru') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto font-sans">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-150 p-8">
            <form method="POST" action="{{ route('admin.manage.gurus.update', $guru->id) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $guru->name) }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $guru->email) }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NUPTK -->
                <div>
                    <label for="nuptk" class="block text-sm font-medium text-gray-700">NUPTK (Nomor Unik Pendidik dan Tenaga Kependidikan)</label>
                    <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk', $guru->guru->nuptk ?? '') }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('nuptk') border-red-500 @enderror">
                    @error('nuptk')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject Specialty -->
                <div>
                    <label for="subject_specialty" class="block text-sm font-medium text-gray-700">Spesialisasi Mata Pelajaran</label>
                    <input type="text" name="subject_specialty" id="subject_specialty" value="{{ old('subject_specialty', $guru->guru->subject_specialty ?? '') }}" required
                           class="mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-colors shadow-sm @error('subject_specialty') border-red-500 @enderror">
                    @error('subject_specialty')
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
