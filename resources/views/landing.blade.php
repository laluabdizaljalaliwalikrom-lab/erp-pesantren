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
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-outfit { font-family: 'Outfit', sans-serif; }
        
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .islamic-pattern {
            background-color: #f9fafb;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0l15 30-15 30-15-30z' fill='%203102d4b' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
        }

        .bento-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .bento-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
    </style>
</head>
<body class="bg-slate-50 islamic-pattern antialiased text-slate-900" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Navigation -->
    <nav class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-6xl transition-all duration-300"
         :class="scrolled ? 'top-4' : 'top-6'">
        <div class="glass rounded-full px-6 py-3 flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-3">
                @if(settings()->logo_path)
                    <img src="{{ asset('storage/' . settings()->logo_path) }}" alt="Logo" class="w-10 h-10 object-contain">
                @else
                    <div class="w-10 h-10 bg-emerald-700 rounded-full flex items-center justify-center text-white" style="background-color: var(--primary-color)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shrub"><path d="M12 22v-7l-2-2"/><path d="M17 8v.01"/><path d="M20 9v.01"/><path d="M20 13v.01"/><path d="M3 13v.01"/><path d="M3 9v.01"/><path d="M4 11v.01"/><path d="M4 15v.01"/><path d="M7 8v.01"/><path d="M8 11v.01"/><path d="M8 15v.01"/><path d="M12 2v.01"/><path d="M12 5v.01"/><path d="M12 8v.01"/><path d="M12 11v.01"/><path d="M16 11v.01"/><path d="M16 15v.01"/><path d="M17 18v.01"/><path d="M20 17v.01"/><path d="M21 14v.01"/><path d="M21 10v.01"/><path d="M4 18v.01"/><path d="M7 18v.01"/></svg>
                    </div>
                @endif
                <span class="font-outfit font-bold text-lg tracking-tight text-emerald-900">{{ settings()->pesantren_name }}</span>
            </div>
            
            <div class="hidden md:flex items-center gap-8 font-medium text-slate-600 text-sm">
                <a href="#profile" class="hover:text-primary transition-colors">Profil</a>
                <a href="#visi" class="hover:text-primary transition-colors">Visi & Misi</a>
                <a href="#gallery" class="hover:text-primary transition-colors">Galeri</a>
                <a href="#news" class="hover:text-primary transition-colors">Berita</a>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('filament.guardian.pages.guardian-dashboard') }}" class="bg-primary hover:opacity-90 text-white px-5 py-2 rounded-full text-sm font-semibold transition-all shadow-md hover:shadow-lg">Dashboard</a>
                @else
                    <a href="{{ route('filament.guardian.auth.login') }}" class="text-slate-600 hover:text-primary px-4 py-2 text-sm font-semibold">Masuk</a>
                    <a href="{{ route('filament.guardian.auth.register') }}" class="bg-primary hover:opacity-90 text-white px-5 py-2 rounded-full text-sm font-semibold transition-all shadow-md hover:shadow-lg">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/landing/hero.png') }}" class="w-full h-full object-cover" alt="Pesantren Lombok">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/80 via-emerald-900/40 to-transparent"></div>
        </div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="max-w-3xl" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div x-show="show" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0">
                    <span class="inline-block px-4 py-1.5 bg-emerald-500/20 text-emerald-300 rounded-full text-xs font-bold uppercase tracking-widest mb-6 border border-emerald-400/30 backdrop-blur-sm">Pendidikan Berbasis Adab & Ilmu</span>
                    <h1 class="text-5xl md:text-7xl font-outfit font-bold text-white leading-[1.1] mb-6">
                        @if(settings()->hero_title)
                            {!! nl2br(e(settings()->hero_title)) !!}
                        @else
                            Mencetak Generasi <br> <span class="text-emerald-400">Rabbani</span> yang Berprestasi
                        @endif
                    </h1>
                    <p class="text-lg md:text-xl text-emerald-50/80 mb-10 leading-relaxed font-light">
                        {{ settings()->hero_subtitle ?? 'Visi Utama: Terwujudnya lembaga pendidikan Islam yang unggul dalam melahirkan generasi yang bertaqwa, cerdas, mandiri, dan berakhlakul karimah.' }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#profile" class="bg-primary hover:opacity-90 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all shadow-xl hover:shadow-emerald-500/20 flex items-center justify-center gap-2 group">
                            Pelajari Lebih Lanjut
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                        <a href="#" class="bg-white/10 hover:bg-white/20 text-white backdrop-blur-md px-8 py-4 rounded-xl font-bold text-lg border border-white/20 transition-all flex items-center justify-center">
                            Unduh Brosur
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 text-white/50 animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
        </div>
    </section>

    <!-- Profile Section -->
    <section id="profile" class="py-24 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl">
                        <img src="{{ asset('images/landing/students.png') }}" alt="Santri Belajar" class="w-full">
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-emerald-100 rounded-3xl -z-0 hidden md:block"></div>
                    <div class="absolute -top-10 -left-10 w-48 h-48 bg-gold-500/10 rounded-full blur-3xl -z-0"></div>
                </div>
                
                <div>
                    <h2 class="text-4xl font-outfit font-bold text-slate-900 mb-6 relative inline-block">
                        Sejarah & Profil
                        <div class="absolute -bottom-2 left-0 w-20 h-1.5 bg-primary rounded-full"></div>
                    </h2>
                    <div class="text-slate-600 leading-relaxed mb-8 prose prose-slate max-w-none">
                        @if(settings()->history)
                            {!! settings()->history !!}
                        @else
                            <p class="text-lg">Berdiri sejak tahun 1995 di jantung Pulau Lombok, Pesantren Al-Ikhlas telah menjadi kawah candradimuka bagi ribuan santri dari seluruh penjuru nusantara.</p>
                            <p>Dengan memadukan kurikulum salafiyah yang kuat dan pendidikan modern yang adaptif, kami berkomitmen untuk menjaga warisan ilmu ulama sekaligus mempersiapkan santri menghadapi tantangan global.</p>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                            <h4 class="font-outfit font-bold text-emerald-900 text-xl mb-1">Kurikulum Terintegrasi</h4>
                            <p class="text-sm text-emerald-700">Perpaduan ilmu agama dan sains modern.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-outfit font-bold text-slate-900 text-xl mb-1">Fasilitas Modern</h4>
                            <p class="text-sm text-slate-500">Laboratorium, Perpustakaan, dan Asrama nyaman.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bento Visi Misi -->
    <section id="visi" class="py-24 bg-slate-50">
        <div class="container mx-auto px-6 text-center mb-16">
            <h2 class="text-4xl font-outfit font-bold text-slate-900 mb-4">Pilar Utama Kami</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">Landasan filosofis yang membimbing setiap langkah kami dalam mendidik generasi umat.</p>
        </div>

        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Visi Card -->
                <div class="md:col-span-2 bg-emerald-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden bento-card" style="background-color: var(--primary-color)">
                    <div class="relative z-10">
                        <span class="bg-white/20 text-white px-4 py-1 rounded-full text-xs font-bold uppercase mb-6 inline-block tracking-widest border border-white/30 backdrop-blur-sm">Visi</span>
                        <h3 class="text-4xl font-outfit font-bold mb-6">Membangun Peradaban dari Akhlak yang Mulia.</h3>
                        <div class="text-emerald-100/80 text-lg leading-relaxed max-w-xl prose prose-invert">
                            @if(settings()->vision)
                                {!! settings()->vision !!}
                            @else
                                <p>Menjadi lembaga pendidikan Islam terdepan di Indonesia yang melahirkan pemimpin masa depan berjiwa Qur'ani dan berwawasan global.</p>
                            @endif
                        </div>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-10 -rotate-12 translate-y-10 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="m8 3 4 8 5-5 5 15H2L8 3z"/></svg>
                    </div>
                </div>

                <!-- Misi 1 -->
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-100 bento-card">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-700 mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z"/><path d="M12 8l4 4-4 4"/><path d="M8 12h8"/></svg>
                    </div>
                    <h4 class="text-2xl font-outfit font-bold text-slate-900 mb-4">Misi Kami</h4>
                    <div class="text-slate-500 leading-relaxed prose prose-sm">
                        @if(settings()->mission)
                            {!! settings()->mission !!}
                        @else
                            <ul class="list-disc pl-4 space-y-2">
                                <li>Program menghafal Al-Qur'an 30 Juz.</li>
                                <li>Pembinaan adab dan karakter terpadu.</li>
                                <li>Penguasaan bahasa dan teknologi.</li>
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="md:col-span-3 mt-12 grid grid-cols-2 md:grid-cols-4 gap-6">
                     <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 text-center bento-card" x-data="{ count: 0, target: {{ $stats['students_count'] ?: 1200 }} }" x-init="let timer = setInterval(() => { if (count < target) { count += Math.ceil(target/100) } else { count = target; clearInterval(timer); } }, 20)">
                        <div class="text-4xl font-outfit font-bold text-primary mb-2" x-text="count + '+'">0+</div>
                        <div class="text-slate-400 font-medium uppercase text-xs tracking-wider">Santri Aktif</div>
                    </div>
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 text-center bento-card" x-data="{ count: 0, target: {{ $stats['teachers_count'] ?: 85 }} }" x-init="let timer = setInterval(() => { if (count < target) { count += 1 } else { count = target; clearInterval(timer); } }, 20)">
                        <div class="text-4xl font-outfit font-bold text-primary mb-2" x-text="count + '+'">0+</div>
                        <div class="text-slate-400 font-medium uppercase text-xs tracking-wider">Tenaga Pengajar</div>
                    </div>
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 text-center bento-card" x-data="{ count: 0, target: {{ $stats['alumni_count'] ?: 5000 }} }" x-init="let timer = setInterval(() => { if (count < target) { count += Math.ceil(target/100) } else { count = target; clearInterval(timer); } }, 20)">
                        <div class="text-4xl font-outfit font-bold text-primary mb-2" x-text="count + '+'">0+</div>
                        <div class="text-slate-400 font-medium uppercase text-xs tracking-wider">Alumni Sukses</div>
                    </div>
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 text-center bento-card" x-data="{ count: 0, target: {{ $stats['achievements_count'] ?: 120 }} }" x-init="let timer = setInterval(() => { if (count < target) { count += Math.ceil(target/100) } else { count = target; clearInterval(timer); } }, 20)">
                        <div class="text-4xl font-outfit font-bold text-primary mb-2" x-text="count + '+'">0+</div>
                        <div class="text-slate-400 font-medium uppercase text-xs tracking-wider">Prestasi Nasional</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest News -->
    <section id="news" class="py-24 bg-white">
        <div class="container mx-auto px-6 mb-16 flex flex-col md:flex-row items-end justify-between gap-4">
            <div>
                <h2 class="text-4xl font-outfit font-bold text-slate-900 mb-4">Informasi & Berita</h2>
                <p class="text-slate-500 max-w-xl">Ikuti perkembangan terbaru dan kegiatan harian para santri di {{ settings()->pesantren_name }}.</p>
            </div>
            <a href="#" class="text-primary font-bold hover:underline flex items-center gap-2">
                Lihat Semua Berita
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>

        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                @forelse($news as $item)
                    <div class="group cursor-pointer">
                        <div class="rounded-3xl overflow-hidden mb-6 relative">
                            <img src="{{ asset('images/landing/mosque.png') }}" alt="News" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-primary uppercase">{{ $item->category }}</div>
                        </div>
                        <div class="flex items-center gap-3 text-slate-400 text-sm mb-3">
                            <span>{{ $item->published_at?->format('d M Y') ?? $item->created_at->format('d M Y') }}</span>
                            <span class="w-1.5 h-1.5 bg-slate-200 rounded-full"></span>
                            <span>Oleh Admin</span>
                        </div>
                        <h3 class="text-2xl font-outfit font-bold text-slate-900 mb-4 group-hover:text-primary transition-colors">{{ $item->title }}</h3>
                        <p class="text-slate-500 leading-relaxed line-clamp-3">
                            {{ strip_tags($item->content) }}
                        </p>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-10 text-slate-400">Belum ada berita terbaru.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 py-20 text-white relative overflow-hidden">
        <div class="container mx-auto px-6 grid md:grid-cols-4 gap-16 relative z-10">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-8">
                    @if(settings()->logo_path)
                        <img src="{{ asset('storage/' . settings()->logo_path) }}" alt="Logo" class="w-12 h-12 object-contain">
                    @else
                        <div class="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center text-white" style="background-color: var(--primary-color)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shrub"><path d="M12 22v-7l-2-2"/><path d="M17 8v.01"/><path d="M20 9v.01"/><path d="M20 13v.01"/><path d="M3 13v.01"/><path d="M3 9v.01"/><path d="M4 11v.01"/><path d="M4 15v.01"/><path d="M7 8v.01"/><path d="M8 11v.01"/><path d="M8 15v.01"/><path d="M12 2v.01"/><path d="M12 5v.01"/><path d="M12 8v.01"/><path d="M12 11v.01"/><path d="M16 11v.01"/><path d="M16 15v.01"/><path d="M17 18v.01"/><path d="M20 17v.01"/><path d="M21 14v.01"/><path d="M21 10v.01"/><path d="M4 18v.01"/><path d="M7 18v.01"/></svg>
                        </div>
                    @endif
                    <span class="font-outfit font-bold text-2xl tracking-tight">{{ settings()->pesantren_name }}</span>
                </div>
                <p class="text-slate-400 max-w-sm mb-10 leading-relaxed">
                    {{ settings()->footer_text ?? 'Membentuk karakter islami yang berintegritas dan profesional di era digital.' }}
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="font-outfit font-bold text-xl mb-8">Tautan Cepat</h4>
                <ul class="space-y-4 text-slate-400">
                    <li><a href="#" class="hover:text-primary transition-colors">Pendaftaran Online</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Kurikulum Santri</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Kontak Kami</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-outfit font-bold text-xl mb-8">Hubungi Kami</h4>
                <ul class="space-y-4 text-slate-400 text-sm">
                    <li class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ settings()->address ?? 'Alamat belum diatur.' }}
                    </li>
                    <li class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary shrink-0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        {{ settings()->phone ?? 'Telepon belum diatur.' }}
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="container mx-auto px-6 mt-20 pt-8 border-t border-white/5 text-center text-slate-500 text-xs">
            <p>&copy; {{ date('Y') }} {{ settings()->pesantren_name }}. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
