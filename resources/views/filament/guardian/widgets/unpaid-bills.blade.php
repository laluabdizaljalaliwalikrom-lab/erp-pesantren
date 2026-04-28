@php
    $stats = $this->getStats();
@endphp

<div class="modern-card" style="padding: 2rem; display: flex; flex-direction: column; min-height: 100%;">
    <div class="flex-row-center gap-4" style="margin-bottom: 2rem;">
        <div style="width: 45px; height: 45px; background: #fff1f2; border-radius: 1rem; display: flex; align-items: center; justify-content: center; border: 1px solid #fecdd3;">
            <x-heroicon-o-banknotes class="guardian-icon-fix" style="color: #e11d48 !important;" />
        </div>
        <div>
            <h3 style="font-size: 16px; font-weight: 900; color: var(--text-main); margin: 0; line-height: 1.2;">Keuangan & Tagihan</h3>
            <p style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin: 0;">Portal Administrasi</p>
        </div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; gap: 1.5rem;">
        {{-- Total Outstanding Card --}}
        <div class="{{ $stats['is_clear'] ? 'vibrant-gradient' : 'rose-gradient' }}" style="padding: 1.5rem 1.75rem; border-radius: 1.75rem; box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.2);">
            <div class="flex-row-center" style="justify-content: space-between; margin-bottom: 8px;">
                <p style="font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.8; margin: 0; color: white !important;">Sisa Tagihan Belum Bayar</p>
                @if($stats['is_clear'])
                    <x-heroicon-s-check-circle class="guardian-icon-fix" style="color: white !important;" />
                @endif
            </div>
            
            <p class="text-responsive-lg" style="font-weight: 900; letter-spacing: -0.03em; margin: 0 0 12px 0; color: white !important; font-size: clamp(1.25rem, 6.5vw, 1.75rem) !important;">
                Rp {{ number_format($stats['total_remaining'], 0, ',', '.') }}
            </p>
            
            <div style="height: 6px; width: 100%; background: rgba(0,0,0,0.1); border-radius: 999px; overflow: hidden;">
                @php $percent = $stats['total_paid'] > 0 ? min(100, ($stats['total_paid'] / ($stats['total_paid'] + $stats['total_remaining'])) * 100) : 0; @endphp
                <div style="height: 100%; border-radius: 999px; background: white; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); width: {{ $percent }}% !important;"></div>
            </div>
            <p style="font-size: 10px; font-weight: 700; margin-top: 8px; opacity: 0.8; text-transform: uppercase; color: white !important;">
                {{ $stats['is_clear'] ? 'Syukron, semua sudah lunas' : 'Status: ' . round($percent) . '% Terbayar' }}
            </p>
        </div>

        {{-- Mini Stats --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div style="padding: 1rem; border-radius: 1.25rem; background: var(--subcard-bg); border: 1px solid var(--card-border);">
                <p style="font-size: 9px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Terbayar</p>
                <p class="text-responsive-base" style="font-weight: 900; color: var(--text-main); margin: 0; font-size: clamp(0.7rem, 2.5vw, 0.85rem) !important;">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</p>
            </div>
            <div style="padding: 1rem; border-radius: 1.25rem; background: var(--subcard-bg); border: 1px solid var(--card-border);">
                <p style="font-size: 9px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Item</p>
                <p class="text-responsive-base" style="font-weight: 900; color: var(--text-main); margin: 0;">{{ $stats['bills_count'] }} Tagihan</p>
            </div>
        </div>
    </div>
</div>
