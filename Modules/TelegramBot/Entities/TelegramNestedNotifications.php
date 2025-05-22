<?php

namespace Modules\TelegramBot\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;

class TelegramNestedNotifications extends Model
{
    use ModuleTrait;

    protected $table = "nested_notifications";

    protected $fillable = ['name', 'value'];

    public $timestamps = false;
}
