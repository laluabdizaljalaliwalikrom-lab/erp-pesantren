@php
    /** @var \Illuminate\Support\Collection $groupedBills */
    $groupedBills = $this->getGroupedBills();
    $allBillIds = $groupedBills->flatten()->pluck('id')->toArray();
@endphp

<div class="space-y-4">
    {{-- Global Select All --}}
    <div class="flex items-center justify-between px-4 py-2.5 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
        <label class="flex items-center gap-3 cursor-pointer group">
            <input
                type="checkbox"
                x-bind:checked="selectedIds.length === {{ count($allBillIds) }} && {{ count($allBillIds) }} > 0"
                @click="toggleAll({{ json_encode($allBillIds) }})"
                class="fi-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm outline-none transition duration-75
                       focus:ring-2 focus:ring-primary-600 dark:border-white/10 dark:bg-white/5
                       dark:checked:bg-primary-500 dark:focus:ring-primary-500 h-5 w-5"
            />
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                Pilih Semua Tagihan
            </span>
        </label>
        <span class="text-xs font-medium text-gray-400 dark:text-gray-500 tabular-nums">
            <span x-text="selectedIds.length"></span> / {{ count($allBillIds) }} dipilih
        </span>
    </div>

    {{-- Grouped Tables --}}
    @forelse ($groupedBills as $feeName => $bills)
        @php
            $groupBillIds = $bills->pluck('id')->toArray();
            $groupTotal = $bills->sum(fn ($b) => $b->remaining_balance);
        @endphp

        <div class="rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm" wire:key="group-{{ \Illuminate\Support\Str::slug($feeName) }}">
            {{-- Group Header --}}
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-gradient-to-r from-primary-50 to-primary-50/50 dark:from-primary-950/40 dark:to-primary-900/20 border-b border-gray-200 dark:border-white/10">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input
                        type="checkbox"
                        x-bind:checked="{{ json_encode($groupBillIds) }}.every(id => selectedIds.includes(id))"
                        @click="toggleGroup({{ json_encode($groupBillIds) }})"
                        class="fi-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm outline-none transition duration-75
                               focus:ring-2 focus:ring-primary-600 dark:border-white/10 dark:bg-white/5
                               dark:checked:bg-primary-500 dark:focus:ring-primary-500 h-5 w-5"
                    />
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-m-banknotes" class="h-5 w-5 text-primary-500 dark:text-primary-400" />
                        <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $feeName }}</span>
                    </div>
                </label>
                <span class="text-xs font-semibold text-primary-700 dark:text-primary-300 bg-primary-100 dark:bg-primary-800/50 px-2.5 py-1 rounded-full tabular-nums">
                    Rp {{ number_format($groupTotal, 0, ',', '.') }}
                </span>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/5">
                            <th class="w-12 px-4 py-2.5"></th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Periode</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Jumlah Asli</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sisa Tunggakan</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        @foreach ($bills->sortBy('due_date') as $bill)
                            @php
                                $remaining = (float) $bill->remaining_balance;
                                $isPartial = $bill->status === \App\Enums\BillStatus::PARTIAL;
                            @endphp
                            <tr
                                wire:key="bill-{{ $bill->id }}"
                                class="transition-colors duration-150 cursor-pointer"
                                :class="selectedIds.includes('{{ $bill->id }}') ? 'bg-primary-50/70 dark:bg-primary-900/20' : 'hover:bg-gray-50 dark:hover:bg-white/[0.02] even:bg-gray-50/50 dark:even:bg-white/[0.01]'"
                                @click="toggleBill('{{ $bill->id }}')"
                            >
                                {{-- Checkbox --}}
                                <td class="px-4 py-3 text-center">
                                    <input
                                        type="checkbox"
                                        x-bind:checked="selectedIds.includes('{{ $bill->id }}')"
                                        @click.stop="toggleBill('{{ $bill->id }}')"
                                        class="fi-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm outline-none transition duration-75
                                               focus:ring-2 focus:ring-primary-600 dark:border-white/10 dark:bg-white/5
                                               dark:checked:bg-primary-500 dark:focus:ring-primary-500 h-4 w-4"
                                    />
                                </td>

                                {{-- Periode --}}
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 font-medium">
                                    {{ $bill->period_name ?? '-' }}
                                </td>

                                {{-- Jumlah Asli --}}
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 tabular-nums">
                                    Rp {{ number_format((float) $bill->final_amount, 0, ',', '.') }}
                                </td>

                                {{-- Sisa Tunggakan --}}
                                <td class="px-4 py-3 text-right font-semibold tabular-nums {{ $isPartial ? 'text-warning-600 dark:text-warning-400' : 'text-danger-600 dark:text-danger-400' }}">
                                    Rp {{ number_format($remaining, 0, ',', '.') }}
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 text-center">
                                    @if ($isPartial)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wider bg-warning-50 text-warning-700 ring-1 ring-inset ring-warning-600/20 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30">
                                            <span class="h-1.5 w-1.5 rounded-full bg-warning-500"></span>
                                            Cicilan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wider bg-danger-50 text-danger-700 ring-1 ring-inset ring-danger-600/20 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30">
                                            <span class="h-1.5 w-1.5 rounded-full bg-danger-500"></span>
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-12 rounded-xl border-2 border-dashed border-gray-200 dark:border-white/10">
            <x-filament::icon icon="heroicon-o-check-badge" class="h-12 w-12 text-success-400 mb-3" />
            <p class="text-base font-semibold text-gray-600 dark:text-gray-300">Tidak Ada Tunggakan 🎉</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Semua tagihan santri ini telah lunas.</p>
        </div>
    @endforelse
</div>iv>
