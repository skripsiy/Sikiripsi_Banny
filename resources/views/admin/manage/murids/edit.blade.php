<div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
    <div class="w-12 h-12 bg-blue-50 text-[#0c2b4d] rounded-xl flex items-center justify-center shadow-inner flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </div>
    <div>
        <h3 class="text-lg font-bold text-gray-800">Edit Akun Murid</h3>
        <p class="text-xs text-gray-400 mt-0.5">Silakan perbarui detail data murid.</p>
    </div>
</div>

<form method="POST" :action="editUrl" class="space-y-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" :value="editData.id">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="edit_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Tampilan (Display Name) <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="edit_name" x-model="editData.name" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="edit_username" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Username Login <span class="text-red-500">*</span></label>
            <input type="text" name="username" id="edit_username" x-model="editData.username" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="edit_namaLengkap" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="namaLengkap" id="edit_namaLengkap" x-model="editData.namaLengkap" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('namaLengkap') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="edit_email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="edit_email" x-model="editData.email" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="edit_noTelpon" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">No. Telepon Murid <span class="text-red-500">*</span></label>
            <input type="text" name="noTelpon" id="edit_noTelpon" x-model="editData.noTelpon" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('noTelpon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="edit_tanggalLahir" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
            <input type="date" name="tanggalLahir" id="edit_tanggalLahir" x-model="editData.tanggalLahir" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('tanggalLahir') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div>
        <label for="edit_alamat" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat <span class="text-red-500">*</span></label>
        <input type="text" name="alamat" id="edit_alamat" x-model="editData.alamat" required
               class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        @if(old('_method') === 'PUT')
            @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="edit_namaOrangTua" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Orang Tua <span class="text-red-500">*</span></label>
            <input type="text" name="namaOrangTua" id="edit_namaOrangTua" x-model="editData.namaOrangTua" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('namaOrangTua') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="edit_no_telepon_orang_tua" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">No. Telp Orang Tua (WhatsApp) <span class="text-red-500">*</span></label>
            <input type="text" name="no_telepon_orang_tua" id="edit_no_telepon_orang_tua" x-model="editData.no_telepon_orang_tua" required placeholder="Contoh: 628123456789"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('no_telepon_orang_tua') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="edit_nisn" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">NISN (10 Digit) <span class="text-red-500">*</span></label>
            <input type="text" name="nisn" id="edit_nisn" x-model="editData.nisn" required
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('nisn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="edit_classroom_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kelas <span class="text-red-500">*</span></label>
            <select name="classroom_id" id="edit_classroom_id" x-model="editData.classroom_id" required
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
                <option value="" disabled>Pilih Kelas</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}">
                        {{ $classroom->nama_kelas }} ({{ $classroom->tahunAkademik->tahun_ajaran ?? '-' }})
                    </option>
                @endforeach
            </select>
            @if(old('_method') === 'PUT')
                @error('classroom_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    <div class="p-3 bg-blue-50 border border-blue-150 rounded-xl text-[11px] text-blue-700 leading-normal">
        Kosongkan password jika Anda tidak ingin mengubahnya.
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="edit_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Password Baru</label>
            <input type="password" name="password" id="edit_password" autocomplete="new-password"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
            @if(old('_method') === 'PUT')
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        <div>
            <label for="edit_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="edit_password_confirmation" autocomplete="new-password"
                   class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#0c2b4d] focus:ring-1 focus:ring-[#0c2b4d] text-sm text-gray-800 transition-all shadow-sm">
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" @click="showEditModal = false"
                class="bg-gray-150 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors cursor-pointer select-none">
            Batal
        </button>
        <button type="submit"
                class="bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d] hover:from-[#081b30] hover:to-[#0c2b4d] text-white font-bold px-5 py-2.5 rounded-xl text-xs transition-colors shadow-sm cursor-pointer select-none">
            Simpan Perubahan
        </button>
    </div>
</form>
