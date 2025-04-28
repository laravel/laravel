<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ApexProController extends Controller
{

    private $APEX_BYPASS_URL = "https://apexpro.micalme.com";

    public function createOrderV2ByPass($network, $api_key, $api_secret, $api_passphrase, $stark_key_private, $account_id, $order)
    {
        Log::info("ApexProController createOrderV2ByPass: " . json_encode($order));

        $url = "{$this->APEX_BYPASS_URL}/create-order?network={$network}&apikey={$api_key}&passphrase={$api_passphrase}&secret={$api_secret}&starkkey={$stark_key_private}&accountid={$account_id}"
        . "&symbol=" . $order["symbol"]
        . "&side=" . $order["side"]
        . "&size=" . $order["quantity"]
        . "&price=" . $order["price"]//  Precio en 0 para hacer orden de mercado....   "&price=" . $order["price"];
         . "&x=" . $order["x"];
        //var_dump($url);

        // Realizar la solicitud
        $response = json_decode(
            file_get_contents($url),
            true
        );

        if (!isset($response["code"])) {
            $response["code"] = 0;
        }

        return json_encode($response);

        /*
    {
    "id": "592012239310946929",
    "clientId": "2863084821862547",
    "clientOrderId": "2863084821862547",
    "accountId": "582635608690655601",
    "symbol": "BTC-USDT",
    "side": "BUY",
    "price": "24012.0",
    "averagePrice": "",
    "limitFee": "0.120060",
    "fee": "",
    "liquidateFee": "",
    "triggerPrice": "0.0",
    "size": "0.010",
    "type": "LIMIT",
    "createdAt": 1718983516907,
    "updatedTime": 0,
    "expiresAt": 1721577600000,
    "status": "OPEN",
    "timeInForce": "GOOD_TIL_CANCEL",
    "reduceOnly": false,
    "isPositionTpsl": false,
    "orderId": "592012239310946929",
    "exitType": "",
    "cancelReason": "UNKNOWN_ORDER_CANCEL_REASON",
    "latestMatchFillPrice": "0.0",
    "cumMatchFillSize": "0.000",
    "cumMatchFillValue": "0.0000",
    "cumMatchFillFee": "0.000000",
    "cumSuccessFillSize": "0.000",
    "cumSuccessFillValue": "0.0000",
    "cumSuccessFillFee": "0.000000",
    "triggerPriceType": "MARKET",
    "isOpenTpslOrder": false,
    "isSetOpenTp": false,
    "isSetOpenSl": false,
    "openTpParam": {},
    "openSlParam": {}
    }
     */
    }

    public function accountBalanceV2ByPass($network, $api_key, $api_secret, $api_passphrase, $stark_key_private, $account_id)
    {
        $url = "{$this->APEX_BYPASS_URL}/account-balance?network={$network}&apikey={$api_key}&passphrase={$api_passphrase}&secret={$api_secret}&starkkey={$stark_key_private}&accountid={$account_id}";

        // Realizar la solicitud
        $response = json_decode(
            file_get_contents($url),
            true
        );

        if (!isset($response["code"])) {
            $response["code"] = 0;
        }

        //$response["msg"] = "Msg"; // msg de error

        return json_encode($response);

        /*
    {
    "usdtBalance": {
    "totalEquityValue": "190.8933761009247601032257080078125",
    "availableBalance": "190.336687",
    "initialMargin": "0.556688444036990404129028320312500000",
    "maintenanceMargin": "0.278344222018495202064514160156250000"
    },
    "usdcBalance": {
    "totalEquityValue": "110.000000",
    "availableBalance": "110.000000",
    "initialMargin": "0.000000",
    "maintenanceMargin": "0.000000"
    }
    }
     */
    }

}
