// Theme Switcher with Alpine.js
export function initThemeSwitcher() {
    return {
        theme: localStorage.getItem('theme') || 'dark',

        init() {
            this.applyTheme();
        },

        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            this.applyTheme();
            localStorage.setItem('theme', this.theme);
        },

        applyTheme() {
            if (this.theme === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
                document.body.classList.remove('bg-slate-900', 'text-white');
                document.body.classList.add('bg-white', 'text-slate-900');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.body.classList.remove('bg-white', 'text-slate-900');
                document.body.classList.add('bg-slate-900', 'text-white');
            }
        }
    };
}
