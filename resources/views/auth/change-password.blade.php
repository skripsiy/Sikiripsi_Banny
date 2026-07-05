<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('Ganti Password') }} - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden relative">
            <!-- Decorative concentric ring accent -->
            <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full border-[12px] border-blue-500/5 pointer-events-none"></div>
            
            <div class="p-4 sm:p-6 lg:p-8">
                <!-- Icon and Title -->
                <div class="flex flex-col items-center mb-6 text-center">
                    <div class="w-16 h-16 bg-blue-50 text-[#0c2b4d] rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-extrabold text-[#0c2b4d] tracking-tight">Ganti Password Anda</h2>
                    <p class="text-sm text-gray-500 mt-1">Demi keamanan akun, silakan ubah password default Anda.</p>
                </div>

                <!-- Session Status / Warning Alert -->
                @if (session('warning'))
                    <div class="mb-4 p-4 rounded-lg bg-amber-50 border border-amber-200 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs text-amber-800 leading-normal">{{ session('warning') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password Baru <span class="text-red-500">*</span></label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               placeholder="Min. 8 karakter, kombinasi huruf & angka"
                               required 
                               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] placeholder-gray-400 text-gray-800 transition-all shadow-sm @error('password') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                        <input id="password_confirmation" 
                               type="password" 
                               name="password_confirmation" 
                               placeholder="Ulangi password baru"
                               required 
                               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] placeholder-gray-400 text-gray-800 transition-all shadow-sm">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#0c2b4d] hover:bg-[#07192d] active:bg-[#040f1c] text-white font-semibold py-3 px-4 rounded-xl mt-6 shadow-md hover:shadow-lg transition-all duration-150 text-center select-none cursor-pointer">
                        Perbarui Password
                    </button>
                </form>

                <!-- Logout Link -->
                <div class="mt-6 text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors underline cursor-pointer select-none">
                            Keluar dari Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
