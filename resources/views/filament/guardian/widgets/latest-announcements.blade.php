<x-filament-widgets::widget>
    <div class="fi-wi-widget">
        <div class="modern-card">
            
            <div class="bg-primary-500/5 px-6 py-5 border-b border-[var(--card-border)] flex items-center gap-4">
                <div class="p-3 bg-primary-500/15 dark:bg-primary-500/25 text-primary-600 dark:text-primary-400 rounded-2xl flex items-center justify-center shrink-0">
                    <x-heroicon-o-megaphone class="w-7 h-7 shrink-0" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[var(--text-main)] m-0">Papan Pengumuman</h3>
                    <p class="text-sm text-[var(--text-muted)] font-medium mt-1 m-0">Informasi & agenda terbaru pesantren</p>
                </div>
            </div>

            <div class="flex flex-col bg-[var(--card-bg)]">
                @forelse($announcements as $announcement)
                    <div class="p-6 border-b border-[var(--card-border)] last:border-b-0 flex gap-5 transition-all duration-200 hover:bg-black/2 dark:hover:bg-white/2 group">
                        <!-- Timeline Indicator -->
                        <div class="flex flex-col items-center pt-1 w-16 shrink-0 text-center">
                            <span class="text-[0.7rem] font-bold text-[var(--text-muted)] uppercase tracking-widest">{{ $announcement->published_at ? $announcement->published_at->format('M') : $announcement->created_at->format('M') }}</span>
                            <span class="text-3xl font-black text-[var(--text-main)] leading-none mt-1">{{ $announcement->published_at ? $announcement->published_at->format('d') : $announcement->created_at->format('d') }}</span>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <h4 class="text-[1.1rem] font-bold text-[var(--text-main)] m-0 group-hover:text-primary-600 transition-colors">{{ $announcement->title }}</h4>
                                @php
                                    $badgeColor = match($announcement->category) {
                                        'Urgent' => 'danger',
                                        'Info' => 'info',
                                        'Agenda' => 'success',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-filament::badge :color="$badgeColor" size="sm" class="rounded-full">
                                    {{ $announcement->category }}
                                </x-filament::badge>
                            </div>
                            
                            <div class="prose prose-sm dark:prose-invert max-w-none text-[var(--text-muted)] leading-relaxed">
                                {!! $announcement->content !!}
                            </div>
                            
                            <div class="flex items-center text-[0.75rem] text-[var(--text-muted)] mt-4 font-medium">
                                <x-heroicon-o-clock class="w-3.5 h-3.5 mr-1 shrink-0" />
                                {{ $announcement->published_at ? $announcement->published_at->format('H:i') : $announcement->created_at->format('H:i') }} WIB
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 px-6">
                        <x-heroicon-o-inbox class="w-12 h-12 text-[var(--text-muted)] opacity-50 mx-auto mb-4" />
                        <h4 class="text-base font-semibold text-[var(--text-main)] m-0 mb-1">Papan Kosong</h4>
                        <p class="text-sm text-[var(--text-muted)] m-0">Belum ada pengumuman terbaru saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
