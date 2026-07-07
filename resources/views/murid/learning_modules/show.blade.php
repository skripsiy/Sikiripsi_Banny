<x-app-layout>
    <x-slot name="header">
        {{ __('Learning Module: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans">

        <!-- Back Button (outside header) -->
        <div class="mb-4">
            <a href="{{ route('murid.learning-modules.index') }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Modul
            </a>
        </div>

        <!-- Module Info Banner -->
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $learningModule->mataPelajaran->nama_pelajaran }} ({{ $learningModule->mataPelajaran->kode_pelajaran }})
                    </span>
                    @if($learningModule->tahunAkademik)
                        <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Tahun Ajaran: {{ $learningModule->tahunAkademik->tahun_ajaran }}
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">{{ $learningModule->description }}</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs bg-white/10 px-3 py-1.5 rounded-lg text-blue-100 flex items-center gap-1.5 select-none">
                        <svg class="w-3.5 h-3.5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="flex items-center gap-1">
                            Pengajar: <strong class="text-white mr-1">{{ $learningModule->guru->user->name ?? 'Guru Pengampu' }}</strong> 
                            <span x-data="{ copied: false }" 
                                  @click="navigator.clipboard.writeText('{{ $learningModule->guru->user->email }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                  class="inline-flex items-center gap-1 bg-white/5 hover:bg-white/10 text-blue-200 hover:text-white px-2 py-0.5 rounded border border-white/15 transition-all duration-150 cursor-pointer select-all relative"
                                  title="Klik untuk menyalin email">
                                <span class="text-[11px] font-medium font-mono">{{ $learningModule->guru->user->email ?? '-' }}</span>
                                <svg x-show="!copied" class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                </svg>
                                <svg x-show="copied" style="display: none;" class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span x-show="copied" x-transition style="display: none;" class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 px-2 py-0.5 bg-gray-900 text-white text-[9px] rounded shadow-lg whitespace-nowrap font-bold">Tersalin!</span>
                            </span>
                        </span>
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Absensi Saya Button -->
                <a href="{{ route('murid.learning-modules.absensi.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
                   class="inline-flex items-center gap-1.5 font-bold px-4 py-2.5 rounded-xl text-xs bg-white/10 hover:bg-white/20 text-white transition-all border border-white/10 select-none cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Absensi Saya
                </a>
                <!-- Rekap Nilai Saya Button -->
                <a href="{{ route('murid.learning-modules.rekap-nilai', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
                   class="inline-flex items-center gap-1.5 font-bold px-4 py-2.5 rounded-xl text-xs bg-emerald-650 hover:bg-emerald-700 text-white transition-all border border-emerald-600 select-none cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path>
                    </svg>
                    Rekap Nilai Saya
                </a>
            </div>
        </div>



        <!-- Statistics / Overview Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 sm:gap-4 mb-6">
            <!-- Absensi Saya Card -->
            <a href="{{ route('murid.learning-modules.absensi.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-blue-200 hover:shadow-md rounded-2xl p-3.5 transition-all duration-200 cursor-pointer select-none">
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <svg class="w-4 h-4 text-gray-350 group-hover/card:text-blue-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Absensi Saya</p>
                        <p class="text-xs font-bold text-gray-750 mt-0.5">Kehadiran Saya</p>
                    </div>
                </div>
            </a>

            <!-- Rekap Nilai Card -->
            <a href="{{ route('murid.learning-modules.rekap-nilai', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-emerald-200 hover:shadow-md rounded-2xl p-3.5 transition-all duration-200 cursor-pointer select-none">
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path>
                            </svg>
                        </div>
                        <svg class="w-4 h-4 text-gray-350 group-hover/card:text-emerald-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Rekap Nilai</p>
                        <p class="text-xs font-bold text-gray-750 mt-0.5">Nilai Tugas Saya</p>
                    </div>
                </div>
            </a>

            <!-- Materi Card -->
            <a href="{{ route('murid.learning-modules.materis.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-indigo-200 hover:shadow-md rounded-2xl p-3.5 transition-all duration-200 cursor-pointer select-none">
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <svg class="w-4 h-4 text-gray-350 group-hover/card:text-indigo-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Materi</p>
                        <p class="text-lg font-extrabold text-gray-800 mt-0.5">{{ $learningModule->materis_count }}</p>
                    </div>
                </div>
            </a>

            <!-- Tugas Card -->
            <a href="{{ route('murid.learning-modules.tugas.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-rose-200 hover:shadow-md rounded-2xl p-3.5 transition-all duration-200 cursor-pointer select-none">
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <svg class="w-4 h-4 text-gray-350 group-hover/card:text-rose-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Tugas</p>
                        <p class="text-lg font-extrabold text-gray-800 mt-0.5">{{ $learningModule->tugas_count }}</p>
                    </div>
                </div>
            </a>

            <!-- Kuis Card -->
            <a href="{{ route('murid.learning-modules.quizzes.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-amber-200 hover:shadow-md rounded-2xl p-3.5 transition-all duration-200 cursor-pointer select-none">
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <svg class="w-4 h-4 text-gray-350 group-hover/card:text-amber-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Kuis</p>
                        <p class="text-lg font-extrabold text-gray-800 mt-0.5">{{ $learningModule->quizzes_count }}</p>
                    </div>
                </div>
            </a>

            <!-- Ujian Card -->
            <a href="{{ route('murid.learning-modules.ujians.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-purple-200 hover:shadow-md rounded-2xl p-3.5 transition-all duration-200 cursor-pointer select-none">
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <svg class="w-4 h-4 text-gray-350 group-hover/card:text-purple-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Ujian</p>
                        <p class="text-lg font-extrabold text-gray-800 mt-0.5">{{ $learningModule->ujians_count }}</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Activities Timeline Card -->
        <div x-data="{ 
            filterType: 'all', 
            filterTime: 'all',
            activities: {{ json_encode($activities) }},
            get filteredActivities() {
                return this.activities.filter(act => {
                    const matchesType = this.filterType === 'all' || act.type === this.filterType;
                    let matchesTime = true;
                    if (this.filterTime === 'week') {
                        matchesTime = act.is_recent;
                    } else if (this.filterTime === 'upcoming') {
                        matchesTime = act.is_upcoming;
                    }
                    return matchesType && matchesTime;
                });
            }
        }" class="bg-white shadow-lg border border-gray-100 rounded-2xl p-4 sm:p-6 mt-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2 select-none">
                        <span class="w-3 h-3 rounded-full bg-[#0c2b4d]"></span>
                        Timeline Aktivitas Pembelajaran
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-1">Daftar materi terbaru, tugas, kuis, dan ujian mendatang untuk modul ini.</p>
                </div>
                
                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <!-- Filter Tipe -->
                    <select x-model="filterType" class="bg-gray-50 border border-gray-200 text-gray-750 text-xs rounded-xl pl-3 pr-8 py-1.5 focus:outline-none focus:border-[#0c2b4d] cursor-pointer">
                        <option value="all">Semua Tipe</option>
                        <option value="materi">Materi</option>
                        <option value="tugas">Tugas</option>
                        <option value="kuis">Kuis</option>
                        <option value="ujian">Ujian</option>
                    </select>

                    <!-- Filter Waktu / Status -->
                    <select x-model="filterTime" class="bg-gray-50 border border-gray-200 text-gray-750 text-xs rounded-xl pl-3 pr-8 py-1.5 focus:outline-none focus:border-[#0c2b4d] cursor-pointer">
                        <option value="all">Semua Waktu</option>
                        <option value="week">Baru Rilis (Minggu Ini)</option>
                        <option value="upcoming">Mendatang (Deadline)</option>
                    </select>
                </div>
            </div>

            <!-- Timeline / Feed List -->
            <div class="relative pl-6 border-l border-gray-150 space-y-6">
                <!-- Timeline Dot and Card for Each Activity -->
                <template x-for="activity in filteredActivities" :key="activity.type + '-' + activity.id">
                    <div class="relative">
                        <!-- Icon indicator on line -->
                        <span class="absolute -left-[31px] top-1.5 w-5 h-5 rounded-full flex items-center justify-center text-white text-[9px] font-bold shadow-sm"
                              :class="{
                                  'bg-indigo-500': activity.type === 'materi',
                                  'bg-rose-500': activity.type === 'tugas',
                                  'bg-amber-500': activity.type === 'kuis',
                                  'bg-purple-500': activity.type === 'ujian'
                              }">
                            <span x-text="activity.type.charAt(0).toUpperCase()"></span>
                        </span>
                        
                        <div class="p-4 bg-gray-50/30 hover:bg-gray-50/70 border border-gray-100 rounded-xl transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-gray-800 text-xs" x-text="activity.title"></h4>
                                    <span class="inline-flex text-[8px] uppercase tracking-wider px-2 py-0.5 rounded font-bold"
                                          :class="{
                                              'bg-indigo-50 text-indigo-700 border border-indigo-150': activity.type === 'materi',
                                              'bg-rose-50 text-rose-700 border border-rose-150': activity.type === 'tugas',
                                              'bg-amber-50 text-amber-700 border border-amber-150': activity.type === 'kuis',
                                              'bg-purple-50 text-purple-700 border border-purple-150': activity.type === 'ujian'
                                          }"
                                          x-text="activity.type"></span>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed whitespace-pre-wrap font-medium" x-text="activity.description"></p>
                            </div>

                            <div class="flex flex-col sm:items-end gap-1 text-[10px] text-gray-400 font-medium whitespace-nowrap self-stretch sm:self-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span>Rilis:</span>
                                    <span class="text-gray-700 font-bold" x-text="activity.created_at_formatted"></span>
                                </div>
                                <template x-if="activity.due_date_formatted">
                                    <div class="flex items-center gap-1 text-red-500 bg-red-50 border border-red-100 px-2 py-0.5 rounded-lg font-bold">
                                        <span>Batas Waktu:</span>
                                        <span x-text="activity.due_date_formatted"></span>
                                    </div>
                                </template>
                                <a :href="activity.url" class="text-xs font-bold text-[#0c2b4d] hover:underline flex items-center gap-0.5 mt-1 sm:mt-0 select-none">
                                    Buka Menu
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Empty State inside filter -->
                <div x-show="filteredActivities.length === 0" class="text-center py-8 text-xs text-gray-450 italic pl-0 -ml-6 border-none select-none">
                    Belum ada aktivitas yang sesuai dengan kriteria filter.
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
