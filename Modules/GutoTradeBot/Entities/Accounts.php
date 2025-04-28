<?php
namespace Modules\GutoTradeBot\Entities;

use App\Traits\UsesModuleConnection;

class Accounts extends Jsons
{
    use UsesModuleConnection;

    protected $table = "accounts";

    protected $fillable = ['bank', 'name', 'number', 'detail', 'is_active', 'data'];

    public $timestamps = false;
    protected $attributes = [
        'data' => '[]',
    ];
}
