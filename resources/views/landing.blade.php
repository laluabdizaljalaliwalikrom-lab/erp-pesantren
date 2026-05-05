<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ settings()->pesantren_name ?? 'Pesantren Modern' }} | Tahfidz & Akademik Unggulan</title>

    @if(settings()->favicon_path)
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . settings()->favicon_path) }}">
    @endif

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary-color: {{ settings()->primary_color ?? '#065f46' }};
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .islamic-pattern {
            background-color: #f9fafb;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0l15 30-15 30-15-30z' fill='%23065f46' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
        }

        .bento-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .bento-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        }

        .prose ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .prose li {
            margin-bottom: 0.5rem;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-slate-50 islamic-pattern antialiased text-slate-900" 
      x-data="{ scrolled: false, mobileMenu: false }" 
      @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Navigation -->
    <nav class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-6xl transition-all duration-300"
         :class="scrolled ? 'top-2' : 'top-4'">
        <div class="glass rounded-full px-4 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between shadow-xl">
            <!-- Brand -->
            <div class="flex items-center gap-2 sm:gap-3 overflow-hidden min-w-0">
                @if(settings()->logo_path)
                <img src="{{ asset('storage/' . settings()->logo_path) }}" alt="Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain shrink-0">
                @else
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white shrink-0" style="background-color: var(--primary-color)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22v-7l-2-2"/><path d="M17 8v.01"/><path d="M20 9v.01"/><path d="M20 13v.01"/><path d="M3 13v.01"/><path d="M3 9v.01"/><path d="M4 11v.01"/><path d="M4 15v.01"/><path d="M12 2v.01"/><path d="M12 5v.01"/><path d="M12 8v.01"/><path d="M12 11v.01"/></svg>
                </div>
                @endif
                <span class="font-outfit font-bold text-xs sm:text-base md:text-lg tracking-tight text-emerald-950 truncate max-w-[140px] sm:max-w-none">{{ settings()->pesantren_name }}</span>
            </div>
            
            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-8 font-medium text-slate-600 text-sm shrink-0">
                <a href="#profile" class="hover:text-emerald-700 transition-colors">Profil</a>
                <a href="#visi" class="hover:text-emerald-700 transition-colors">Visi & Misi</a>
                <a href="#news" class="hover:text-emerald-700 transition-colors">Berita</a>
            </div>

            <!-- CTA Actions -->
            <div class="flex items-center gap-2 shrink-0">
                @auth
                <a href="{{ route('filament.guardian.pages.guardian-dashboard') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 sm:px-6 py-2 rounded-full text-[10px] sm:text-sm font-bold transition-all shadow-md">Dashboard</a>
                @else
                <a href="/admin/login" class="hidden sm:inline-flex bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-full text-sm font-bold transition-all shadow-md">Login Admin</a>
                @endauth
                
                <!-- Mobile Toggle -->
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                    <svg x-show="!mobileMenu" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    <svg x-show="mobileMenu" x-cloak xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div x-show="mobileMenu" x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="absolute top-full left-0 right-0 mt-2 mx-4 lg:hidden">
            <div class="bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 flex flex-col gap-4">
                <a href="#profile" @click="mobileMenu = false" class="text-lg font-semibold text-slate-700 px-4 py-2 hover:bg-slate-50 rounded-xl transition-colors">Profil Pesantren</a>
                <a href="#visi" @click="mobileMenu = false" class="text-lg font-semibold text-slate-700 px-4 py-2 hover:bg-slate-50 rounded-xl transition-colors">Visi & Misi</a>
                <a href="#news" @click="mobileMenu = false" class="text-lg font-semibold text-slate-700 px-4 py-2 hover:bg-slate-50 rounded-xl transition-colors">Berita Terkini</a>
                <hr class="border-slate-100">
                <a href="/admin/login" class="bg-slate-800 text-white text-center py-3 rounded-xl font-bold">Login Admin</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-[90vh] lg:min-h-screen flex items-center pt-28 pb-16 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/landing/hero.png') }}" class="w-full h-full object-cover" alt="Hero Background">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/90 via-emerald-900/40 to-emerald-900/10 lg:to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-transparent to-transparent"></div>
        </div>

        <div class="container mx-auto px-8 sm:px-10 relative z-10">
            <div class="max-w-4xl" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div x-show="show" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 translate-y-12" x-transition:enter-end="opacity-100 translate-y-0">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-500/20 text-emerald-300 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-widest mb-6 border border-emerald-400/30 backdrop-blur-sm">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        Pendidikan Berbasis Adab & Ilmu
                    </span>
                    <h1 class="text-3xl sm:text-6xl lg:text-8xl font-outfit font-bold text-white leading-[1.2] sm:leading-[1.1] mb-6">
                        @if(settings()->hero_title)
                        {!! nl2br(e(settings()->hero_title)) !!}
                        @else
                        Mencetak Generasi <br> <span class="text-emerald-400">Rabbani</span> Unggulan
                        @endif
                    </h1>
                    <p class="text-base sm:text-xl text-emerald-50/80 mb-10 leading-relaxed max-w-2xl font-light">
                        {{ settings()->hero_subtitle ?? 'Visi Utama: Terwujudnya lembaga pendidikan Islam yang unggul dalam melahirkan generasi yang bertaqwa, cerdas, mandiri, dan berakhlakul karimah.' }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('filament.guardian.auth.login') }}" class="bg-gradient-to-br from-emerald-500 to-emerald-700 hover:shadow-2xl hover:shadow-emerald-500/40 text-white px-10 py-5 rounded-2xl font-bold text-lg transition-all flex items-center justify-center gap-3 group">
                            Login Wali Santri
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                        <a href="#profile" class="bg-white/10 hover:bg-white/20 text-white backdrop-blur-md px-10 py-5 rounded-2xl font-bold text-lg border border-white/20 transition-all flex items-center justify-center">
                            Profil Pesantren
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Section -->
    <section id="profile" class="py-20 lg:py-32 bg-white relative">
        <div class="container mx-auto px-8 sm:px-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="relative px-2 sm:px-0">
                    <div class="relative z-10 rounded-3xl lg:rounded-[3rem] overflow-hidden shadow-2xl rotate-1 lg:rotate-2 hover:rotate-0 transition-transform duration-700">
                        <img src="{{ asset('images/landing/students.png') }}" alt="Santri Belajar" class="w-full">
                    </div>
                    <div class="absolute -bottom-6 -right-6 lg:-bottom-10 lg:-right-10 w-48 h-48 lg:w-64 lg:h-64 bg-emerald-100 rounded-3xl lg:rounded-[3rem] -z-0"></div>
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl"></div>
                </div>
                
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h4 class="text-emerald-600 font-bold uppercase tracking-widest text-sm">Tentang Kami</h4>
                        <h2 class="text-4xl lg:text-5xl font-outfit font-bold text-slate-900 leading-tight">
                            Membangun Karakter Islami Sejak <span class="text-emerald-600">Dini.</span>
                        </h2>
                    </div>
                    <div class="text-slate-600 leading-relaxed text-lg prose prose-slate">
                        @if(settings()->history)
                        {!! settings()->history !!}
                        @else
                        <p>Berdiri sejak tahun 1995 di jantung Pulau Lombok, Pesantren Al-Ikhlas telah menjadi kawah candradimuka bagi ribuan santri dari seluruh penjuru nusantara.</p>
                        <p>Kami memadukan kurikulum salafiyah yang kuat dengan pendidikan modern yang adaptif untuk mempersiapkan tantangan masa depan.</p>
                        @endif
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-emerald-600 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            </div>
                            <span class="font-bold text-emerald-900">Kurikulum Unggul</span>
                        </div>
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-slate-600 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/></svg>
                            </div>
                            <span class="font-bold text-slate-800">Fasilitas Lengkap</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Director Section -->
    <section class="py-24 lg:py-32 bg-slate-50 relative overflow-hidden">
        <div class="container mx-auto px-8 sm:px-10 relative z-10">
            <div class="grid lg:grid-cols-5 gap-16 items-center">
                <div class="lg:col-span-2 relative">
                    <div class="relative z-10 rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white">
                        @if(settings()->director_image_path)
                            <img src="{{ asset('storage/' . settings()->director_image_path) }}" alt="Pimpinan Pesantren" class="w-full aspect-[3/4] object-cover">
                        @else
                            <div class="w-full aspect-[3/4] bg-emerald-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-300"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-emerald-600 rounded-2xl -z-0"></div>
                </div>

                <div class="lg:col-span-3 space-y-8">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center text-white mb-6 shadow-lg shadow-emerald-600/30">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                        </div>
                        <h2 class="text-4xl lg:text-5xl font-outfit font-bold text-slate-900 leading-tight">Sambutan <span class="text-emerald-600">Pimpinan.</span></h2>
                    </div>
                    
                    <div class="text-slate-600 leading-relaxed text-xl italic font-light prose prose-slate">
                        @if(settings()->director_greeting)
                            {!! settings()->director_greeting !!}
                        @else
                            <p>"Bismillah. Kami menyambut kehadiran para calon generasi rabbani di lembaga kami. Fokus kami bukan hanya pada kecerdasan intelektual, namun yang utama adalah pembentukan adab dan karakter Qur'ani."</p>
                        @endif
                    </div>

                    <div class="pt-8 border-t border-slate-200">
                        <h4 class="text-2xl font-outfit font-bold text-slate-900">{{ settings()->director_name ?? 'KH. Ahmad Dahlan, Lc' }}</h4>
                        <p class="text-emerald-600 font-bold tracking-widest uppercase text-sm mt-1">{{ settings()->director_title ?? 'Pimpinan Pondok Pesantren' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-1/3 h-full bg-emerald-600/5 -skew-x-12 translate-x-1/2"></div>
    </section>

    <!-- Visi Misi Section -->
    <section id="visi" class="py-24 lg:py-32 bg-white islamic-pattern">
        <div class="container mx-auto px-8 sm:px-10 text-center space-y-4">
            <h4 class="text-emerald-600 font-bold uppercase tracking-widest text-sm">Pilar Filosofis</h4>
            <h2 class="text-4xl lg:text-5xl font-outfit font-bold text-slate-900">Visi & Misi Utama</h2>
        </div>

        <div class="container mx-auto px-8 sm:px-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Visi Card -->
                <div class="lg:col-span-2 bg-emerald-900 rounded-3xl lg:rounded-[3.5rem] p-8 lg:p-16 text-white relative overflow-hidden bento-card shadow-2xl shadow-emerald-900/20" 
                     style="background-color: var(--primary-color)">
                    <div class="relative z-10 space-y-6 lg:space-y-8">
                        <div class="w-14 h-14 lg:w-16 lg:h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="space-y-3 lg:space-y-4">
                            <span class="text-emerald-300 font-bold uppercase tracking-widest text-[10px] sm:text-sm">Visi Pesantren</span>
                            <h3 class="text-2xl lg:text-5xl font-outfit font-bold leading-tight">
                                @if(settings()->vision)
                                {!! strip_tags(settings()->vision) !!}
                                @else
                                Membentuk Generasi Qur'ani yang Berwawasan Global & Berakhlakul Karimah.
                                @endif
                            </h3>
                        </div>
                    </div>
                    <div class="absolute -right-20 -bottom-20 opacity-5 rotate-12 scale-150">
                        <svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/></svg>
                    </div>
                </div>

                <!-- Misi Card -->
                <div class="bg-white rounded-3xl lg:rounded-[3.5rem] p-8 lg:p-12 shadow-xl border border-slate-100 bento-card">
                    <div class="space-y-6 lg:space-y-8">
                        <div class="w-14 h-14 lg:w-16 lg:h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z"/><path d="M12 8l4 4-4 4"/><path d="M8 12h8"/></svg>
                        </div>
                        <div class="space-y-4 lg:space-y-6">
                            <span class="text-emerald-600 font-bold uppercase tracking-widest text-[10px] sm:text-sm">Misi Strategis</span>
                            <div class="text-slate-600 prose prose-emerald prose-sm leading-relaxed">
                                @if(settings()->mission)
                                {!! settings()->mission !!}
                                @else
                                <ul class="space-y-3">
                                    <li class="flex gap-3">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span>
                                        Tahfidz Qur'an terintegrasi akademik.
                                    </li>
                                    <li class="flex gap-3">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span>
                                        Penguasaan Bahasa Arab & Inggris.
                                    </li>
                                    <li class="flex gap-3">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span>
                                        Pemanfaatan Teknologi Informasi.
                                    </li>
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="mt-16 grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-8 rounded-3xl text-center shadow-sm border border-slate-100 bento-card">
                    <div class="text-4xl font-outfit font-bold text-emerald-700 mb-2">{{ $stats['students_count'] ?: '1.2k+' }}</div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest">Santri Aktif</div>
                </div>
                <div class="bg-white p-8 rounded-3xl text-center shadow-sm border border-slate-100 bento-card">
                    <div class="text-4xl font-outfit font-bold text-emerald-700 mb-2">{{ $stats['teachers_count'] ?: '85+' }}</div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest">Pengajar</div>
                </div>
                <div class="bg-white p-8 rounded-3xl text-center shadow-sm border border-slate-100 bento-card">
                    <div class="text-4xl font-outfit font-bold text-emerald-700 mb-2">{{ $stats['alumni_count'] ?: '5k+' }}</div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest">Alumni</div>
                </div>
                <div class="bg-white p-8 rounded-3xl text-center shadow-sm border border-slate-100 bento-card">
                    <div class="text-4xl font-outfit font-bold text-emerald-700 mb-2">{{ $stats['achievements_count'] ?: '120+' }}</div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest">Prestasi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="py-24 lg:py-32 bg-white">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-end gap-6 mb-20">
            <div class="space-y-4">
                <h4 class="text-emerald-600 font-bold uppercase tracking-widest text-sm">Update Terkini</h4>
                <h2 class="text-4xl lg:text-5xl font-outfit font-bold text-slate-900 leading-tight">Warta & Kegiatan Santri</h2>
            </div>
            <a href="#" class="inline-flex items-center gap-2 text-emerald-700 font-bold hover:gap-4 transition-all group">
                Lihat Semua Berita
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div class="container mx-auto px-6 grid md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($news as $item)
            <article class="group cursor-pointer">
                <div class="relative rounded-[2.5rem] overflow-hidden mb-8 shadow-xl">
                    <img src="{{ asset('images/landing/mosque.png') }}" class="w-full h-72 object-cover group-hover:scale-110 transition-transform duration-700" alt="News Image">
                    <div class="absolute top-6 left-6 px-4 py-1.5 bg-white/90 backdrop-blur-md rounded-full text-[10px] font-bold text-emerald-700 uppercase tracking-wider">{{ $item->category }}</div>
                </div>
                <div class="space-y-4 px-2">
                    <div class="flex items-center gap-4 text-slate-400 text-sm">
                        <time>{{ $item->published_at?->format('d M Y') ?? $item->created_at->format('d M Y') }}</time>
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                        <span>Oleh Admin</span>
                    </div>
                    <h3 class="text-2xl font-outfit font-bold text-slate-900 group-hover:text-emerald-700 transition-colors leading-snug">{{ $item->title }}</h3>
                    <p class="text-slate-500 line-clamp-3 leading-relaxed">{{ strip_tags($item->content) }}</p>
                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-20 text-slate-400 font-medium">Belum ada berita terbaru saat ini.</div>
            @endforelse
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 pt-24 pb-12 text-white relative overflow-hidden">
        <div class="container mx-auto px-6 grid md:grid-cols-2 lg:grid-cols-4 gap-16 relative z-10">
            <div class="lg:col-span-2 space-y-10">
                <div class="flex items-center gap-4">
                    @if(settings()->logo_path)
                    <img src="{{ asset('storage/' . settings()->logo_path) }}" alt="Logo" class="w-14 h-14 object-contain">
                    @else
                    <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22v-7l-2-2"/><path d="M17 8v.01"/><path d="M20 9v.01"/><path d="M12 2v.01"/></svg>
                    </div>
                    @endif
                    <span class="font-outfit font-bold text-3xl tracking-tight">{{ settings()->pesantren_name }}</span>
                </div>
                <p class="text-slate-400 max-w-sm leading-relaxed text-lg">
                    {{ settings()->footer_text ?? 'Membentuk karakter islami yang berintegritas dan profesional di era digital.' }}
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center hover:bg-emerald-600 transition-all border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center hover:bg-emerald-600 transition-all border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    </a>
                </div>
            </div>

            <div class="space-y-10">
                <h4 class="font-outfit font-bold text-xl">Tautan Cepat</h4>
                <ul class="space-y-4 text-slate-400 text-lg">
                    <li><a href="#profile" class="hover:text-emerald-500 transition-colors">Profil Sekolah</a></li>
                    <li><a href="#visi" class="hover:text-emerald-500 transition-colors">Visi & Misi</a></li>
                    <li><a href="#" class="hover:text-emerald-500 transition-colors">Pendaftaran Online</a></li>
                </ul>
            </div>

            <div class="space-y-10">
                <h4 class="font-outfit font-bold text-xl">Kontak Kami</h4>
                <ul class="space-y-6 text-slate-400">
                    <li class="flex items-start gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="text-sm leading-relaxed">{{ settings()->address ?? 'Alamat Pesantren Belum Diatur.' }}</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 shrink-0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span class="text-sm">{{ settings()->phone ?? 'Belum Ada Telepon.' }}</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="container mx-auto px-6 mt-20 pt-10 border-t border-white/5 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ settings()->pesantren_name }}. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>