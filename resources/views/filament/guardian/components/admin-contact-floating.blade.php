@php
    $setting = \App\Models\AppSetting::instance();
    $user = auth()->user(); 
    if ($user && method_exists($user, 'students')) {
        $studentNames = $user->students ? $user->students->pluck('full_name')->join(', ') : '';
        $message = "Assalamu'alaikum Admin, saya Wali dari {$studentNames}, ingin menanyakan terkait...";
    } else {
        $message = "Assalamu'alaikum Admin, ...";
    }
    
    $phone = $setting->phone ?? '';
    if (str_starts_with($phone, '0')) {
        $phone = '62' . substr($phone, 1);
    }
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $whatsappUrl = $phone ? "https://wa.me/{$phone}?text=" . urlencode($message) : '#';
@endphp

@if(auth()->check() && request()->routeIs('filament.guardian.*'))
<div class="fixed bottom-6 right-6 z-[9999]">
    <a href="{{ $whatsappUrl }}" 
       target="_blank" 
       rel="noopener noreferrer"
       class="relative flex items-center justify-center w-[60px] h-[60px] bg-[#25D366] hover:bg-[#128C7E] text-white rounded-full shadow-[0_8px_30px_rgba(0,0,0,0.15)] hover:shadow-[0_12px_40px_rgba(37,211,102,0.4)] hover:-translate-y-1 transition-all duration-300 group"
       title="Hubungi Admin">
        
        <x-heroicon-s-chat-bubble-oval-left-ellipsis class="w-8 h-8 shrink-0" />
        
        <span class="absolute right-[calc(100%+1rem)] bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 px-4 py-2 rounded-xl text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300 shadow-[0_10px_25px_rgba(0,0,0,0.1)] dark:shadow-[0_10px_25px_rgba(0,0,0,0.5)] border border-black/5 dark:border-white/10 whitespace-nowrap pointer-events-none">
            Hubungi Admin 💬
        </span>
    </a>
</div>
@endif
