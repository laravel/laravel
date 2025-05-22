<?php
namespace Modules\GutoTradeBot\Entities;


use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;


class Profits extends Model
{
    use ModuleTrait;

    protected $fillable = ['name', 'comment', 'value'];

    public $timestamps = false;

}
