import './bootstrap';

import Alpine from 'alpinejs';

// Theme Engine (Dark, Light, System)
window.getAppTheme = function() {
    return localStorage.getItem('labsoal_theme') || 'system';
};

window.setAppTheme = function(theme) {
    if (!['dark', 'light', 'system'].includes(theme)) {
        theme = 'system';
    }
    localStorage.setItem('labsoal_theme', theme);

    let isDark = false;
    if (theme === 'dark') {
        isDark = true;
    } else if (theme === 'light') {
        isDark = false;
    } else {
        isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    if (isDark) {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
    }
    document.documentElement.setAttribute('data-theme', theme);

    window.dispatchEvent(new CustomEvent('app-theme-changed', {
        detail: { theme: theme, isDark: isDark }
    }));
};

// System color scheme change listener
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        const currentTheme = window.getAppTheme();
        if (currentTheme === 'system') {
            if (e.matches) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
            window.dispatchEvent(new CustomEvent('app-theme-changed', {
                detail: { theme: 'system', isDark: e.matches }
            }));
        }
    });
}

window.Alpine = Alpine;

Alpine.start();
