<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostingAccount extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'domain_id', 'cpanel_username', 'cpanel_password', 'status', 'plan_name', 'server_ip'];

    protected function casts(): array
    {
        return [
            'cpanel_password' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
