<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'titulo',
        'slug',
        'resumo',
        'conteudo',
        'imagem_destaque',
        'categoria',
        'tags',
        'status',
        'published_at',
        'views',
        'is_featured',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'views' => 'integer',
        ];
    }

    // ========== Relationships ==========

    /**
     * Get the user (editor) that created this post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user relationship.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ========== Scopes ==========

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'publicado')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include featured posts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('categoria', $category);
    }

    // ========== Accessors & Mutators ==========

    /**
     * Generate slug from title when creating.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->titulo);
            }
        });
    }

    /**
     * Increment view count.
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Publish the post.
     */
    public function publish()
    {
        $this->update([
            'status' => 'publicado',
            'published_at' => now(),
        ]);
    }

    /**
     * Get excerpt from content.
     */
    public function getExcerptAttribute()
    {
        return $this->resumo ?: Str::limit(strip_tags($this->conteudo), 200);
    }
}
