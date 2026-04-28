@php
    $payments = $this->getPaymentHistory();
@endphp

<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10 shadow-sm transition-all">
    <table class="w-full text-left text-[13px] leading-relaxed">
        <thead>
            <tr class="bg-gray-50/80 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                <th class="px-4 py-2.5 font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-[11px]">Tanggal</th>
                <th class="px-4 py-2.5 font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-[11px]">Keterangan</th>
                <th class="px-4 py-2.5 font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-[11px]">Metode</th>
                <th class="px-4 py-2.5 font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-[11px] text-right">Nominal</th>
                <th class="px-4 py-2.5 font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-[11px] text-center">Status</th>
                <th class="px-4 py-2.5 font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-[11px] text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
            @forelse($payments as $payment)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 tabular-nums">
                        {{ $payment->created_at->format('d/m/Y') }}
                        <span class="block text-[10px] opacity-70">{{ $payment->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-tight">
                            {{ $payment->bill?->fee?->name ?? 'Tagihan' }}
                        </span>
                        @if($payment->bill?->period_name)
                            <span class="block text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                                {{ $payment->bill->period_name }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($payment->method === 'cash')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/20 text-[11px] font-bold uppercase tracking-wider">
                                <x-filament::icon icon="heroicon-m-banknotes" class="h-3 w-3" />
                                Tunai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-400 ring-1 ring-inset ring-blue-600/20 text-[11px] font-bold uppercase tracking-wider">
                                <x-filament::icon icon="heroicon-m-building-library" class="h-3 w-3" />
                                Bank
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-gray-200 tabular-nums">
                        Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($payment->status === \App\Enums\PaymentStatus::CONFIRMED)
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-emerald-500/20 text-emerald-500 ring-1 ring-emerald-500/30">
                                <x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5" />
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-orange-500/20 text-orange-500 ring-1 ring-orange-500/30 font-bold text-[10px]">
                                ?
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($payment->transaction_id)
                            <button 
                                type="button"
                                wire:click="$refresh" {{-- Fallback for reactivity if needed --}}
                                onclick="window.open('{{ route('receipt.batch.print', ['transactionId' => $payment->transaction_id]) }}', '_blank')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10 hover:border-primary-500 dark:hover:border-primary-500/50 hover:text-primary-600 dark:hover:text-primary-400 transition-all text-[11px] font-bold uppercase tracking-wider group-hover:shadow-sm"
                            >
                                <x-filament::icon icon="heroicon-m-printer" class="h-3.5 w-3.5" />
                                Cetak
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 italic">
                        Belum ada riwayat pembayaran untuk santri ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
