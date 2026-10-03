// ══════════════════════════════════════════════════════════════════════
// PERFORMANCE ESTOICA — Gerenciador de Tema (Claro / Escuro)
// ══════════════════════════════════════════════════════════════════════

(function() {
    'use strict';

    const STORAGE_KEY = 'app_theme';
    const THEME_DARK = 'dark';
    const THEME_LIGHT = 'light';

    /**
     * Obtém o tema armazenado no localStorage ou o padrão (dark)
     */
    function getStoredTheme() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved === THEME_LIGHT || saved === THEME_DARK) {
                return saved;
            }
        } catch (e) {
            console.warn('Não foi possível acessar o localStorage:', e);
        }
        return THEME_DARK;
    }

    /**
     * Atualiza o visual de todos os botões de alternância de tema
     */
    function updateToggleButtons(theme) {
        const buttons = document.querySelectorAll('.btn-theme-toggle, #btnThemeToggle');
        const isLight = theme === THEME_LIGHT;

        buttons.forEach(btn => {
            const sunIcon = btn.querySelector('.theme-icon-sun');
            const moonIcon = btn.querySelector('.theme-icon-moon');
            const label = btn.querySelector('.theme-toggle-label');

            if (sunIcon && moonIcon) {
                if (isLight) {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'inline-flex';
                } else {
                    sunIcon.style.display = 'inline-flex';
                    moonIcon.style.display = 'none';
                }
            }

            if (label) {
                label.textContent = isLight ? 'Modo Escuro' : 'Modo Claro';
            }

            btn.setAttribute('aria-pressed', isLight ? 'true' : 'false');
            btn.setAttribute('title', isLight ? 'Alternar para Modo Escuro' : 'Alternar para Modo Claro');
        });
    }

    /**
     * Aplica o tema na tag <html> e <body> e notifica ouvintes
     */
    function applyTheme(theme, animate = false) {
        const root = document.documentElement;
        const body = document.body;

        if (animate) {
            root.classList.add('theme-transitioning');
        }

        root.setAttribute('data-theme', theme);
        if (body) {
            body.setAttribute('data-theme', theme);
        }

        if (theme === THEME_LIGHT) {
            root.classList.remove('dark-theme');
            root.classList.add('light-theme');
            if (body) {
                body.classList.remove('dark-theme');
                body.classList.add('light-theme');
            }
        } else {
            root.classList.remove('light-theme');
            root.classList.add('dark-theme');
            if (body) {
                body.classList.remove('light-theme');
                body.classList.add('dark-theme');
            }
        }

        updateToggleButtons(theme);

        if (animate) {
            window.clearTimeout(window.__themeTransitionTimeout);
            window.__themeTransitionTimeout = window.setTimeout(() => {
                root.classList.remove('theme-transitioning');
            }, 300);
        }

        // Dispara evento customizado para componentes (como gráficos do Chart.js)
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: theme } }));
    }

    /**
     * Alterna entre o tema claro e escuro
     */
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') === THEME_LIGHT ? THEME_LIGHT : THEME_DARK;
        const nextTheme = currentTheme === THEME_LIGHT ? THEME_DARK : THEME_LIGHT;

        try {
            localStorage.setItem(STORAGE_KEY, nextTheme);
        } catch (e) {
            console.warn('Erro ao salvar tema no localStorage:', e);
        }

        applyTheme(nextTheme, true);
    }

    // Aplicação imediata inicial (antes do render completo para evitar FOUC)
    const initialTheme = getStoredTheme();
    document.documentElement.setAttribute('data-theme', initialTheme);
    if (initialTheme === THEME_LIGHT) {
        document.documentElement.classList.add('light-theme');
    } else {
        document.documentElement.classList.add('dark-theme');
    }

    // Configuração após o DOM estar pronto
    function init() {
        applyTheme(getStoredTheme(), false);

        // Delegação de evento para cliques em botões de alternância
        document.addEventListener('click', function(e) {
            const toggleBtn = e.target.closest('.btn-theme-toggle, #btnThemeToggle');
            if (toggleBtn) {
                e.preventDefault();
                toggleTheme();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Exporta objeto global ThemeManager
    window.ThemeManager = {
        getTheme: () => document.documentElement.getAttribute('data-theme') || THEME_DARK,
        setTheme: (theme) => {
            try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
            applyTheme(theme, true);
        },
        toggleTheme: toggleTheme
    };
})();
