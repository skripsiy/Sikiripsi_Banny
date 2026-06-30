<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Tambah Akun Murid</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan isi detail data murid baru.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.manage.murids.store') }}" class="space-y-4">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Tampilan (Display Name) <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="create_name" value="{{ !old('_method') ? old('name') : '' }}" required autofocus
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_username" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Username Login <span class="text-red-500">*</span></label>
            <input type="text" name="username" id="create_username" value="{{ !old('_method') ? old('username') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_namaLengkap" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="namaLengkap" id="create_namaLengkap" value="{{ !old('_method') ? old('namaLengkap') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('namaLengkap') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="create_email" value="{{ !old('_method') ? old('email') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_noTelpon" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">No. Telepon Murid <span class="text-red-500">*</span></label>
            <input type="text" name="noTelpon" id="create_noTelpon" value="{{ !old('_method') ? old('noTelpon') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('noTelpon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_tanggalLahir" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
            <input type="date" name="tanggalLahir" id="create_tanggalLahir" value="{{ !old('_method') ? old('tanggalLahir') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('tanggalLahir') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div>
        <label for="create_alamat" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat <span class="text-red-500">*</span></label>
        <input type="text" name="alamat" id="create_alamat" value="{{ !old('_method') ? old('alamat') : '' }}" required
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        @if(!old('_method'))
            @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_namaOrangTua" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Orang Tua <span class="text-red-500">*</span></label>
            <input type="text" name="namaOrangTua" id="create_namaOrangTua" value="{{ !old('_method') ? old('namaOrangTua') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('namaOrangTua') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_no_telepon_orang_tua" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">No. Telp Orang Tua (WhatsApp) <span class="text-red-500">*</span></label>
            <input type="text" name="no_telepon_orang_tua" id="create_no_telepon_orang_tua" value="{{ !old('_method') ? old('no_telepon_orang_tua') : '' }}" required placeholder="Contoh: 628123456789"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('no_telepon_orang_tua') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="create_nisn" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">NISN (10 Digit) <span class="text-red-500">*</span></label>
            <input type="text" name="nisn" id="create_nisn" value="{{ !old('_method') ? old('nisn') : '' }}" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(!old('_method'))
                @error('nisn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="create_classroom_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kelas <span class="text-red-500">*</span></label>
            <select name="classroom_id" id="create_classroom_id" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
                <option value="" disabled {{ !old('classroom_id') ? 'selected' : '' }}>Pilih Kelas</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->nama_kelas }} ({{ $classroom->tahunAkademik->tahun_ajaran ?? '-' }})
                    </option>
                @endforeach
            </select>
            @if(!old('_method'))
                @error('classroom_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
