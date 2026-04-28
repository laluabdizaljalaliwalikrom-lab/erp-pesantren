<div class="space-y-6" style="font-family: inherit; width: 100%; max-width: 400px; margin-left: auto; margin-right: auto;">
    {{-- Total Display - Premium Card --}}
    <div style="padding: 1.25rem; border-radius: 1rem; background: linear-gradient(135deg, rgba(var(--primary-600), 0.15) 0%, rgba(var(--primary-600), 0.05) 100%); border: 1px solid rgba(var(--primary-500), 0.3); box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);" class="dark:bg-primary-950/20">
        <p style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; color: rgba(var(--primary-600), 1); margin-bottom: 0.5rem;" class="dark:text-primary-400">Total Terpilih</p>
        <div style="display: flex; align-items: baseline; gap: 0.25rem; flex-wrap: wrap;">
            <span style="font-weight: 900; letter-spacing: -0.02em; color: rgba(var(--primary-700), 1);" class="text-3xl md:text-4xl dark:text-primary-300 tabular-nums break-all" x-text="formatRupiah(totalSelected)"></span>
        </div>
    </div>

    {{-- Inputs Section --}}
    <div class="space-y-5">
        {{-- Uang Diterima (Only for Cash) --}}
        <div x-show="paymentMethod === 'cash' || !paymentMethod" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2">
            <label class="block text-[0.7rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-300 mb-2.5">Uang Diterima (Cash)</label>
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 flex items-center pointer-events-none" style="left: 1.75rem;">
                    <span class="text-xl font-extrabold text-gray-400 dark:text-gray-500">Rp</span>
                </div>
                <input 
                    type="number" 
                    x-model.number="amountReceived"
                    @wheel.prevent
                    placeholder="0"
                    style="display: block; width: 100%; padding: 1.25rem 1rem 1.25rem 4.5rem; font-size: 1.75rem; font-weight: 900; border-radius: 1.25rem; transition: all 0.2s;"
                    class="bg-white border-2 border-gray-200 text-gray-900 dark:bg-white/5 dark:border-white/10 dark:text-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none tabular-nums shadow-sm"
                >
            </div>
        </div>

        {{-- Payment Method - Modern Toggle --}}
        <div class="pt-4 border-t border-gray-100 dark:border-white/5">
            <label class="block text-[0.7rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-300 mb-2.5">Metode Pembayaran</label>
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem;">
                <button 
                    type="button"
                    @click="paymentMethod = 'cash'"
                    :style="paymentMethod === 'cash' ? 'border-color: rgba(var(--primary-500), 1); background: rgba(var(--primary-500), 0.1);' : ''"
                    class="flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="paymentMethod === 'cash' ? 'dark:bg-primary-900/30' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10'"
                >
                    <span style="font-size: 1.75rem; margin-bottom: 0.25rem;">💵</span>
                    <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;" :class="paymentMethod === 'cash' ? 'text-primary-700 dark:text-primary-300' : 'text-gray-500 dark:text-gray-400'">Tunai</span>
                </button>
                <button 
                    type="button"
                    @click="paymentMethod = 'midtrans'"
                    :style="paymentMethod === 'midtrans' ? 'border-color: rgba(var(--primary-500), 1); background: rgba(var(--primary-500), 0.1);' : ''"
                    class="flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="paymentMethod === 'midtrans' ? 'dark:bg-primary-900/30' : 'bg-gray-50 border-gray-200 dark:bg-white/5 dark:border-white/10'"
                >
                    <span style="font-size: 1.75rem; margin-bottom: 0.25rem;">📱</span>
                    <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;" :class="paymentMethod === 'midtrans' ? 'text-primary-700 dark:text-primary-300' : 'text-gray-500 dark:text-gray-400'">Online</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Change Display - High Contrast (Only for Cash) --}}
    <template x-if="paymentMethod === 'cash' || !paymentMethod">
        <div style="padding: 1.25rem; border-radius: 1.25rem;" class="border-2 border-dashed border-gray-300 bg-gray-50/50 dark:border-white/20 dark:bg-white/[0.03]">
            <p class="text-[0.65rem] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1.5">Kembalian</p>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span 
                    style="font-size: 1.85rem; font-weight: 950;"
                    class="tabular-nums transition-colors"
                    :class="change >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'"
                    x-text="change >= 0 ? '✅ ' + formatRupiah(change) : '⚠️ Kurang ' + formatRupiah(Math.abs(change))"
                ></span>
            </div>
        </div>
    </template>

    {{-- Waterfall Allocation Summary - Downtree Fall --}}
    <div x-show="selectedIds.length > 0 && (amountReceived > 0 || paymentMethod === 'midtrans')" x-transition style="padding: 1.25rem; border-radius: 1.25rem;" class="bg-gray-50 border border-gray-200 dark:bg-white/[0.02] dark:border-white/10">
        <p class="text-[0.7rem] font-black uppercase tracking-widest text-gray-500 dark:text-gray-100 mb-3 border-b border-gray-200 dark:border-white/10 pb-2">Rencana Alokasi Pembayaran</p>
        <div class="space-y-3">
            <template x-for="item in allocations" :key="item.id">
                <div class="space-y-1">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span class="text-gray-700 dark:text-gray-100 font-semibold truncate max-w-[150px]" x-text="item.name"></span>
                        
                        <div class="flex items-center gap-2">
                            <span x-show="item.status === 'LUNAS'" class="inline-flex items-center rounded-md bg-success-50 px-1.5 py-0.5 text-[10px] font-bold text-success-700 ring-1 ring-inset ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">LUNAS</span>
                            <span x-show="item.status === 'CICIL'" class="inline-flex items-center rounded-md bg-warning-50 px-1.5 py-0.5 text-[10px] font-bold text-warning-700 ring-1 ring-inset ring-warning-600/20 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30">CICIL</span>
                            <span x-show="item.status === 'ANTRE'" class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-[10px] font-bold text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-white/5 dark:text-gray-400 dark:ring-white/10">ANTRE</span>
                            
                            <span style="font-weight: 800;" class="text-primary-600 dark:text-primary-400 tabular-nums font-mono" x-text="formatRupiah(item.allocated)"></span>
                        </div>
                    </div>
                    {{-- Sub-info for partials --}}
                    <div x-show="item.status === 'CICIL'" class="flex justify-end pr-0.5">
                        <span class="text-[9px] font-semibold text-gray-400 italic">Terbayar sebagian, sisa: <span x-text="formatRupiah(item.shortage)"></span></span>
                    </div>
                </div>
            </template>
            
            <div class="mt-4 pt-3 border-t-2 border-gray-200 dark:border-white/10 flex justify-between items-center text-gray-500 dark:text-gray-400">
                <span class="text-[0.7rem] font-black uppercase">Total {{ $this->student->full_name }}</span>
                <span class="text-base font-black text-primary-700 dark:text-primary-400 tabular-nums" x-text="formatRupiah(totalSelected)"></span>
            </div>
        </div>
    </div>
</div>

