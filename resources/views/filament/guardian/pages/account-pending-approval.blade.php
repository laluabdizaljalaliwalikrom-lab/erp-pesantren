<div class="flex flex-col items-center justify-center text-center py-10">
    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-full">
        <x-heroicon-o-clock class="w-12 h-12 text-amber-600 dark:text-amber-400" />
    </div>

    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white mb-4">
        Pendaftaran Berhasil!
    </h2>

    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-8">
        Terima kasih telah mendaftar. Admin kami sedang memverifikasi data Anda. <br>
        <span class="font-semibold text-gray-950 dark:text-white">Mohon cek kembali dalam 1x24 jam.</span>
    </p>

    <div class="flex flex-col gap-3 w-full">
        <button wire:click="logout" type="button" class="fi-btn fi-btn-size-md fi-btn-color-gray relative flex items-center justify-center gap-1 font-semibold outline-none transition duration-75 focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-70 bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10 ring-1 ring-gray-950/10 dark:ring-white/20 py-2.5 px-4 rounded-lg w-full">
            <span>Keluar Sesi</span>
        </button>
    </div>
</div>
