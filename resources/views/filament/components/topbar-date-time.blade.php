<div class="flex items-center gap-x-1 sm:gap-x-2 md:gap-x-3 px-2 sm:px-3 py-1 lg:py-1.5 bg-gray-50/10 ring-1 ring-gray-200 rounded-lg shadow-sm dark:bg-white/5 dark:ring-white/10 transition-all hover:bg-gray-100 dark:hover:bg-white/10 group"
     x-data="{ 
        time: '', 
        timeShort: '',
        date: '',
        dateShort: '',
        update() {
            const now = new Date();
            this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            this.timeShort = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
            this.date = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            this.dateShort = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        } 
     }" 
     x-init="update(); setInterval(() => update(), 1000)">
    
    <style>
        @media (max-width: 639px) { /* sm breakpoint */
            .dt-full { display: none !important; }
            .dt-icon { display: none !important; }
            .dt-sep { margin-left: 0.15rem; margin-right: 0.15rem; }
        }
        @media (min-width: 640px) {
            .dt-short { display: none !important; }
            .dt-sep { margin-left: 0.5rem; margin-right: 0.5rem; }
        }
    </style>

    {{-- Clock --}}
    <div class="flex items-center gap-x-1.5 text-primary-600 dark:text-primary-400 font-bold font-mono text-[10px] sm:text-xs md:text-sm tracking-tight lg:tracking-widest whitespace-nowrap">
        <x-heroicon-m-clock class="dt-icon w-3.5 h-3.5 sm:w-4 sm:h-4 opacity-80" />
        <span x-text="time" class="dt-full"></span>
        <span x-text="timeShort" class="dt-short"></span>
    </div>

    {{-- Minimalist Divider --}}
    <div class="dt-sep h-3 sm:h-4 w-px bg-gray-300 dark:bg-gray-700 opacity-60"></div>

    {{-- Date --}}
    <div class="flex items-center gap-x-1.5 text-[9px] sm:text-[10px] md:text-xs font-normal text-gray-400 dark:text-gray-500 tracking-tighter sm:tracking-wider whitespace-nowrap">
        <x-heroicon-m-calendar class="dt-icon w-3.5 h-3.5 opacity-40" />
        <span x-text="date" class="dt-full"></span>
        <span x-text="dateShort" class="dt-short"></span>
    </div>
</div>
