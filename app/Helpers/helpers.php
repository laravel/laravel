<?php

use App\Models\LandingPageContent;

if (!function_exists('landing_content')) {
    /**
     * Get landing page content by key with translation support
     *
     * @param string $key The content key
     * @param string|null $locale The locale (defaults to current app locale)
     * @return string The translated content or empty string if not found
     */
    function landing_content(string $key, ?string $locale = null): string
    {
        static $cache = [];
        
        // Build cache key
        $cacheKey = $key . '_' . ($locale ?? app()->getLocale());
        
        // Return from cache if available
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }
        
        // Fetch from database
        $content = LandingPageContent::byKey($key)->first();
        
        if (!$content) {
            $cache[$cacheKey] = '';
            return '';
        }
        
        $translation = $content->getTranslation($locale);
        $cache[$cacheKey] = $translation ?? '';
        
        return $cache[$cacheKey];
    }
}
