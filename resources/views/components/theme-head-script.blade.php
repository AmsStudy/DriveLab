<script>
    (function() {
        try {
            var theme = localStorage.getItem('labsoal_theme') || 'system';
            var isDark = false;
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
        } catch(e) {}
    })();
</script>

