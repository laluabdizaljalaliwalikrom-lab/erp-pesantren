<!DOCTYPE html>
<html lang="id" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ settings()->pesantren_name ?? 'ERP Pesantren' }}</title>
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @filamentStyles
    @vite(['resources/css/app.css'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-outfit { font-family: 'Outfit', sans-serif; }

        .sufi-bg {
            background-color: #0f172a; /* Darker for Admin (Slate 950) */
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(5, 150, 105, 0.1) 0%, transparent 50%),
                url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0l20 40-20 40-20-40z' fill='%23ffffff' fill-opacity='0.02' fill-rule='evenodd'/%3E%3C/svg%3E");
            background-attachment: fixed;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .floating {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(0.5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        @keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .fade-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both; }
        
        .fi-btn { 
            border-radius: 16px !important; 
            padding: 12px 24px !important;
            font-weight: 700 !important;
            letter-spacing: 0.025em !important;
            transition: all 0.3s ease !important;
        }
        .fi-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        }

        .fi-fo-text-input {
            border-radius: 16px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            padding: 4px 8px !important;
            transition: all 0.3s ease !important;
        }
        .fi-fo-text-input:focus-within {
            background: white !important;
            border-color: #10b981 !important;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important;
        }

        /* --- TOTAL CLEANUP FILAMENT UI --- */
        
        /* 1. Sembunyikan Header & Judul Ganda */
        .fi-simple-header, 
        .fi-simple-header-title, 
        .fi-simple-header-description,
        .fi-simple-main-heading { 
            display: none !important; 
        }

        /* 2. Sembunyikan Label Input (karena sudah pakai placeholder) */
        .fi-fo-field-label-wrp,
        .fi-fo-field-label { 
            display: none !important; 
        }

        /* 3. Rapikan Container Utama */
        .fi-simple-main { 
            background: transparent !important; 
            box-shadow: none !important; 
            border: none !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .fi-simple-main-card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        /* 4. Style Kotak Input (Input Fields) */
        .fi-input-wrp {
            border-radius: 16px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
            transition: all 0.3s ease !important;
            overflow: hidden !important;
            margin-bottom: 12px !important;
            min-height: 52px !important;
            display: flex !important;
            align-items: center !important;
        }

        .dark .fi-input-wrp {
            background: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .fi-input-wrp:focus-within {
            border-color: #10b981 !important;
            background: white !important;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important;
        }

        .dark .fi-input-wrp:focus-within {
            background: rgba(255, 255, 255, 0.05) !important;
            border-color: #10b981 !important;
        }

        .fi-input {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 12px 16px !important;
            font-size: 0.95rem !important;
            height: 52px !important;
            flex: 1 !important;
            color: #1e293b !important;
        }

        .dark .fi-input {
            color: #f8fafc !important;
        }

        .dark .fi-input::placeholder {
            color: rgba(255, 255, 255, 0.3) !important;
        }

        /* Rapikan tombol mata (password toggle) agar tidak merusak tinggi */
        .fi-input-wrp button {
            margin-right: 8px !important;
            padding: 8px !important;
            height: auto !important;
            color: #64748b !important;
        }

        .dark .fi-input-wrp button {
            color: #94a3b8 !important;
        }

        /* 5. Style Tombol Login (Submit Button) */
        .fi-ac-btn-action,
        .fi-btn { 
            width: 100% !important;
            border-radius: 16px !important; 
            padding: 14px 24px !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            letter-spacing: 0.025em !important;
            background-color: #10b981 !important;
            color: white !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
            border: none !important;
            margin-top: 10px !important;
            position: relative !important; /* Kunci agar loading tetap di dalam */
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
        }

        .fi-btn:hover {
            background-color: #059669 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4) !important;
        }

        /* Rapikan Loading Spinner agar tidak raksasa */
        .fi-btn svg.animate-spin,
        .fi-btn-spinner,
        button svg.animate-spin,
        [wire\:loading] svg.animate-spin {
            width: 24px !important;
            height: 24px !important;
            max-width: 24px !important;
            max-height: 24px !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
            z-index: 10 !important;
        }

        /* Sembunyikan label tombol saat loading agar tidak tumpang tindih */
        button[disabled] .fi-btn-label,
        button[disabled] span {
            opacity: 0 !important;
        }

        /* 6. Style Link "Lupa Password" & "Remember Me" */
        .fi-checkbox {
            border-radius: 6px !important;
            border-color: #d1d5db !important;
        }
        
        .fi-checkbox:checked {
            background-color: #10b981 !important;
        }

        .fi-simple-layout {
            padding: 0 !important;
            margin: 0 !important;
            min-height: 0 !important;
        }
    </style>
</head>
<body class="h-full bg-slate-50 dark:bg-gray-950 antialiased overflow-hidden">

<div class="h-screen flex overflow-hidden">

    {{-- LEFT: Authority & Security --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden sufi-bg flex-col items-center justify-center px-12 text-white border-r border-white/5 h-full">
        
        <!-- Decorative Ornaments -->
        <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] bg-blue-500/5 rounded-full blur-[120px]"></div>
        
        <div class="relative z-10 text-center max-w-lg fade-up">
            <div class="floating mb-10">
                @if(settings()->logo_path)
                    <div class="inline-block p-4 rounded-[2rem] glass-card shadow-2xl">
                        <img src="{{ Storage::disk('public')->url(settings()->logo_path) }}" alt="Logo" class="h-24 w-24 object-contain">
                    </div>
                @else
                    <div class="h-24 w-24 bg-slate-500/20 backdrop-blur-xl rounded-[2rem] flex items-center justify-center mx-auto shadow-2xl border border-white/10">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.74c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="space-y-2">
                    <p class="text-2xl font-light text-slate-400/80 italic" style="font-family: 'Times New Roman', serif;">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</p>
                    <h1 class="text-5xl font-outfit font-bold tracking-tight leading-[1.1]">
                        Portal Utama <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-slate-200 to-emerald-200">Administrasi & Kendali</span>
                    </h1>
                </div>
                
                <p class="text-lg text-slate-400 font-light leading-relaxed">
                    Akses terbatas bagi pengelola {{ settings()->pesantren_name ?? 'Pesantren' }}. 
                    Kelola data, keuangan, dan akademik dalam satu kendali terpusat yang aman.
                </p>

                <div class="flex justify-center gap-3 pt-4 text-xs font-bold tracking-widest uppercase text-slate-500">
                    <span>Integritas</span>
                    <span class="text-emerald-800">•</span>
                    <span>Keamanan</span>
                    <span class="text-emerald-800">•</span>
                    <span>Efisiensi</span>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-0 right-0 text-center">
            <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-white/10">
                System Administrator Control Center • v3.0
            </p>
        </div>
    </div>

    {{-- RIGHT: Form --}}
    <div class="flex-1 flex flex-col items-center justify-start px-6 lg:px-20 bg-white dark:bg-gray-950 relative overflow-y-auto h-full">
        <div class="lg:hidden absolute inset-0 opacity-[0.03] sufi-bg -z-10"></div>
        
        <div class="w-full max-w-md space-y-10 fade-up pt-24 pb-16 lg:pt-32" style="animation-delay: 0.2s">
            
            <div class="space-y-3">
                <div class="lg:hidden flex justify-center mb-8">
                     @if(settings()->logo_path)
                        <img src="{{ Storage::disk('public')->url(settings()->logo_path) }}" alt="Logo" class="h-16 w-16 object-contain">
                    @endif
                </div>
                <h2 class="text-4xl font-outfit font-bold text-slate-900 dark:text-white tracking-tight">Admin Login</h2>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                    Gunakan kredensial otorisasi tingkat tinggi Anda untuk masuk ke sistem pusat.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-900/50">
                {{ $slot }}
            </div>

            <div class="pt-8 border-t border-slate-100 dark:border-white/5 text-center">
                <a href="/" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 flex items-center justify-center gap-2 group transition-all">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>

</div>

@filamentScripts
@vite(['resources/js/app.js'])
</body>
</html>
