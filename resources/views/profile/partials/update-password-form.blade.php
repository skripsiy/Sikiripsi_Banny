<section>
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
        <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m-5-2a2 2 0 012 2m-5 10v-3a1 1 0 011-1h.5a.5.5 0 00.5-.5v-1a.5.5 0 00-.5-.5H9a.5.5 0 00-.5.5v.5a.5.5 0 01-.5.5H7a.5.5 0 00-.5.5v1.5a.5.5 0 00.5.5h.5a.5.5 0 01.5.5v.5a.5.5 0 00.5.5H9a.5.5 0 00.5-.5v-.5a.5.5 0 01.5-.5h.5a.5.5 0 00.5-.5v-.5zM3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">{{ __('Perbarui Kata Sandi') }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk menjaga keamanan.') }}</p>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Password Saat Ini -->
        <div>
            <label for="update_password_current_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">{{ __('Kata Sandi Saat Ini') }}</label>
            <input type="password" name="current_password" id="update_password_current_password" autocomplete="current-password"
                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm @error('current_password', 'updatePassword') border-red-500 @enderror">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- Password Baru -->
        <div>
            <label for="update_password_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">{{ __('Kata Sandi Baru') }}</label>
            <input type="password" name="password" id="update_password_password" autocomplete="new-password"
                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm @error('password', 'updatePassword') border-red-500 @enderror">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password -->
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">{{ __('Konfirmasi Kata Sandi Baru') }}</label>
            <input type="password" name="password_confirmation" id="update_password_password_confirmation" autocomplete="new-password"
                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm @error('password_confirmation', 'updatePassword') border-red-500 @enderror">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit & Notification -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-4">
            <button type="submit" 
                    class="w-full sm:w-auto bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] active:scale-[0.98] text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 text-center select-none cursor-pointer text-sm">
                {{ __('Perbarui Kata Sandi') }}
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition
                     x-init="setTimeout(() => show = false, 3000)"
                     class="flex items-center gap-1.5 text-xs text-green-600 font-semibold bg-green-50 px-3 py-2 rounded-lg border border-green-200 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ __('Berhasil diperbarui.') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
