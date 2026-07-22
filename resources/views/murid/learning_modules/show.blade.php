<x-app-layout>
    <x-slot name="header">
        {{ __('Learning Module: ' . $learningModule->title) }}
    </x-slot>

    <div class="max-w-7xl mx-auto font-sans">

        <!-- Back Button & Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <a href="{{ route('murid.learning-modules.index') }}" 
               class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-800 font-bold text-xs transition-colors select-none w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Modul Saya
            </a>

            <!-- Filter Semester Pills -->
            @if ($semesters->isNotEmpty())
                <div class="flex items-center gap-1.5 bg-gray-100/80 p-1 rounded-2xl border border-gray-200/60 self-start sm:self-auto overflow-x-auto max-w-full">
                    <a href="{{ route('murid.learning-modules.show', [$learningModule->id]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ !request('semester_id') ? 'bg-[#0c2b4d] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200/50' }}">
                        Semua Semester
                    </a>
                    @foreach ($semesters as $sem)
                        <a href="{{ route('murid.learning-modules.show', [$learningModule->id, 'semester_id' => $sem->id]) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $selectedSemester?->id == $sem->id ? 'bg-[#0c2b4d] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200/50' }}">
                            <span>Semester {{ ucfirst($sem->semester) }}</span>
                            @if ($sem->is_active)
                                <span class="w-1.5 h-1.5 rounded-full {{ $selectedSemester?->id == $sem->id ? 'bg-emerald-400' : 'bg-emerald-500' }}"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Module Banner Card -->
        <div class="bg-[#0c2b4d] text-white p-4 sm:p-6 rounded-2xl shadow-lg border border-gray-150 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-500/20 text-blue-200 border border-blue-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $learningModule->mataPelajaran->nama_pelajaran }} ({{ $learningModule->mataPelajaran->kode_pelajaran }})
                    </span>
                    <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Guru: {{ $learningModule->guru->user->name ?? 'N/A' }}
                    </span>
                    @if($selectedSemester)
                        <span class="bg-amber-500/20 text-amber-200 border border-amber-500/30 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Semester {{ ucfirst($selectedSemester->semester) }}
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold mt-2.5">{{ $learningModule->title }}</h2>
                <p class="text-xs text-blue-100/70 mt-1 max-w-xl">{{ $learningModule->description }}</p>
            </div>
            <div>
                <a href="{{ route('murid.learning-modules.rekap-nilai', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
                   class="inline-flex items-center gap-1.5 font-bold px-4 py-2.5 rounded-xl text-xs bg-white/10 hover:bg-white/20 text-white transition-all border border-white/10 select-none cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                    </svg>
                    Rekap Nilai Saya
                </a>
            </div>
        </div>

        <!-- Navigation Sub-Menu Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <!-- Materi -->
            <a href="{{ route('murid.learning-modules.materis.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-indigo-200 hover:shadow-md rounded-2xl p-4 transition-all duration-200 cursor-pointer select-none">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Materi</p>
                        <p class="text-sm font-extrabold text-gray-800">{{ $learningModule->materis_count }} Berkas</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-350 group-hover/card:text-indigo-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Tugas -->
            <a href="{{ route('murid.learning-modules.tugas.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-rose-200 hover:shadow-md rounded-2xl p-4 transition-all duration-200 cursor-pointer select-none">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tugas</p>
                        <p class="text-sm font-extrabold text-gray-800">{{ $learningModule->tugas_count }} Tugas</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-350 group-hover/card:text-rose-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Kuis -->
            <a href="{{ route('murid.learning-modules.quizzes.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-amber-200 hover:shadow-md rounded-2xl p-4 transition-all duration-200 cursor-pointer select-none">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kuis</p>
                        <p class="text-sm font-extrabold text-gray-800">{{ $learningModule->quizzes_count }} Kuis</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-350 group-hover/card:text-amber-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Ujian -->
            <a href="{{ route('murid.learning-modules.ujians.index', [$learningModule->id, 'semester_id' => $selectedSemester?->id]) }}"
               class="group/card bg-white border border-gray-100 hover:border-purple-200 hover:shadow-md rounded-2xl p-4 transition-all duration-200 cursor-pointer select-none">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ujian</p>
                        <p class="text-sm font-extrabold text-gray-800">{{ $learningModule->ujians_count }} Ujian</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-350 group-hover/card:text-purple-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>
        </div>

        <!-- Timeline & Meetings Feed Container -->
        <div x-data="{ 
            viewMode: 'meetings', 
            filterType: 'all',
            meetings: {{ json_encode($meetingsTimeline) }},
            activities: {{ json_encode($activities) }},
            get filteredActivities() {
                return this.activities.filter(act => {
                    return this.filterType === 'all' || act.type === this.filterType;
                });
            }
        }" class="bg-white shadow-lg border border-gray-100 rounded-2xl p-4 sm:p-6 mt-6">

            <!-- Section Header & Controls -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2 select-none">
                        <span class="w-3 h-3 rounded-full bg-[#0c2b4d]"></span>
                        Timeline Pembelajaran Per Pertemuan
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-1">Ikuti materi, tugas, dan kuis secara berurutan sesuai alur pertemuan kelas.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                    <!-- Mode Switcher Tabs -->
                    <div class="bg-gray-100 p-1 rounded-xl flex items-center gap-1 border border-gray-200/60">
                        <button @click="viewMode = 'meetings'"
                                :class="viewMode === 'meetings' ? 'bg-white text-[#0c2b4d] font-extrabold shadow-sm' : 'text-gray-600 hover:text-gray-900 font-semibold'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Kartu Pertemuan
                        </button>
                        <button @click="viewMode = 'feed'"
                                :class="viewMode === 'feed' ? 'bg-white text-[#0c2b4d] font-extrabold shadow-sm' : 'text-gray-600 hover:text-gray-900 font-semibold'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Aktivitas Terbaru
                        </button>
                    </div>

                    <!-- Filter Tipe Aktivitas -->
                    <select x-model="filterType" class="bg-gray-50 border border-gray-200 text-gray-750 text-xs rounded-xl px-3 py-1.5 focus:outline-none focus:border-[#0c2b4d] cursor-pointer">
                        <option value="all">Semua Tipe</option>
                        <option value="materi">Materi</option>
                        <option value="tugas">Tugas</option>
                        <option value="kuis">Kuis</option>
                        <option value="ujian">Ujian</option>
                        <option value="absensi">Status Presensi</option>
                    </select>
                </div>
            </div>

            <!-- VIEW 1: Meeting Cards Timeline -->
            <div x-show="viewMode === 'meetings'" class="space-y-6">
                <template x-for="meeting in meetings" :key="meeting.date">
                    <div class="relative pl-6 sm:pl-8 border-l-2 border-dashed border-blue-200">
                        <!-- Node Circle -->
                        <div class="absolute -left-[11px] top-4 w-5 h-5 rounded-full border-2 border-white shadow-md flex items-center justify-center"
                             :class="{
                                 'bg-emerald-500 text-white': meeting.status === 'past',
                                 'bg-blue-600 text-white ring-4 ring-blue-100': meeting.status === 'today',
                                 'bg-gray-300 text-white': meeting.status === 'future'
                             }">
                            <template x-if="meeting.status === 'past'">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </template>
                            <template x-if="meeting.status === 'today'">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            </template>
                        </div>

                        <!-- Card Container -->
                        <div class="bg-white border rounded-2xl shadow-sm hover:shadow-md transition-all overflow-hidden"
                             :class="{
                                 'border-emerald-200': meeting.status === 'past',
                                 'border-blue-300 ring-2 ring-blue-500/10': meeting.status === 'today',
                                 'border-gray-200': meeting.status === 'future'
                             }">
                            <!-- Header Bar -->
                            <div class="px-4 py-3 bg-gray-50/80 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-[#0c2b4d] text-white">
                                        Pertemuan <span x-text="meeting.meeting_number"></span>
                                    </span>
                                    <h4 class="text-xs font-bold text-gray-800" x-text="meeting.date_formatted"></h4>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                      :class="{
                                          'bg-emerald-50 text-emerald-700 border border-emerald-200': meeting.status === 'past',
                                          'bg-blue-50 text-blue-700 border border-blue-200 animate-pulse': meeting.status === 'today',
                                          'bg-gray-100 text-gray-600': meeting.status === 'future'
                                      }"
                                      x-text="meeting.status === 'past' ? 'Selesai' : (meeting.status === 'today' ? 'Hari Ini' : 'Mendatang')">
                                </span>
                            </div>

                            <!-- Items List -->
                            <div class="p-4 space-y-3">
                                <template x-for="item in meeting.items" :key="item.id">
                                    <div x-show="filterType === 'all' || filterType === item.type"
                                         class="p-3 rounded-xl border border-gray-100 hover:border-blue-200 bg-gray-50/40 hover:bg-gray-50 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <!-- Badge Icon -->
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm flex-shrink-0 mt-0.5"
                                                 :class="{
                                                     'bg-indigo-500': item.type === 'materi',
                                                     'bg-rose-500': item.type === 'tugas',
                                                     'bg-amber-500': item.type === 'kuis',
                                                     'bg-purple-500': item.type === 'ujian',
                                                     'bg-emerald-600': item.type === 'absensi'
                                                 }">
                                                <span x-text="item.type.charAt(0).toUpperCase()"></span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h5 class="text-xs font-bold text-gray-800" x-text="item.title"></h5>
                                                    <span class="inline-flex text-[8px] uppercase tracking-wider px-2 py-0.5 rounded font-bold"
                                                          :class="{
                                                              'bg-indigo-50 text-indigo-700 border border-indigo-150': item.type === 'materi',
                                                              'bg-rose-50 text-rose-700 border border-rose-150': item.type === 'tugas',
                                                              'bg-amber-50 text-amber-700 border border-amber-150': item.type === 'kuis',
                                                              'bg-purple-50 text-purple-700 border border-purple-150': item.type === 'ujian',
                                                              'bg-emerald-50 text-emerald-700 border border-emerald-150': item.type === 'absensi'
                                                          }"
                                                          x-text="item.type"></span>
                                                </div>
                                                <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed" x-text="item.description"></p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 flex-shrink-0 self-end sm:self-center">
                                            <template x-if="item.due_date_formatted">
                                                <span class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded-lg border border-red-100">
                                                    Deadline: <span x-text="item.due_date_formatted"></span>
                                                </span>
                                            </template>
                                            <a :href="item.url" class="text-xs font-bold text-[#0c2b4d] hover:underline flex items-center gap-1 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-2xs hover:bg-gray-50">
                                                <span x-text="item.label"></span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="meetings.length === 0" class="text-center py-12 bg-gray-50/50 border border-gray-200/60 rounded-2xl">
                    <h4 class="text-sm font-bold text-gray-700">Belum Ada Aktivitas Pertemuan</h4>
                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Materi dan tugas pembelajaran akan tampil di sini secara kronologis.</p>
                </div>
            </div>

            <!-- VIEW 2: Feed Activity List -->
            <div x-show="viewMode === 'feed'" class="relative pl-6 border-l border-gray-150 space-y-6">
                <template x-for="activity in filteredActivities" :key="activity.id">
                    <div class="relative">
                        <span class="absolute -left-[31px] top-1.5 w-5 h-5 rounded-full flex items-center justify-center text-white text-[9px] font-bold shadow-sm"
                              :class="{
                                  'bg-indigo-500': activity.type === 'materi',
                                  'bg-rose-500': activity.type === 'tugas',
                                  'bg-amber-500': activity.type === 'kuis',
                                  'bg-purple-500': activity.type === 'ujian',
                                  'bg-emerald-600': activity.type === 'absensi'
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
                                              'bg-purple-50 text-purple-700 border border-purple-150': activity.type === 'ujian',
                                              'bg-emerald-50 text-emerald-700 border border-emerald-150': activity.type === 'absensi'
                                          }"
                                          x-text="activity.type"></span>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed whitespace-pre-wrap" x-text="activity.description"></p>
                            </div>

                            <div class="flex flex-col sm:items-end gap-1 text-[10px] text-gray-400 font-medium whitespace-nowrap self-stretch sm:self-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span>Tanggal:</span>
                                    <span class="text-gray-700 font-bold" x-text="activity.created_at_formatted"></span>
                                </div>
                                <template x-if="activity.due_date_formatted">
                                    <div class="flex items-center gap-1 text-red-500 bg-red-50 border border-red-100 px-2 py-0.5 rounded-lg font-bold">
                                        <span>Batas:</span>
                                        <span x-text="activity.due_date_formatted"></span>
                                    </div>
                                </template>
                                <a :href="activity.url" class="text-xs font-bold text-[#0c2b4d] hover:underline flex items-center gap-0.5 mt-1 sm:mt-0 select-none">
                                    <span x-text="activity.label"></span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="filteredActivities.length === 0" class="text-center py-8 text-xs text-gray-450 italic pl-0 -ml-6 border-none select-none">
                    Belum ada aktivitas yang sesuai dengan kriteria filter.
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
