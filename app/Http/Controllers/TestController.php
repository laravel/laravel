<?php
namespace App\Http\Controllers;

use App\Http\Controllers\GraphsController;
use Carbon\Carbon;
use DOMDocument;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Modules\GutoTradeBot\Entities\Capitals;
use Modules\GutoTradeBot\Entities\Moneys;
use Modules\GutoTradeBot\Entities\Payments;
use Modules\GutoTradeBot\Entities\Profits;
use Modules\GutoTradeBot\Http\Controllers\CapitalsController;
use Modules\GutoTradeBot\Http\Controllers\GutoTradeBotController;
use Modules\GutoTradeBot\Http\Controllers\PaymentsController;
use Modules\GutoTradeBot\Http\Controllers\ProfitsController;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Webklex\IMAP\Facades\Client;
use Modules\TelegramBot\Entities\Actors;
use Modules\GutoTradeBot\Http\Controllers\CoingeckoController;

class TestController extends Controller
{

    public function test(Request $request)
    {
        $bot = new GutoTradeBotController("GutoTradeBot");
        $pc = new PaymentsController();
        $payments = $pc->get(Payments::class, "id", ">", 0);

        $dates = array();
        foreach ($payments as $payment) {
            $array = $payment->data;

            $date = Carbon::createFromFormat("Y-m-d H:i:s", $payment->created_at);

            if (!isset($dates[$date->format("d-m-Y")]))
                $dates[$date->format("d-m-Y")] = CoingeckoController::getHistory("eur", "tether", $date->format("Y-m-d"));

            $array["rate"] = array(
                "internal" => $array["rate"],
                "oracle" => $dates[$date->format("d-m-Y")],
                "receiver" => 0
            );


            $payment->data = $array;
            $payment->save();
        }



        $pc = new CapitalsController();
        $payments = $pc->get(Capitals::class, "id", ">", 0);

        $dates = array();
        foreach ($payments as $payment) {
            $array = $payment->data;

            $date = Carbon::createFromFormat("Y-m-d H:i:s", $payment->created_at);

            if (!isset($dates[$date->format("d-m-Y")]))
                $dates[$date->format("d-m-Y")] = CoingeckoController::getHistory("eur", "tether", $date->format("Y-m-d"));

            $array["rate"] = array(
                "internal" => 1,
                "oracle" => $dates[$date->format("d-m-Y")],
                "receiver" => 1
            );


            $payment->data = $array;
            $payment->save();
        }

        dd($payments->toArray());
        die;

        $amount = 100;
        $reply = $bot->ProfitsController->getSpended($amount);
        dd($reply);
        die;


        $ac = new ActorsController();
        $actor = $ac->getFirst(Actors::class, 'user_id', '=', "816767995");
        $reply = $bot->notifyStats($actor);
        dd($reply);
        die;
    }
}
