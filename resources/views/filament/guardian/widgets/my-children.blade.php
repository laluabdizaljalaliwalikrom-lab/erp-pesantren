@php
    $children = $this->getChildren();
@endphp

<div class="modern-card" style="display: flex; flex-direction: column; min-height: 100%;">
    <div class="flex-row-center gap-4" style="padding: 1.5rem 1.75rem; border-bottom: 1px solid var(--card-border); justify-content: space-between;">
        <div class="flex-row-center gap-4">
            <div style="width: 45px; height: 45px; background: #eff6ff; border-radius: 1rem; display: flex; align-items: center; justify-content: center; border: 1px solid #dbeafe;">
                <x-heroicon-o-users class="guardian-icon-fix" style="color: #2563eb !important;" />
            </div>
            <div>
                <h3 style="font-size: 16px; font-weight: 900; color: var(--text-main); margin: 0; line-height: 1.2;">Informasi Santri</h3>
                <p style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin: 0;">Update Akademik</p>
            </div>
        </div>
        <span style="font-size: 10px; font-weight: 900; color: #2563eb; background: rgba(37, 99, 235, 0.1); padding: 6px 12px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.1em;">
            {{ $children->count() }} Terhubung
        </span>
    </div>

    <div style="flex: 1; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; overflow-y: auto; max-height: 450px;">
        @forelse($children as $child)
            @php
                $statusColor = match($child->status?->value ?? '') {
                    'active'    => '#10b981',
                    'graduated' => '#3b82f6',
                    default     => '#9ca3af',
                };
            @endphp
            <div style="position: relative; padding: 1.25rem; border-radius: 1.5rem; background: var(--subcard-bg); border: 1px solid var(--card-border); display: flex; align-items: center; gap: 1.25rem;">
                {{-- Side Status Accent --}}
                <div style="position: absolute; left: 0; top: 20%; bottom: 20%; width: 5px; background: {{ $statusColor }}; border-radius: 0 4px 4px 0;"></div>
                
                {{-- Avatar --}}
                <div style="flex-shrink: 0; position: relative;">
                    <div class="guardian-avatar-fix" style="box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 2px solid var(--card-bg);">
                        @if($child->photo)
                            <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->full_name }}" class="guardian-avatar-fix">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #3b82f6 0%, #4f46e5 100%); display: flex; align-items: center; justify-content: center; border-radius: 1.25rem;">
                                <span style="color: white; font-weight: 900; font-size: 1.25rem;">{{ strtoupper(substr($child->full_name, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div style="position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; background: var(--card-bg); border-radius: 50%; padding: 2px;">
                        <div style="width: 100%; height: 100%; background: {{ $statusColor }}; border-radius: 50%;"></div>
                    </div>
                </div>

                {{-- Info --}}
                <div style="flex: 1; min-width: 0;">
                    <h4 class="truncate-fix" style="font-size: 15px; font-weight: 900; color: var(--text-main); margin: 0 0 6px 0;">
                        {{ $child->full_name }}
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <div class="flex-row-center gap-2" style="opacity: 0.6; min-width: 0;">
                            <x-heroicon-m-academic-cap class="guardian-icon-fix" style="width: 13px; height: 13px; color: var(--text-main) !important;" />
                            <span class="truncate-fix" style="font-size: 10px; font-weight: 700; text-transform: uppercase; max-width: 160px; display: block !important; color: var(--text-main) !important;">{{ $child->academic?->institution?->name ?? 'Belum Ada Lembaga' }}</span>
                        </div>
                        <div class="flex-row-center gap-2" style="opacity: 0.5; min-width: 0;">
                            <x-heroicon-m-tag class="guardian-icon-fix" style="width: 13px; height: 13px; color: var(--text-main) !important;" />
                            <span class="truncate-fix" style="font-size: 10px; font-weight: 700; text-transform: uppercase; max-width: 160px; display: block !important; color: var(--text-main) !important;">{{ $child->academic?->schoolClass?->name ?? 'Tanpa Kelas' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 0; opacity: 0.3;">
                <x-heroicon-o-user-minus style="width: 64px; height: 64px; margin-bottom: 1rem; color: var(--text-muted);" />
                <p style="font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted);">Data Santri Kosong</p>
            </div>
        @endforelse
    </div>
</div>
