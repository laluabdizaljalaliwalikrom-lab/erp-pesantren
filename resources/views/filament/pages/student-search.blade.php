<x-filament-panels::page>
    {{-- Search Header --}}
    <div class="relative max-w-2xl mx-auto mb-8">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <x-filament::icon icon="heroicon-o-magnifying-glass" class="h-6 w-6 text-gray-400" />
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="searchQuery" 
            placeholder="Ketik Nama atau NIS santri..." 
            class="block w-full pl-12 pr-12 py-4 text-lg border-gray-300 rounded-xl shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white transition-all duration-200"
            style="height: 3.5rem; font-size: 1.2rem;"
            autocomplete="off"
        >
        
        <div wire:loading.delay wire:target="searchQuery" class="absolute inset-y-0 right-0 pr-4 flex items-center">
            <x-filament::loading-indicator class="h-5 w-5 text-primary-500" />
        </div>
    </div>

    {{-- Search Results Grid --}}
    @if(blank($searchQuery))
        {{-- Empty State (No query) --}}
        <div class="flex flex-col items-center justify-center p-8 mt-12 text-center text-gray-500 dark:text-gray-400">
            <div class="p-5 bg-gray-100 dark:bg-gray-800 rounded-full mb-4 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-filament::icon icon="heroicon-o-magnifying-glass-circle" class="h-16 w-16 text-gray-400 dark:text-gray-500" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Terminal Siap</h3>
            <p class="mt-2">Ketik nama atau NIS santri untuk memulai pencarian realtime.</p>
        </div>
    @elseif($this->students->isEmpty())
        {{-- Empty State (No results) --}}
        <div class="flex flex-col items-center justify-center p-8 mt-12 text-center text-gray-500 dark:text-gray-400">
            <div class="p-5 bg-gray-100 dark:bg-gray-800 rounded-full mb-4 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-filament::icon icon="heroicon-o-x-circle" class="h-16 w-16 text-gray-400 dark:text-gray-500" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Santri Tidak Ditemukan</h3>
            <p class="mt-2">Tidak ada santri yang cocok dengan pencarian "<strong>{{ $searchQuery }}</strong>".</p>
        </div>
    @else
        {{-- Results Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->students as $student)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-white/10 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col group/card">
                    <div class="p-5 flex-grow">
                        <div class="flex items-center gap-4 mb-4">
                            {{-- Avatar Placeholder --}}
                            <div class="flex-shrink-0 h-14 w-14 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center border border-primary-200 dark:border-primary-800/50 shadow-inner group-hover/card:scale-105 transition-transform">
                                <span class="text-primary-700 dark:text-primary-400 font-bold text-xl">
                                    {{ (string) str($student->full_name)->substr(0, 2)->upper() }}
                                </span>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white leading-tight">
                                    {{ $student->full_name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-mono bg-gray-100 dark:bg-white/5 inline-block px-1.5 py-0.5 rounded">
                                    NIS: {{ $student->nis ?? '-' }}
                                </p>
                            </div>
                        </div>
                        
                        {{-- Class Badges --}}
                        <div class="flex flex-wrap gap-2 mt-4">
                            @forelse($student->academicRecords as $record)
                                <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-white/5 px-2 py-1 text-[11px] font-bold text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10 dark:ring-white/10 uppercase tracking-wide">
                                    <x-filament::icon icon="heroicon-m-academic-cap" class="h-3 w-3 mr-1 opacity-50" />
                                    {{ $record->schoolClass?->name ?? 'Tanpa Kelas' }}
                                </span>
                            @empty
                                <span class="inline-flex items-center rounded-md bg-orange-50 dark:bg-orange-400/10 px-2 py-1 text-[11px] font-bold text-orange-600 dark:text-orange-400 ring-1 ring-inset ring-orange-500/20 uppercase tracking-wide">
                                    Belum Masuk Kelas
                                </span>
                            @endforelse
                        </div>
                    </div>
                    
                    {{-- Action Footer --}}
                    <div class="px-5 py-4 bg-gray-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-white/5">
                        <button 
                            wire:click="openFinance('{{ $student->id }}')"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-lg transition-all focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 shadow-sm hover:shadow-md disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:bg-primary-600 group"
                        >
                            <span wire:loading.remove wire:target="openFinance('{{ $student->id }}')">
                                <x-filament::icon icon="heroicon-o-credit-card" class="h-5 w-5" />
                            </span>
                            <span wire:loading wire:target="openFinance('{{ $student->id }}')">
                                <x-filament::loading-indicator class="h-5 w-5" />
                            </span>
                            Buka Pembayaran
                            <span class="opacity-0 group-hover:opacity-100 transition-opacity ml-1">&rarr;</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Final Bulletproof Centered Loading Overlay --}}
    <div 
        wire:loading.flex 
        wire:target="openFinance" 
        class="fixed inset-0 z-[99999] flex items-center justify-center overflow-hidden"
        style="display: none; background-color: rgba(0, 0, 0, 0.7); backdrop-filter: blur(12px); position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;"
    >
        <div class="relative p-8 bg-white dark:bg-gray-900 rounded-[2rem] shadow-2xl max-w-sm w-full mx-4 border border-gray-200 dark:border-white/10 flex flex-col items-center justify-center animate-in zoom-in duration-300 overflow-hidden">
            {{-- Decorative Gradient Background --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 to-primary-600"></div>

            <div class="mb-8 mt-4">
                <div class="h-20 w-20 text-primary-600 bg-primary-50 dark:bg-primary-900/20 p-5 rounded-2xl ring-1 ring-primary-500/20 flex items-center justify-center relative">
                    <x-filament::loading-indicator class="h-10 w-10" />
                    {{-- Subtle Inner Pulse --}}
                    <div class="absolute inset-0 rounded-2xl border-2 border-primary-500/30 animate-pulse"></div>
                </div>
            </div>
            
            <h2 class="text-2xl font-black text-gray-900 dark:text-white text-center mb-2 tracking-tight">
                Memproses Data
            </h2>
            
            <div class="flex items-center gap-2 mb-6">
                <div class="h-1.5 w-1.5 bg-primary-500 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                <div class="h-1.5 w-1.5 bg-primary-500 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                <div class="h-1.5 w-1.5 bg-primary-500 rounded-full animate-bounce"></div>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400 text-center font-medium px-4 leading-relaxed">
                Mohon tunggu sejenak, sedang menyiapkan terminal pembayaran santri...
            </p>
        </div>
    </div>
</x-filament-panels::page>
