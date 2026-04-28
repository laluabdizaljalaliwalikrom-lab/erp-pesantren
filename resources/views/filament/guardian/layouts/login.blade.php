<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Wali Santri - {{ settings()->pesantren_name ?? 'ERP Pesantren' }}</title>
    <meta name="description" content="Portal Wali Santri {{ settings()->pesantren_name ?? 'ERP Pesantren' }}">
    @filamentStyles
    @vite(['resources/css/app.css'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background: linear-gradient(-45deg, #064e3b, #065f46, #0d9488, #0f766e, #134e4a, #1e3a5f);
            background-size: 400% 400%;
            animation: meshMove 12s ease infinite;
        }
        @keyframes meshMove { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .fade-up { animation: fadeInUp 0.7s ease-out both; }
        .fade-up-delay { animation: fadeInUp 0.7s ease-out 0.15s both; }
        .orb { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.25; }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950">

<div class="min-h-screen flex" wire:ignore.self>

    {{-- LEFT: Hero Visual --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 relative overflow-hidden hero-bg flex-col items-center justify-center px-10 xl:px-16 text-white">
        <div class="orb w-72 h-72 bg-teal-400 top-0 -left-24 animate-pulse"></div>
        <div class="orb w-80 h-80 bg-emerald-500 bottom-0 -right-24" style="animation:pulse 6s ease-in-out infinite"></div>
        <div class="absolute inset-0 opacity-5" style="background-image:linear-gradient(rgba(255,255,255,.4) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.4) 1px,transparent 1px);background-size:48px 48px;"></div>

        <div class="relative z-10 text-center max-w-sm fade-up">
            @if(settings()->logo_path)
                <img src="{{ asset('storage/' . settings()->logo_path) }}" alt="Logo" class="h-20 w-20 object-contain mx-auto mb-6 rounded-2xl bg-white/15 p-2 backdrop-blur shadow-xl">
            @else
                <div class="h-20 w-20 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
                </div>
            @endif

            <p class="text-xl font-light mb-3 opacity-80" dir="rtl">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</p>
            <h1 class="text-3xl xl:text-4xl font-bold mb-2 leading-tight">
                Selamat Datang di<br>
                <span class="text-emerald-300">Portal Wali Santri</span>
            </h1>
            <p class="text-base opacity-70 mb-1 font-light">{{ settings()->pesantren_name ?? 'ERP Pesantren' }}</p>
            <div class="w-12 h-0.5 bg-white/30 mx-auto my-5 rounded-full"></div>
            <p class="text-sm opacity-60 leading-relaxed">Pantau perkembangan dan administrasi putra-putri Anda kapan saja dan di mana saja.</p>

            <div class="flex flex-wrap justify-center gap-2 mt-6">
                @foreach(['📚 Data Santri', '📋 Tagihan', '💳 Riwayat Bayar'] as $badge)
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-white/10 border border-white/20">{{ $badge }}</span>
                @endforeach
            </div>
        </div>

        <div class="absolute bottom-5 left-0 right-0 text-center text-xs text-white/30">
            © {{ date('Y') }} {{ settings()->pesantren_name ?? 'ERP Pesantren' }}
        </div>
    </div>

    {{-- RIGHT: Form --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12 bg-white dark:bg-gray-900">
        <div class="w-full max-w-md fade-up-delay">

            {{-- Mobile logo --}}
            <div class="flex lg:hidden justify-center mb-6">
                @if(settings()->logo_path)
                    <img src="{{ asset('storage/' . settings()->logo_path) }}" alt="Logo" class="h-12 w-12 object-contain">
                @endif
            </div>

            <div class="mb-7">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Masuk ke Akun Anda</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gunakan email dan kata sandi yang telah diberikan pihak pesantren.</p>
            </div>

            {{-- The Livewire form is injected here --}}
            @yield('content')

            @if(settings()->phone ?? null)
            <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800 text-center">
                <p class="text-xs text-gray-400 mb-2.5">Belum memiliki akun atau lupa kata sandi?</p>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', settings()->phone) }}?text={{ urlencode('Assalamu\'alaikum, saya wali santri dan membutuhkan bantuan akses Portal Wali.') }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-50 hover:bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm font-medium transition-all">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.118 1.528 5.849L0 24l6.335-1.652A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 01-5.001-1.372l-.36-.214-3.737.978.999-3.645-.235-.374A9.778 9.778 0 012.182 12c0-5.417 4.401-9.818 9.818-9.818s9.818 4.401 9.818 9.818-4.401 9.818-9.818 9.818z"/></svg>
                    Hubungi Admin via WhatsApp
                </a>
            </div>
            @endif

        </div>
    </div>

</div>

@filamentScripts
@vite(['resources/js/app.js'])
</body>
</html>
