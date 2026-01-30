<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Associado extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'cargo',
        'oab',
        'foto',
        'bio',
        'email',
        'telefone',
        'linkedin',
        'areas_atuacao',
        'ordem',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'areas_atuacao' => 'array',
            'is_active' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    // ========== Scopes ==========

    /**
     * Scope a query to only include active associados.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by 'ordem' field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordem');
    }

    /**
     * Scope a query to filter by cargo (role).
     */
    public function scopeByCargo($query, $cargo)
    {
        return $query->where('cargo', $cargo);
    }
}
