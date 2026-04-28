<x-filament-panels::page>
    <div 
        x-data="{ 
            selectedIds: @entangle('selectedBillIds'),
            balances: {{ json_encode($this->bills->pluck('remaining_balance', 'id')->toArray()) }},
            getTotal() {
                let total = 0;
                this.selectedIds.forEach(id => {
                    if (this.balances[id]) total += parseFloat(this.balances[id]);
                });
                return total;
            },
            formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(amount);
            },
            toggle(id) {
                if (this.selectedIds.includes(id)) {
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                } else {
                    this.selectedIds.push(id);
                }
            }
        }"
        class="space-y-8"
    >
        {{-- Student Tabs Header --}}
        <div class="flex items-center gap-2 p-1 bg-gray-100 dark:bg-gray-900 rounded-2xl w-max max-w-full overflow-x-auto no-scrollbar">
            @foreach($students as $student)
                <button 
                    wire:click="setActiveStudent('{{ $student->id }}')"
                    wire:loading.attr="disabled"
                    wire:key="student-tab-{{ $student->id }}"
                    @class([
                        'flex items-center gap-3 px-5 py-3 rounded-xl transition-all duration-300 whitespace-nowrap relative',
                        'bg-white dark:bg-gray-800 shadow-sm shadow-primary-500/10 text-primary-600 font-bold' => $activeStudentId === (string) $student->id,
                        'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-semibold' => $activeStudentId !== (string) $student->id,
                    ])
                >
                    <div @class([
                        'w-8 h-8 rounded-lg flex items-center justify-center shrink-0',
                        'bg-primary-50 text-primary-600' => $activeStudentId === (string) $student->id,
                        'bg-gray-200 dark:bg-gray-700 text-gray-500' => $activeStudentId !== (string) $student->id,
                    ])>
                        <x-heroicon-s-user class="w-5 h-5" />
                    </div>
                    <div class="text-left">
                        <div class="text-sm leading-tight">{{ $student->full_name }}</div>
                        <div class="text-[10px] opacity-60 font-medium uppercase tracking-wider">{{ $student->nis }}</div>
                    </div>
                    <div wire:loading wire:target="setActiveStudent('{{ $student->id }}')" class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 rounded-xl flex items-center justify-center">
                        <x-filament::loading-indicator class="w-5 h-5" />
                    </div>
                </button>
            @endforeach
        </div>

        {{-- Bills Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 pb-24" wire:loading.class="opacity-50">
            @forelse($this->bills as $bill)
                <div 
                    x-on:click="toggle('{{ $bill->id }}')"
                    wire:key="bill-card-{{ $bill->id }}"
                    :class="selectedIds.includes('{{ $bill->id }}') ? 'ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-gray-900 bg-primary-50/30 dark:bg-primary-500/5' : 'bg-white dark:bg-gray-900'"
                    class="modern-card p-6 cursor-pointer group transition-all duration-300"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-1 flex-1">
                            <h4 class="text-lg font-black text-gray-900 dark:text-gray-100 group-hover:text-primary-600 transition-colors uppercase tracking-tight">
                                {{ $bill->fee->name }}
                            </h4>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest opacity-70">
                                {{ $bill->period_name ?? 'Sekali Bayar' }}
                            </p>
                        </div>
                        
                        {{-- Custom Checkbox --}}
                        <div 
                            :class="selectedIds.includes('{{ $bill->id }}') ? 'bg-primary-500 border-primary-500 text-white' : 'border-gray-300 dark:border-gray-600 bg-transparent'"
                            class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300 shrink-0"
                        >
                            <template x-if="selectedIds.includes('{{ $bill->id }}')">
                                <x-heroicon-s-check class="w-4 h-4" />
                            </template>
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-end justify-between">
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Sisa Tagihan</span>
                                <div class="text-2xl font-black text-primary-600 leading-none tracking-tight">
                                    Rp {{ number_format($bill->remaining_balance, 0, ',', '.') }}
                                </div>
                            </div>
                            
                            <x-filament::badge :color="'danger'" size="sm" class="rounded-full bg-danger-50 text-danger-600 dark:bg-danger-500/10 shadow-none border-none capitalize font-bold">
                                {{ $bill->status->getLabel() }}
                            </x-filament::badge>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-o-clock class="w-4 h-4" />
                                Tempo: {{ $bill->due_date->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 flex flex-col items-center justify-center text-center space-y-4 opacity-40">
                    <x-heroicon-o-check-circle class="w-16 h-16 text-primary-500" />
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 italic">Alhamdulillah!</h3>
                        <p class="text-sm font-medium text-gray-500">Semua tagihan untuk santri ini sudah diselesaikan.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Floating Action Bar --}}
        <div 
            x-show="selectedIds.length > 0"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-full opacity-0"
            class="fixed bottom-8 left-1/2 -translate-x-1/2 w-full max-w-2xl px-4 z-[9990]"
            style="display: none;"
        >
            <div class="bg-gray-900/90 dark:bg-gray-800/95 backdrop-blur-xl border border-white/10 p-4 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center justify-between gap-6">
                <div class="pl-2 space-y-0.5">
                    <p class="text-[10px] font-black uppercase tracking-widest text-primary-400">
                        <span x-text="selectedIds.length"></span> Tagihan Dipilih
                    </p>
                    <h3 class="text-xl font-black text-white">
                        Total: Rp <span x-text="formatRupiah(getTotal())"></span>
                    </h3>
                </div>

                <x-filament::button 
                    wire:click="checkout"
                    wire:loading.attr="disabled"
                    color="primary"
                    size="xl"
                    icon="heroicon-s-credit-card"
                    class="rounded-2xl !py-4 shadow-xl shadow-primary-500/20 font-black tracking-tight"
                >
                    <span wire:loading.remove wire:target="checkout">Bayar Sekaligus</span>
                    <span wire:loading wire:target="checkout">Memproses...</span>
                </x-filament::button>
            </div>
        </div>
    </div>
    
    {{-- Ultra-Minimalist Midtrans Teleport --}}
    <div x-data="{ open: false, token: '' }" 
         x-on:open-midtrans-snap.window="open = true; token = $event.detail.snapToken; $nextTick(() => { 
            window.snap.embed(token, {
                embedId: 'snap-container',
                onSuccess: function(result) { window.location.reload(); },
                onPending: function(result) { window.location.reload(); },
                onError: function(result) { alert('Gagal!'); open = false; },
                onClose: function() { open = false; }
            });
         })"
    >
        <template x-teleport="body">
            <div x-show="open" 
                 class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
                 style="background: rgba(0,0,0,0);"
                 x-on:keydown.escape.window="open = false"
            >
                {{-- Transparent blocking backdrop --}}
                <div class="fixed inset-0 cursor-default" aria-hidden="true" x-on:click.stop></div>

                <div class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white dark:bg-gray-900 shadow-[0_25px_70px_-15px_rgba(0,0,0,0.4)] animate-in zoom-in duration-300 ring-1 ring-gray-200 dark:ring-gray-800">
                    {{-- Minimalist Header/Action --}}
                    <div class="absolute right-4 top-4 z-10">
                        <button type="button" x-on:click="open = false" class="rounded-full bg-gray-100/50 p-1.5 text-gray-500 hover:bg-gray-200 dark:bg-gray-800/50 dark:text-gray-400 dark:hover:bg-gray-700 transition-all">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>

                    <div id="snap-container" class="min-h-[500px]">
                        {{-- Midtrans Here --}}
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script type="text/javascript"
                src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
                data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endpush
</x-filament-panels::page>
