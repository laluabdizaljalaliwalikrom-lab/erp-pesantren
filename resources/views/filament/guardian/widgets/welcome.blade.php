@php
    $guardian = auth('guardian')->user();
    $phone = settings()->phone ?? null;
    $waNumber = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;
    $hour = (int) now()->format('H');
    $greeting = match(true) {
        $hour < 11 => 'Pagi',
        $hour < 15 => 'Siang',
        $hour < 18 => 'Sore',
        default    => 'Malam',
    };
@endphp

<div class="modern-card vibrant-gradient" style="padding: 2.5rem !important;">
    {{-- Decorative Orbs --}}
    <div style="position: absolute; top: -50px; right: -50px; width: 250px; height: 250px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(60px); pointer-events: none;"></div>
    
    <div class="flex-row-center gap-6" style="justify-content: space-between !important; flex-wrap: wrap !important;">
        <div class="flex-row-center gap-6">
            <div style="width: 70px; height: 70px; background: var(--glass-bg); border-radius: 1.5rem; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center;">
                <x-heroicon-o-sparkles class="guardian-icon-lg-fix" style="color: white !important;" />
            </div>
            
            <div class="flex-col-start">
                <p style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.2em; opacity: 0.8; margin-bottom: 4px; color: white !important;">Selamat {{ $greeting }}</p>
                <h2 class="text-responsive-xl" style="font-weight: 900; line-height: 1.1; margin: 0; color: white !important;">
                    Ahlan, {{ $guardian?->name ?? 'Wali Santri' }} 👋
                </h2>
                <div class="flex-row-center gap-2" style="margin-top: 8px; opacity: 0.7;">
                    <x-heroicon-m-calendar-days class="guardian-icon-fix" style="color: white !important;" />
                    <span style="font-size: 12px; font-weight: 600; color: white !important;">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
            </div>
        </div>

        @if($waNumber)
        <div style="flex-shrink: 0; width: 100%; display: flex; justify-content: flex-start; margin-top: 10px;">
            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Assalamu\'alaikum, saya ' . ($guardian?->name ?? 'Wali') . ', ingin bertanya mengenai perkembangan anak saya.') }}"
               target="_blank"
               style="display: flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 1.25rem; background: var(--card-bg); color: #047857; text-decoration: none; font-size: 13px; font-weight: 800; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1); transition: transform 0.2s; min-width: max-content;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="color: #10b981; flex-shrink: 0;">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.118 1.528 5.849L0 24l6.335-1.652A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 01-5.001-1.372l-.36-.214-3.737.978.999-3.645-.235-.374A9.778 9.778 0 012.182 12c0-5.417 4.401-9.818 9.818-9.818s9.818 4.401 9.818 9.818-4.401 9.818-9.818 9.818z"/></svg>
                <span style="color: #047857 !important; display: inline-block !important;">Bantuan Kontak</span>
            </a>
        </div>
        @endif
    </div>
</div>
