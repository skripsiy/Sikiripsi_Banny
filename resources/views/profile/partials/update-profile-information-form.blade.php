<section>
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
        <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">{{ __('Informasi Profil') }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Perbarui informasi profil akun dan alamat email Anda.') }}</p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">{{ __('Nama Lengkap') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm @error('name', 'updateProfileInformation') border-red-500 @enderror">
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">{{ __('Alamat Email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm @error('email', 'updateProfileInformation') border-red-500 @enderror">
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-xs text-amber-800 leading-relaxed">
                        {{ __('Alamat email Anda belum diverifikasi.') }}
                        <button form="send-verification" class="underline text-xs text-amber-750 hover:text-amber-900 font-bold focus:outline-none transition-colors ml-1">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-xs text-green-600">
                            {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Submit & Notification -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-4">
            <button type="submit" 
                    class="w-full sm:w-auto bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] active:scale-[0.98] text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 text-center select-none cursor-pointer text-sm">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition
                     x-init="setTimeout(() => show = false, 3000)"
                     class="flex items-center gap-1.5 text-xs text-green-600 font-semibold bg-green-50 px-3 py-2 rounded-lg border border-green-200 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ __('Berhasil disimpan.') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
