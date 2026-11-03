@props(['align' => 'right', 'compact' => false])

<div class="relative inline-block text-left theme-switcher-container"
     x-data="{
        open: false,
        theme: (localStorage.getItem('labsoal_theme') || 'system'),
        isDark: document.documentElement.classList.contains('dark'),
        setTheme(newTheme) {
            this.theme = newTheme;
            if (window.setAppTheme) {
                window.setAppTheme(newTheme);
            }
            this.isDark = document.documentElement.classList.contains('dark');
            this.open = false;
        }
     }"
     @app-theme-changed.window="theme = $event.detail.theme; isDark = $event.detail.isDark;"
     @click.away="open = false"
     @keydown.escape.window="open = false">

    {{-- Trigger Button --}}
    <button type="button"
            @click="open = !open"
            class="flex items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-xl border transition-all duration-200 cursor-pointer select-none group focus:outline-none focus:ring-1 focus:ring-emerald-500/40 theme-switcher-btn"
            style="min-height: 36px;"
            :title="'Tema: ' + (theme === 'dark' ? 'Gelap' : (theme === 'light' ? 'Terang' : 'Sistem'))"
            aria-label="Pilih Tema Tampilan">

        {{-- Dynamic Theme Icon --}}
        <span class="w-4 h-4 flex items-center justify-center flex-shrink-0 text-emerald-400 group-hover:scale-110 transition-transform">
            {{-- Sun Icon (Shown when Light mode) --}}
            <svg x-show="theme === 'light' || (!isDark && theme === 'system')" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>

            {{-- Moon Icon (Shown when Dark mode) --}}
            <svg x-show="theme === 'dark' || (isDark && theme === 'system')" x-cloak class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </span>

        @if(!$compact)
        {{-- Current Theme Label (Responsive) --}}
        <span class="text-xs font-semibold tracking-wide hidden sm:inline-block theme-switcher-label"
              x-text="theme === 'dark' ? 'Dark' : (theme === 'light' ? 'Light' : 'Auto')">
        </span>
        @endif

        {{-- Subtle Chevron --}}
        <svg class="w-3 h-3 text-gray-400 group-hover:text-emerald-400 transition-transform duration-200"
             :class="{ 'rotate-180': open }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Dropdown Popover Menu --}}
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
         class="absolute {{ $align === 'right' ? 'right-0' : 'left-0' }} mt-2 w-44 rounded-2xl shadow-2xl p-1.5 z-50 border backdrop-blur-xl theme-switcher-dropdown"
         role="menu">

        <div class="px-3 py-1.5 text-[10px] uppercase font-bold tracking-wider text-gray-500 theme-switcher-header">
            Pilihan Tema
        </div>

        {{-- Option: Light --}}
        <button type="button"
                @click="setTheme('light')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-colors cursor-pointer group theme-option-btn"
                :class="theme === 'light' ? 'theme-option-active text-emerald-400 font-bold' : 'text-gray-300 hover:text-white'"
                role="menuitem">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Terang (Light)</span>
            </div>
            <span x-show="theme === 'light'" x-cloak class="text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </span>
        </button>

        {{-- Option: Dark --}}
        <button type="button"
                @click="setTheme('dark')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-colors cursor-pointer group theme-option-btn"
                :class="theme === 'dark' ? 'theme-option-active text-emerald-400 font-bold' : 'text-gray-300 hover:text-white'"
                role="menuitem">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <span>Gelap (Dark)</span>
            </div>
            <span x-show="theme === 'dark'" x-cloak class="text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </span>
        </button>

        {{-- Option: System --}}
        <button type="button"
                @click="setTheme('system')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-colors cursor-pointer group theme-option-btn"
                :class="theme === 'system' ? 'theme-option-active text-emerald-400 font-bold' : 'text-gray-300 hover:text-white'"
                role="menuitem">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-sky-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>Sistem (Auto)</span>
            </div>
            <span x-show="theme === 'system'" x-cloak class="text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </span>
        </button>

    </div>
</div>

