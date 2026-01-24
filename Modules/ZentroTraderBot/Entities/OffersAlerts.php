<?php

namespace Modules\ZentroTraderBot\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModuleTrait;

class OffersAlerts extends Model
{
    use ModuleTrait;

    protected $table = 'offers_alerts';
    protected $guarded = [];
}
