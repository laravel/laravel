<?php
namespace Modules\GutoTradeBot\Entities;

use App\Traits\UsesModuleConnection;
use Illuminate\Database\Eloquent\Model;
use Modules\GutoTradeBot\Http\Controllers\ProfitsController;

class Profits extends Model
{
    use UsesModuleConnection;

    protected $fillable = ['name', 'comment', 'value'];

    public $timestamps = false;

}
