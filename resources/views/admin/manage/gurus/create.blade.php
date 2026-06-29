<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Akun Guru</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan isi detail data guru baru.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.manage.gurus.store') }}" class="space-y-4">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Tampilan (Display Name)</label>
            <input type="text" name="name" id="create_name" value="{{ !old('_method') ? old('name') : '' }}" required autofocus
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_username" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Username Login</label>
            <input type="text" name="username" id="create_username" value="{{ !old('_method') ? old('username') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_fullname" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
            <input type="text" name="fullname" id="create_fullname" value="{{ !old('_method') ? old('fullname') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('fullname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_gelar" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Gelar Akademik (Misal: S.Pd, M.Kom)</label>
            <input type="text" name="gelar" id="create_gelar" value="{{ !old('_method') ? old('gelar') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('gelar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email</label>
            <input type="email" name="email" id="create_email" value="{{ !old('_method') ? old('email') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_noWhatsapp" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">No. WhatsApp</label>
            <input type="text" name="noWhatsapp" id="create_noWhatsapp" value="{{ !old('_method') ? old('noWhatsapp') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('noWhatsapp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_nuptk" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">NUPTK</label>
            <input type="text" name="nuptk" id="create_nuptk" value="{{ !old('_method') ? old('nuptk') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('nuptk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_nip" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">NIP</label>
            <input type="text" name="nip" id="create_nip" value="{{ !old('_method') ? old('nip') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('nip') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_tanggalLahir" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
            <input type="date" name="tanggalLahir" id="create_tanggalLahir" value="{{ !old('_method') ? old('tanggalLahir') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('tanggalLahir') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
        
        <div>
            <label for="create_alamat" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat</label>
            <input type="text" name="alamat" id="create_alamat" value="{{ !old('_method') ? old('alamat') : '' }}"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Password</label>
            <input type="text" name="password" id="create_password" value="ChangeMe@123" readonly
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed select-none rounded-xl focus:outline-none text-sm transition-all shadow-sm">
            @if(!old('_method'))
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
            <input type="text" name="password_confirmation" id="create_password_confirmation" value="ChangeMe@123" readonly
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed select-none rounded-xl focus:outline-none text-sm transition-all shadow-sm">
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showCreateModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Akun
        </button>
    </div>
</form>
