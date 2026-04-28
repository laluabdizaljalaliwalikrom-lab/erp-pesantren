@php
    $livewire ??= null;
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="fi-simple-layout min-h-screen flex items-center justify-center p-6 sm:p-12 bg-emerald-50/50">
        
        {{-- Custom Minimalist Background --}}
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
            <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-emerald-100/50 blur-[100px]"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-teal-100/50 blur-[100px]"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            
            {{-- Header/Logo Section --}}
            <div class="text-center mb-8">
                @if(settings()->logo_path)
                    <div class="inline-flex items-center justify-center p-3 bg-white rounded-3xl shadow-sm mb-5 border border-emerald-100/50">
                        <img src="{{ asset('storage/' . settings()->logo_path) }}" alt="Logo" class="h-16 w-16 object-contain">
                    </div>
                @else
                    <div class="inline-flex items-center justify-center h-16 w-16 bg-emerald-600 rounded-2xl shadow-lg mb-5">
                        <x-heroicon-o-academic-cap class="h-10 w-10 text-white" />
                    </div>
                @endif
                
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Portal Wali Santri</h1>
                <p class="text-sm font-medium text-emerald-600 mt-1.5">{{ settings()->pesantren_name ?? 'ERP Pesantren' }}</p>
            </div>

            {{-- Centered Auth Card --}}
            <div class="bg-white/90 backdrop-blur-md shadow-xl shadow-emerald-900/5 ring-1 ring-emerald-900/10 rounded-[2rem] p-8 sm:p-10 transition-all">
                
                <div class="mb-8 ">
                    <h3 class="text-lg font-semibold text-gray-900">Selamat Datang</h3>
                    <p class="text-sm text-gray-500 mt-1">Silakan masuk untuk memantau data santri.</p>
                </div>

                {{-- Slot for the Login Form --}}
                <div class="fi-simple-form-container">
                    {{ $slot }}
                </div>

            </div>

            {{-- Footer / Help Section --}}
            @if(settings()->phone ?? null)
            <div class="mt-8 text-center px-4">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', settings()->phone) }}?text={{ urlencode('Assalamu\'alaikum, saya wali santri ingin bertanya tentang akses Portal Wali.') }}"
                   target="_blank"
                   class="inline-flex items-center justify-center gap-2.5 px-6 py-3 rounded-2xl bg-white border border-emerald-100 text-emerald-700 hover:text-emerald-800 text-sm font-semibold transition-all shadow-sm hover:shadow hover:-translate-y-0.5 group">
                    {{-- WhatsApp SVG with fixed size attributes to prevent "massive" scaling --}}
                    <svg width="20" height="20" viewBox="0 0 24 24" class="fill-current text-emerald-500 group-hover:text-emerald-600">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.118 1.528 5.849L0 24l6.335-1.652A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 01-5.001-1.372l-.36-.214-3.737.978.999-3.645-.235-.374A9.778 9.778 0 012.182 12c0-5.417 4.401-9.818 9.818-9.818s9.818 4.401 9.818 9.818-4.401 9.818-9.818 9.818z"/>
                    </svg>
                    <span>Butuh Bantuan Login?</span>
                </a>
            </div>
            @endif
            
            <div class="mt-12 text-center text-[10px] font-bold text-emerald-900/20 uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} {{ settings()->pesantren_name ?? 'ERP Pesantren' }}
            </div>

        </div>

    </div>
</x-filament-panels::layout.base>
