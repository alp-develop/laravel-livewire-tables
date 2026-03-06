/* ═══════════════════════════════════════════════════════════════════════════════
   Demo Page JS — Dark mode init, theme toggle, icon management
   ═══════════════════════════════════════════════════════════════════════════════ */

(function () {
    'use strict';

    // SVG icon constants
    var SUN_ICON = '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/></svg>';
    var MOON_ICON = '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>';

    /**
     * Set the dark-toggle button icon based on current dark state
     */
    function updateIcon(btn) {
        if (!btn) return;
        var isDark = document.documentElement.classList.contains('lt-dark');
        btn.innerHTML = isDark ? SUN_ICON : MOON_ICON;
    }

    /**
     * Toggle dark mode and persist preference
     */
    function toggleDarkMode() {
        var cl = document.documentElement.classList;
        cl.toggle('lt-dark');
        cl.toggle('dark');
        var isDark = cl.contains('lt-dark');
        localStorage.setItem('lt-dark', isDark ? '1' : '0');
        updateIcon(document.querySelector('.lt-dark-toggle'));

        // Notify all Livewire table components so they re-configure
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('dark-mode-changed', { active: isDark });
        }
    }

    // Expose toggle globally for the onclick handler
    window.ltToggleDark = toggleDarkMode;

    // Set correct icon on initial load
    document.addEventListener('DOMContentLoaded', function () {
        updateIcon(document.querySelector('.lt-dark-toggle'));
    });

    // Sync dark mode state to Livewire components once they are ready
    document.addEventListener('livewire:init', function () {
        var isDark = document.documentElement.classList.contains('lt-dark');
        if (isDark) {
            Livewire.dispatch('dark-mode-changed', { active: true });
        }
    });

    // Close language dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.lt-lang-wrap')) {
            document.querySelectorAll('.lt-lang-dropdown.lt-lang-open').forEach(function (el) {
                el.classList.remove('lt-lang-open');
            });
        }
    });

    // Reload page when Livewire fires language-changed so layout re-renders with new locale
    document.addEventListener('language-changed', function () {
        window.location.reload();
    });
})();
