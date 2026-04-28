<x-filament-panels::page>
    <div x-data="{
        selectedIds: @entangle('selected_bills'),
        billAmounts: @js($this->bill_amounts),
        billNames: @js($this->bill_names),
        billDates: @js($this->bill_dates),
        amountReceived: @entangle('data.amount_received'),
        paymentMethod: @entangle('data.payment_method'),
        
        get totalSelected() {
            let total = 0;
            this.selectedIds.forEach(id => {
                if (this.billAmounts[id]) total += parseFloat(this.billAmounts[id]);
            });
            return total;
        },

        get allocations() {
            if (this.selectedIds.length === 0) return [];
            
            // 1. Determine base amount to distribute
            // If Midtrans, it's always the total. If Cash, it's what's entered.
            let balance = (this.paymentMethod === 'midtrans') ? this.totalSelected : (parseFloat(this.amountReceived) || 0);
            
            // 2. Sort selected IDs by due date (oldest first)
            let sortedIds = [...this.selectedIds].sort((a, b) => {
                return (this.billDates[a] || '').localeCompare(this.billDates[b] || '');
            });

            // 3. Waterfall distribution
            return sortedIds.map(id => {
                let amount = parseFloat(this.billAmounts[id]);
                let allocated = Math.min(balance, amount);
                balance -= allocated;

                let status = 'ANTRE';
                if (allocated >= amount) status = 'LUNAS';
                else if (allocated > 0) status = 'CICIL';

                return {
                    id: id,
                    name: this.billNames[id],
                    total: amount,
                    allocated: allocated,
                    shortage: amount - allocated,
                    status: status
                };
            });
        },

        get change() {
            if (!this.amountReceived || this.selectedIds.length === 0) return 0;
            return parseFloat(this.amountReceived) - this.totalSelected;
        },

        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        toggleBill(id) {
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
        },

        toggleGroup(ids) {
            const allInGroup = ids.every(id => this.selectedIds.includes(id));
            if (allInGroup) {
                this.selectedIds = this.selectedIds.filter(id => !ids.includes(id));
            } else {
                this.selectedIds = [...new Set([...this.selectedIds, ...ids])];
            }
        },

        toggleAll(allIds) {
            if (this.selectedIds.length === allIds.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [...allIds];
            }
        }
    }">
    {{-- Student Biodata Header --}}
    <div class="mb-6">
        {{ $this->studentInfolist }}
    </div>

    {{-- POS Split Layout (handled by Flex component) --}}
    <form wire:submit="processPayment">
        {{ $this->form }}
    </form>

    {{-- Ultra-Minimalist Midtrans Teleport with Bulletproof Absolute Centering --}}
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
                 style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 999999; background: transparent;"
                 x-on:keydown.escape.window="open = false"
            >
                {{-- Transparent blocking backdrop --}}
                <div x-on:click.stop style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: default;"></div>

                {{-- Absolute Centered Card --}}
                <div class="animate-in zoom-in duration-300" 
                     style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 550px; background: white; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 25px 70px -15px rgba(0,0,0,0.5); border: 1px solid rgba(0,0,0,0.1);"
                >
                    {{-- Minimalist Header/Action --}}
                    <div style="position: absolute; right: 1rem; top: 1rem; z-index: 10;">
                        <button type="button" x-on:click="open = false" style="border-radius: 9999px; background: rgba(0,0,0,0.05); padding: 0.5rem; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #666;">
                            <x-heroicon-o-x-mark style="width: 1.25rem; height: 1.25rem;" />
                        </button>
                    </div>

                    <div id="snap-container" style="min-height: 600px; width: 100%; background: white;">
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
