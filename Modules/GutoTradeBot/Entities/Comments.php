<?php

namespace Modules\GutoTradeBot\Entities;

use App\Traits\UsesModuleConnection;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use UsesModuleConnection;

    protected $table = "comments";
    protected $fillable = ['comment', 'screenshot', 'sender_id', 'payment_id', 'data'];

    protected $casts = [
        'data' => 'json',
    ];

    public $timestamps = true;

}
