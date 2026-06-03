<section class="space-y-6">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
        <div class="w-12 h-12 bg-red-50 text-red-650 rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">{{ __('Hapus Akun') }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.') }}</p>
        </div>
    </div>

    <!-- Alert Banner -->
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs leading-relaxed">
        {{ __('Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan. Tindakan ini tidak dapat dibatalkan.') }}
    </div>

    <div class="pt-2">
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 select-none cursor-pointer text-sm">
            {{ __('Hapus Akun Saya') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                {{ __('Setelah akun Anda dihapus, semua data akan hilang selamanya. Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">{{ __('Kata Sandi Konfirmasi') }}</label>
                <input type="password" name="password" id="password" placeholder="{{ __('Masukkan Kata Sandi Anda') }}"
                       class="w-full sm:w-3/4 px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm text-gray-800 transition-all shadow-sm">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
                    {{ __('Batal') }}
                </button>

                <button type="submit"
                        class="bg-red-650 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
                    {{ __('Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
