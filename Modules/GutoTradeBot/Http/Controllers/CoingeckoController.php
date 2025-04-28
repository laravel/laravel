<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\Controller;

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
            $rate["direct"] = $array["tether"]["eur"];
            $rate["inverse"] = 1 / $array["tether"]["eur"];

        } catch (\Throwable $th) {

        }

        return $rate;

    }
}
