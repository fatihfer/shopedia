// Dark mode: class-based (.dark on <html>), persisted in localStorage
(function () {
    const root = document.documentElement;

    function currentTheme() {
        return localStorage.getItem('shopedia-theme')
            || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    }

    function applyTheme(theme) {
        root.classList.toggle('dark', theme === 'dark');
        document.querySelectorAll('[data-theme-icon]').forEach((el) => {
            const isDark = theme === 'dark';
            // sun shown in dark mode, moon shown in light mode
            el.querySelector('[data-icon-sun]')?.classList.toggle('hidden', !isDark);
            el.querySelector('[data-icon-moon]')?.classList.toggle('hidden', isDark);
        });
        const label = theme === 'dark' ? 'Mode terang' : 'Mode gelap';
        document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
            btn.setAttribute('aria-label', label);
            btn.setAttribute('title', label);
        });
    }

    applyTheme(currentTheme());

    document.addEventListener('click', (e) => {
        const toggle = e.target.closest('[data-theme-toggle]');
        if (toggle) {
            const next = root.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('shopedia-theme', next);
            applyTheme(next);
            return;
        }

        const menuBtn = e.target.closest('[data-menu-toggle]');
        if (menuBtn) {
            document.getElementById('mobile-menu')?.classList.toggle('hidden');
            return;
        }

        const toastBtn = e.target.closest('[data-toast-close]');
        if (toastBtn) {
            toastBtn.closest('.toast')?.remove();
            return;
        }
    });

    // Auto-dismiss toasts after 5s
    setTimeout(() => {
        document.querySelectorAll('.toast').forEach((t) => t.remove());
    }, 5000);
})();
