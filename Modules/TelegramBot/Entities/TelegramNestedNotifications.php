<?php

namespace Modules\TelegramBot\Entities;

use App\Traits\UsesModuleConnection;
use Illuminate\Database\Eloquent\Model;

class TelegramNestedNotifications extends Model
{
    use UsesModuleConnection;
    protected $table = "nested_notifications";

    protected $fillable = ['name', 'value'];

    public $timestamps = false;
}
