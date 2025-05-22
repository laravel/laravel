<?php
namespace Modules\GutoTradeBot\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;

class Rates extends Model
{
    use ModuleTrait;

    protected $fillable = ['date', 'base', 'coin', 'rate'];

    public $timestamps = false;

}
