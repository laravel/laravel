<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'status',
        'price',
        'renewal_date',
        'notes',
        'details',
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'details' => 'array',
        'name' => 'array',
        'notes' => 'array',
    ];

    /**
     * Get the translation for a field in the current locale
     */
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        $value = $this->$field;
        
        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? null;
        }
        
        return $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
