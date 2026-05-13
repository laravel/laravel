<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DnsRecord extends Model
{
    use HasFactory;

    protected $fillable = ['domain_id', 'type', 'name', 'content', 'proxied', 'ttl', 'cloudflare_record_id'];

    protected function casts(): array
    {
        return [
            'proxied' => 'boolean',
            'ttl' => 'integer',
        ];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
