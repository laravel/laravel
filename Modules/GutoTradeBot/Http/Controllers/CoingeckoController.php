<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CoingeckoController extends Controller
{

    public static function getRate($coin = "eur", $base = "tether")
    {
        $rate = array(
            "direct" => 0,
            "inverse" => 0,
        );

        try {
            $url = "https://api.coingecko.com/api/v3/simple/price?ids={$base}&vs_currencies={$coin}";
            $response = file_get_contents($url, false, stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "Content-Type: application/x-www-form-urlencoded",
                    //'content' => $data ? http_build_query($data) : false,
                ],
            ]));
            $array = json_decode($response, true);
            $rate["direct"] = $array["tether"][$coin];
            $rate["inverse"] = 1 / $rate["direct"];

        } catch (\Throwable $th) {
            Log::error("CoingeckoController getRate ERROR CODE {$th->getCode()} line {$th->getLine()}: {$th->getMessage()}");
            //Log::error("GutoTradeBotController TraceAsString: " . $th->getTraceAsString());
        }

        return $rate;

    }
    /*
     * Summary of getHistory
     * @param mixed $coin "eur"
     * @param mixed $base "tether"
     * @param mixed $date "Y-m-d"
     * @return array|array{direct: int, inverse: int}
     */
    public static function getHistory($coin = "eur", $base = "tether", $date = false)
    {
        if (!$date)
            $date = Carbon::now()->format("d-m-Y");
        else
            $date = Carbon::createFromFormat("Y-m-d", "2025-01-01")->format("d-m-Y");

        $rate = array(
            "direct" => 0,
            "inverse" => 0,
        );

        try {
            $url = "https://api.coingecko.com/api/v3/coins/{$base}/history?date={$date}&localization=false";
            $response = file_get_contents($url, false, stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "Content-Type: application/x-www-form-urlencoded",
                    //'content' => $data ? http_build_query($data) : false,
                ],
            ]));
            $array = json_decode($response, true);
            $rate["direct"] = $array["market_data"]["current_price"][$coin];
            $rate["inverse"] = 1 / $rate["direct"];

        } catch (\Throwable $th) {
            Log::error("CoingeckoController getHistory ERROR CODE {$th->getCode()} line {$th->getLine()}: {$th->getMessage()}");
            //Log::error("GutoTradeBotController TraceAsString: " . $th->getTraceAsString());
        }

        return $rate;

    }
}
