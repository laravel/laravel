<?php

namespace Modules\TelegramBot\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;

class TelegramBots extends Model
{
    use ModuleTrait;

    protected $table = "bots";

    protected $fillable = ['name', 'token', 'data'];

    protected $casts = [
        'data' => 'json',
    ];

    public $timestamps = false;
}
