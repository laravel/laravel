/**
 * إدارة وضع السمة الداكنة/الفاتحة
 */
document.addEventListener('DOMContentLoaded', function() {
    // تحقق من تفضيلات المستخدم أو القيمة الافتراضية
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // تطبيق السمة عند تحميل الصفحة
    applyTheme(currentTheme);
    
    // إعداد مستمع الحدث لزر تبديل السمة
    const themeToggle = document.querySelector('.theme-toggle-btn');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // تطبيق السمة الجديدة
            applyTheme(newTheme);
            
            // حفظ التفضيل عبر AJAX
            saveThemePreference(newTheme);
        });
    }
});

/**
 * تطبيق السمة على المستند
 * @param {string} theme - 'light', 'dark', or 'auto'
 */
function applyTheme(theme) {
    // إذا كانت السمة تلقائية، استخدم تفضيلات النظام
    if (theme === 'auto') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        theme = prefersDark ? 'dark' : 'light';
    }
    
    // تطبيق السمة على عنصر HTML
    document.documentElement.setAttribute('data-theme', theme);
    
    // تحديث أيقونة الزر
    const themeIcon = document.querySelector('.theme-icon');
    if (themeIcon) {
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }
    
    // حفظ في التخزين المحلي للمتصفح
    localStorage.setItem('theme', theme);
}

/**
 * حفظ تفضيل السمة في قاعدة البيانات عبر AJAX
 * @param {string} theme
 */
function saveThemePreference(theme) {
    fetch('/theme/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ theme: theme })
    })
    .then(response => response.json())
    .catch(error => console.error('Error saving theme preference:', error));
}

// استمع إلى تغييرات في تفضيلات النظام إذا كان الوضع تلقائيًا
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
    if (localStorage.getItem('theme') === 'auto') {
        const newTheme = e.matches ? 'dark' : 'light';
        applyTheme(newTheme);
    }
});
