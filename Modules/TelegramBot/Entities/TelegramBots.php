<?php

namespace Modules\TelegramBot\Entities;

use App\Traits\UsesModuleConnection;
use Illuminate\Database\Eloquent\Model;

class TelegramBots extends Model
{
    use UsesModuleConnection;
    protected $table = "bots";

    protected $fillable = ['name', 'token', 'data'];

    protected $casts = [
        'data' => 'json',
    ];

    public $timestamps = false;
}
