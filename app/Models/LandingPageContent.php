<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageContent extends Model
{
    protected $fillable = [
        'key',
        'value',
        'section',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Get the translation for the current locale or fallback to a specific locale
     */
    public function getTranslation(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (is_array($this->value)) {
            return $this->value[$locale] ?? $this->value['en'] ?? null;
        }
        
        return null;
    }

    /**
     * Scope to filter by section
     */
    public function scopeSection($query, string $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope to get content by key
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }
}
