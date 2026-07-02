<x-app-layout>
    <x-slot name="header">
        {{ __('Bank Soal') }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans" x-data="{
        showCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        showEditModal: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
        formTipe: '{{ old('tipe') ?? 'pg' }}',
        editData: {
            id: '{{ old('id') ?? '' }}',
            mata_pelajaran_id: '{{ old('mata_pelajaran_id') ?? '' }}',
            tipe: '{{ old('tipe') ?? 'pg' }}',
            pertanyaan: {{ json_encode(old('pertanyaan') ?? '') }},
            pembahasan: {{ json_encode(old('pembahasan') ?? '') }},
            teks_opsi: {
                A: {{ json_encode(old('teks_opsi.A') ?? '') }},
                B: {{ json_encode(old('teks_opsi.B') ?? '') }},
                C: {{ json_encode(old('teks_opsi.C') ?? '') }},
                D: {{ json_encode(old('teks_opsi.D') ?? '') }}
            },
            correct_option: '{{ old('correct_option') ?? '' }}'
        },
        editUrl: '{{ old('id') ? route('guru.bank-soal.update', old('id')) : '' }}'
    }">

        <!-- Banner Info -->
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold">Bank Soal Pembelajaran</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">
                    Kelola seluruh bank pertanyaan Anda berdasarkan mata pelajaran. Soal-soal ini dapat digunakan kembali (reusable) di berbagai kuis dan ujian.
                </p>
            </div>
            <button @click="showCreateModal = true; formTipe = 'pg'"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Soal
            </button>
        </div>

        <!-- Status Notification -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium shadow-sm">
                <ul class="list-disc pl-5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter and Search -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Daftar Pertanyaan</h3>
                <p class="text-[11px] text-gray-400">Menampilkan bank soal Anda yang siap digunakan.</p>
            </div>
            
            <div>
                <form method="GET" action="{{ route('guru.bank-soal.index') }}" class="flex items-center gap-2.5">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Filter Mapel:</span>
                    <select name="mata_pelajaran_id" onchange="this.form.submit()"
                            class="px-4 py-2 bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-700 font-semibold focus:outline-none focus:bg-white focus:border-[#0c2b4d] cursor-pointer min-w-[200px]">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($mata_pelajarans as $sub)
                            <option value="{{ $sub->id }}" {{ $selectedSubjectId == $sub->id ? 'selected' : '' }}>
                                {{ $sub->nama_pelajaran }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- List Soal -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if($soals->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-[10px] font-bold uppercase tracking-wider border-b border-gray-100">
                                <th class="py-4 px-6 w-12 text-center">No</th>
                                <th class="py-4 px-6 w-48">Mata Pelajaran</th>
                                <th class="py-4 px-6 w-24 text-center">Tipe</th>
                                <th class="py-4 px-6">Pertanyaan</th>
                                <th class="py-4 px-6 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700 font-medium">
                            @foreach($soals as $index => $soal)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6 text-center text-gray-400 font-bold">
                                        {{ ($soals->currentPage() - 1) * $soals->perPage() + $index + 1 }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-800">
                                        <span class="bg-blue-50 text-[#0c2b4d] px-2.5 py-1 rounded-lg border border-blue-100 font-bold text-[10px] uppercase">
                                            {{ $soal->mataPelajaran->nama_pelajaran }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold border uppercase {{ $soal->tipe === 'pg' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-purple-50 text-purple-700 border-purple-100' }}">
                                            {{ $soal->tipe === 'pg' ? 'Pilgan' : 'Essay' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="line-clamp-2 leading-relaxed whitespace-pre-wrap">{!! nl2br(e($soal->pertanyaan)) !!}</div>
                                        @if($soal->gambar_path)
                                            <div class="mt-2.5 text-[10px] text-blue-600 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                </svg>
                                                <span>Memiliki Gambar Pendukung</span>
                                            </div>
                                        @endif
                                        @if($soal->tipe === 'pg')
                                            <div class="grid grid-cols-2 gap-2 mt-2 pl-2 border-l-2 border-gray-150 text-[11px] text-gray-400">
                                                @foreach($soal->options as $opt)
                                                    <div class="{{ $opt->is_correct ? 'text-green-600 font-bold' : '' }}">
                                                        {{ $opt->label }}. {{ $opt->teks_opsi }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Edit -->
                                            <button @click="
                                                showEditModal = true;
                                                formTipe = '{{ $soal->tipe }}';
                                                editData = {
                                                    id: '{{ $soal->id }}',
                                                    mata_pelajaran_id: '{{ $soal->mata_pelajaran_id }}',
                                                    tipe: '{{ $soal->tipe }}',
                                                    pertanyaan: {{ json_encode($soal->pertanyaan) }},
                                                    pembahasan: {{ json_encode($soal->pembahasan) }},
                                                    teks_opsi: {
                                                        @foreach($soal->options as $opt)
                                                            '{{ $opt->label }}': {{ json_encode($opt->teks_opsi) }},
                                                        @endforeach
                                                    },
                                                    correct_option: '{{ $soal->options->firstWhere('is_correct', true)->label ?? '' }}'
                                                };
                                                editUrl = '{{ route('guru.bank-soal.update', $soal->id) }}';
                                            " class="text-gray-400 hover:text-blue-600 p-2 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>

                                            <!-- Hapus -->
                                            <form action="{{ route('guru.bank-soal.destroy', $soal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini dari Bank Soal? Soal yang sudah ditautkan ke kuis/ujian tidak akan hilang namun tidak bisa diedit ulang.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $soals->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-14 text-gray-450 italic">
                    Belum ada soal terdaftar di Bank Soal. Silakan tambahkan baru.
                </div>
            @endif
        </div>

        <!-- Create Modal -->
        <div x-show="showCreateModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showCreateModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-4 sm:p-6 lg:p-8 max-h-[85vh] overflow-y-auto">
                    @include('guru.bank_soal.create')
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center shadow-2xl" style="display: none;">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="showEditModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-w-xl mx-auto z-10 border border-gray-100">
                <div class="h-1.5 bg-gradient-to-r from-[#0c2b4d] to-[#1a4a7d]"></div>
                <div class="p-4 sm:p-6 lg:p-8 max-h-[85vh] overflow-y-auto">
                    @include('guru.bank_soal.edit')
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
