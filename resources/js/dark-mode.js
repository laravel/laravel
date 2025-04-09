/**
 * Dark mode functionality for RTLA v2.0
 */
document.addEventListener('DOMContentLoaded', function() {
    const darkModeEnabled = window.darkModeSettings?.enabled || false;
    
    if (!darkModeEnabled) {
        return;
    }
    
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const htmlElement = document.documentElement;
    
    // Initialize dark mode from saved preference or system preference
    function initDarkMode() {
        const savedTheme = localStorage.getItem('theme');
        
        if (savedTheme) {
            applyTheme(savedTheme);
        } else if (window.darkModeSettings?.default === 'system') {
            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                applyTheme('dark');
            } else {
                applyTheme('light');
            }
            
            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                applyTheme(event.matches ? 'dark' : 'light');
            });
        } else {
            // Apply default theme
            applyTheme(window.darkModeSettings?.default || 'light');
        }
    }
    
    // Apply specified theme
    function applyTheme(theme) {
        if (theme === 'dark') {
            htmlElement.classList.add('dark');
        } else {
            htmlElement.classList.remove('dark');
        }
        
        // Update toggle button if it exists
        if (darkModeToggle) {
            darkModeToggle.setAttribute('aria-checked', theme === 'dark');
        }
        
        // Save theme preference
        localStorage.setItem('theme', theme);
    }
    
    // Toggle between dark and light mode
    function toggleDarkMode() {
        const isDark = htmlElement.classList.contains('dark');
        applyTheme(isDark ? 'light' : 'dark');
        
        // Send theme preference to server if user is logged in
        if (window.userId) {
            fetch('/api/user/preferences/theme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    theme: localStorage.getItem('theme')
                })
            });
        }
    }
    
    // Setup dark mode toggle if present
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleDarkMode);
    }
    
    // Initialize dark mode
    initDarkMode();
});
