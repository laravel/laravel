<?php
namespace Modules\GutoTradeBot\Entities;

use App\Traits\UsesModuleConnection;
use Illuminate\Database\Eloquent\Model;

class Rates extends Model
{
    use UsesModuleConnection;

    protected $fillable = ['date', 'base', 'coin', 'rate'];

    public $timestamps = false;

}
