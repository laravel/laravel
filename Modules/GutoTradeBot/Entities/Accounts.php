<?php
namespace Modules\GutoTradeBot\Entities;

use App\Traits\ModuleTrait;

class Accounts extends Jsons
{
    use ModuleTrait;
    protected $table = "accounts";

    protected $fillable = ['bank', 'name', 'number', 'detail', 'is_active', 'data'];

    public $timestamps = false;
    protected $attributes = [
        'data' => '[]',
    ];
}
