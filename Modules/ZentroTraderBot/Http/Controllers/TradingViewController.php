<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Entities\TelegramNestedNotifications;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\ZentroTraderBot\Entities\TradingSuscriptions;

class TradingViewController extends TelegramBotController
{
    /**
     * Recibe la alerta de TradingView.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        /*
                // 1. 🛡️ SEGURIDAD: Verificar la Llave Maestra
                // Comparamos el "secret" de la URL con el que guardaste en el .env
                $mySecret = config('zentrotraderbot.tv_webhook_secret') ?? env('TRADINGVIEW_WEBHOOK_SECRET');
                if ($secret !== $mySecret) {
                    Log::warning("⛔ Intento de acceso no autorizado al Webhook. IP: " . $request->ip());
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
                }
        */
        // 2. 📨 RECIBIR DATOS
        // TradingView envía los datos en formato JSON (body)
        // currency = El activo (ej: MATIC, BNB, BTC) -> {{syminfo.basecurrency}}
        // base     = La contraparte (ej: USDT, USDC)  -> {{syminfo.currency}}
        $data = $request->all();

        if (empty($data)) {
            return response()->json(['status' => 'error', 'message' => 'No data received'], 400);
        }

        // 3. 📝 LOGUEAR (Para ver qué nos llega)
        Log::info("📡 SEÑAL RECIBIDA de TradingView:", $data);

        $bot = new ZentroTraderBotController("ZentroTraderBot");

        // /swap 2.212377 USDC POL
        $amount = 0;
        switch (strtoupper(trim($request["action"]))) {
            case "SELL":
                $from = $request["currency"];  // Token que vendes
                $to = $request["base"]; // Token que compras
                break;
            case "BUY":
                $from = $request["base"];
                $to = $request["currency"];
                break;

            default:
                break;
        }
        $wc = new WalletController();
        $privateKey = $wc->getDecryptedPrivateKey($bot->actor->user_id);
        $array = $bot->engine->swap($from, $to, $amount, $privateKey, true);



        // mandarle mensaje directamente al suscriptor
        $array = array(
            "message" => array(
                "text" => "📡 SEÑAL RECIBIDA de TradingView\n\n" . $request["ticker"] . " | " . $request["action"] . " | " . $request["currency"] . " | " . $request["base"],
                "chat" => array(
                    "id" => "816767995",
                ),
            ),
        );
        $bot->TelegramController->sendMessage($array, $bot->token);

        /*
        $info = [
            "text" => $request["text"],
            "additionalData" => $request["additionalData"],
            "demo" => $request["demo"],
            "user" => $request["user"],
            "alert" => $request["alert"],
        ];

        // Escribiendo en el Log lo recibido para debug
        Log::info("TradingViewController webhook for {$info['alert']}: " . json_encode($info));

        if (isset($info["additionalData"]) && is_array($info["additionalData"])) {

            $payload = [
                "symbol" => $info["additionalData"]["basecurrency"] . "-" . $info["additionalData"]["currency"],
                "side" => strtoupper($info["additionalData"]["action"]),
                "type" => "MARKET",
                "quantity" => floatval($info["additionalData"]["contracts"]),
                "price" => floatval($info["additionalData"]["price"]),
            ];
            // Haciendo q se venda menos del total... para garantizar la venta
            if ($payload["side"] == "SELL") {
                $payload["quantity"] = $payload["quantity"] - ($payload["quantity"] * floatval($info["additionalData"]["commision_percent"]) / 100);
            }

            if (isset($info["demo"])) {
                echo "payload = ";
                var_dump($payload);
            }

            $ac = new ActorsController();

            // verificando el tipo de alerta q estos recibiendo
            switch ($info["alert"]) {
                // esta es la alerta oficial
                case "community":
                    if (config('metadata.system.app.zentrotraderbot.tradingview.alert.action.level') > 1) {
                        // Intentando ejecutar las orden en el CEX para cada suscriptor q espera notificaciones comunitarias
                        $suscriptors = $ac->getData(Actors::class, [
                            [
                                // los suscriptores 1 solo esperan sus propias alertas, por eso los excluimos
                                "contain" => false,
                                "name" => "suscription_level",
                                "value" => 1,
                            ],
                        ], $this->telegram["username"]);
                        $this->createTradeOrders($info, $suscriptors, $payload);
                    }
                    if (config('metadata.system.app.zentrotraderbot.tradingview.alert.action.level') > 0) {
                        // Notificar a los copy traders en el canal de telegram
                        $this->notifyOnTelegram($info, $payload, config('metadata.system.app.zentrotraderbot.telegram.notifications.channel'));
                    }
                    break;
                // cualquier otra la valoro como personal
                default:
                    $suscriptor = $ac->getFirst(Actors::class, 'user_id', '=', $info["user"]);
                    $this->createTradeOrders($info, [$suscriptor], $payload);
                    break;
            }

        }
        */

        // 4. ✅ RESPONDER RÁPIDO
        // TradingView necesita un 200 OK rápido o marcará error.
        // Aquí luego pondremos la lógica de compra/venta.
        // Envía una respuesta al servidor de TradingView para confirmar la recepción
        return response()->json(['status' => 'success', 'message' => 'OK']);
    }

    private function createTradeOrders($info, $suscriptors, $payload)
    {
        $bc = new BingXController();
        $ac = new ApexProController();
        $tc = new TelegramController();

        // Intentando ejecutar las orden en el CEX para cada suscriptor
        for ($i = 0; $i < count($suscriptors); $i++) {
            $response = [];
            if (isset($info["demo"])) {
                if (strtolower($info["demo"]["result"]) == "ok")
                //OK: string(292) "{"code":0,"msg":"","debugMsg":"","data":{"symbol":"SOL-USDT","orderId":1770113650711855104,"transactTime":1710862956108,"price":"183.03","stopPrice":"0","origQty":"0.03","executedQty":"0.03","cummulativeQuoteQty":"5.490834","status":"FILLED","type":"MARKET","side":"SELL","clientOrderID":""}}"
                {
                    $response = [
                        "code" => 0,
                        "msg" => "",
                        "debugMsg" => "",
                        "data" => [
                            "symbol" => "SOL-USDT",
                            "orderId" => 1770113650711855104,
                            "transactTime" => 1710862956108,
                            "price" => "183.03",
                            "stopPrice" => "0",
                            "origQty" => "0.03",
                            "executedQty" => "0.03",
                            "cummulativeQuoteQty" => "5.490834",
                            "status" => "FILLED",
                            "type" => "MARKET",
                            "side" => "SELL",
                            "clientOrderID" => "",
                        ],
                    ];
                } else
                // ERROR: string(164) "{"code":100400,"msg":" check market entrust volume fail, entrust volume to low, userID: 1073074124988538881, minVolume:0.0272, entrustVolume: 0.0100","debugMsg":""}"
                {
                    $response = [
                        "code" => 100400,
                        "msg" => " check market entrust volume fail, entrust volume to low, userID: 1073074124988538881, minVolume:0.0272, entrustVolume: 0.0100",
                        "debugMsg" => "",
                    ];
                }
            } else {

                $ticket = "🎫";
                if ($payload["side"] == "SELL") {
                    $ticket = "🎟";
                }

                if (isset($suscriptors[$i]->data["exchanges"]) && isset($suscriptors[$i]->data["exchanges"]["active"])) {
                    $exchanges = TradingSuscriptions::$EXCHANGES;

                    foreach ($suscriptors[$i]->data["exchanges"]["active"] as $exchange) {
                        if ($suscriptors[$i]->isReadyForExchange($exchange)) {
                            // hay q excluir los suscriptores q han puesto base_order_size en 0 es porq no desean q se ejecuten ordenes
                            if (floatval($suscriptors[$i]->data["exchanges"][$exchange]["base_order_size"]) == 0) {
                                $this->notifyOnTelegram($info, $payload, $info["user"]);
                            } else {
                                $text = "";

                                switch ($exchange) {
                                    case "bingx":
                                        // Ajustar la cantidad correspondiente a cada suscriptor, tomando su cantidad y asumiendo q TV manda a comprar 10 USDT
                                        $payload["quantity"] = $payload["quantity"] * floatval($suscriptors[$i]->data["exchanges"][$exchange]["base_order_size"]) / 10;
                                        try {
                                            $response = json_decode($bc->spotTradeCreateOrder($suscriptors[$i]->data["exchanges"]["bingx"]["api_key"], $suscriptors[$i]->data["exchanges"]["bingx"]["secret_key"], $payload), true);
                                            Log::info("TradingViewController createTradeOrders {$exchange} after response: " . json_encode([
                                                "suscriptor" => $suscriptors[$i]->user_id,
                                                "payload" => $payload,
                                                "response" => $response,
                                            ]));
                                        } catch (\Throwable $th) {
                                            Log::error("TradingViewController createTradeOrders {$exchange} error: " . $th->getTraceAsString());
                                            $response["code"] = 1;
                                            $response["msg"] = $th->getMessage();
                                        }
                                        if ($response["code"] > 0) { // error al ejecutar la orden
                                            $text = "🔴 *ERROR creating trade order in " . $exchanges[$exchange]["icon"] . " " . $exchanges[$exchange]["name"] . "*:\n🔔 _{$info['text']}_\n\n{$ticket} *Order*: " . json_encode($payload) . "\n\n🐞 *Code*: {$response["code"]}\n🙇🏻 *Response*:{$response["msg"]}";
                                        } else {
                                            $orderId = (string) number_format($response["data"]["orderId"], 0, '', '');
                                            $text = "🟢 *Order {$orderId}* " . $exchanges[$exchange]["icon"] . "\n🔔 _{$info['text']}_\n\n{$ticket} *Order*: " . json_encode($payload) . "\n\n💵 *Price*: {$response["data"]["price"]}\n✔️ *origQty*: {$response["data"]["origQty"]}\n👉 *executedQty*: {$response["data"]["executedQty"]}\n📦 *cummulativeQuoteQty*: {$response["data"]["cummulativeQuoteQty"]}\n✅ *status*: {$response["data"]["status"]}";
                                        }
                                        break;
                                    case "apexpromainnet":
                                    case "apexprotestnet":
                                        // Ajustar la cantidad correspondiente a cada suscriptor, tomando su x5 para potenciar el minimo q se puede comprar en ese PAR
                                        $payload["x"] = floatval($suscriptors[$i]->data["exchanges"][$exchange]["base_order_size"]);
                                        try {
                                            $response = json_decode(
                                                $ac->createOrderV2ByPass(
                                                    str_replace("apexpro", "", strtolower($exchange)),
                                                    $suscriptors[$i]->data["exchanges"][$exchange]["api_key"],
                                                    $suscriptors[$i]->data["exchanges"][$exchange]["api_key_secret"],
                                                    $suscriptors[$i]->data["exchanges"][$exchange]["api_key_passphrase"],
                                                    $suscriptors[$i]->data["exchanges"][$exchange]["stark_key_private"],
                                                    $suscriptors[$i]->data["exchanges"][$exchange]["account_id"],
                                                    $payload
                                                ),
                                                true
                                            );

                                            Log::info("TradingViewController createTradeOrders {$exchange} after response: " . json_encode([
                                                "suscriptor" => $suscriptors[$i]->user_id,
                                                "payload" => $payload,
                                                "response" => $response,
                                            ]));
                                        } catch (\Throwable $th) {
                                            Log::error("TradingViewController createTradeOrders {$exchange} error: " . $th->getTraceAsString());
                                            $response["code"] = 1;
                                            $response["message"] = $th->getMessage();
                                        }
                                        if ($response["code"] > 0) { // error al ejecutar la orden
                                            $text = "🔴 *ERROR creating trade order in " . $exchanges[$exchange]["icon"] . " " . $exchanges[$exchange]["name"] . "*:\n🔔 _{$info['text']}_\n\n{$ticket} *Order*: " . json_encode($payload) . "\n\n🐞 *Code*: {$response["code"]}\n🙇🏻 *Response*:{$response["message"]}";
                                        } else {
                                            $text = "🟢 *Order " . $response["orderId"] . "* " . $exchanges[$exchange]["icon"] . "\n🔔 _{$info['text']}_\n\n{$ticket} *Order*: " . json_encode($payload) .
                                                "\n\n📦 *Quantity*: {$response["size"]}\n💵 *Price*: {$response["price"]}";

                                            /*
                                        {
                                        "id":"599303150579483249",
                                        "clientId":"5608955904173432",
                                        "clientOrderId":"5608955904173432",
                                        "accountId":"582635608690655601",
                                        "symbol":"SOL-USDT",
                                        "side":"SELL",
                                        "price":"138.240",
                                        "averagePrice":"",
                                        "limitFee":"0.006912",
                                        "fee":"",
                                        "liquidateFee":"",
                                        "triggerPrice":"0.000",
                                        "size":"0.1",
                                        "type":"MARKET",
                                        "createdAt":1720721805612,
                                        "updatedTime":0,
                                        "expiresAt":1723316400000,
                                        "status":"PENDING",
                                        "timeInForce":"IMMEDIATE_OR_CANCEL",
                                        "reduceOnly":false,"isPositionTpsl":false,
                                        "orderId":"599303150579483249","exitType":"","cancelReason":"UNKNOWN_ORDER_CANCEL_REASON",
                                        "latestMatchFillPrice":"0.000","cumMatchFillSize":"0.0",
                                        "cumMatchFillValue":"0.0000","cumMatchFillFee":"0.000000",
                                        "cumSuccessFillSize":"0.0","cumSuccessFillValue":"0.0000","cumSuccessFillFee":"0.000000",
                                        "triggerPriceType":"MARKET","isOpenTpslOrder":false,"isSetOpenTp":false,"isSetOpenSl":false,
                                        "openTpParam":[],"openSlParam":[],"code":0
                                        }
                                         */
                                        }
                                        break;

                                    default:
                                        break;
                                }

                                // mandarle mensaje directamente al suscriptor
                                $array = array(
                                    "demo" => $info["demo"],
                                    "message" => array(
                                        "text" => $text,
                                        "chat" => array(
                                            "id" => $suscriptors[$i]->user_id,
                                        ),
                                    ),
                                );
                                $tc->sendMessage($array, $this->getToken($this->telegram["username"]));

                            }
                        }
                    }

                }
            }

        }
    }

    private function notifyOnTelegram($info, $payload, $chat_id)
    {
        $tc = new TelegramController();

        // Notificar a los copy traders en el canal de telegram
        // Obtén el ID del mensaje anterior correspondiente a la moneda actual
        $messageId = "";
        $notification_id = $this->telegram["username"] . "|" . $chat_id . "|" . $payload["symbol"];
        $notification = TelegramNestedNotifications::where('name', '=', $notification_id)->first();
        if ($notification && $notification->id > 0) {
            $messageId = $notification->value;
        } else {
            // creamos el par de notificaciones
            TelegramNestedNotifications::create([
                'name' => $notification_id,
                'value' => "",
            ]);
            $notification = TelegramNestedNotifications::where('name', '=', $notification_id)->first();
        }

        $array = array(
            "demo" => $info["demo"],
            "message" => array(
                "text" => $this->getText4Signal($info), // Obteniendo el texto en base a la señal recibida de TV
                "chat" => array(
                    "id" => $chat_id,
                ),
                "reply_to_message_id" => $messageId,
            ),
        );

        $response = json_decode($tc->sendMessage($array, $this->getToken($this->telegram["username"])), true);
        if (isset($response["result"])) {
            // Actualiza el ID del mensaje para la moneda actual en el objeto messageIds
            $newId = $response["result"]["message_id"];
            if ($payload["side"] == "SELL") {
                $newId = "";
            }

            $notification->value = $newId;
            $notification->save();
        }
    }

    public function getText4Signal($array)
    {
        if ($array["additionalData"] && $array["additionalData"]["action"]) {
            try {
                $actionicon = "🟥"; //🔴🟢
                $charticon = "📈";

                $position = "";

                $action = strtoupper($array["additionalData"]["action"]);
                if ($action == "BUY") {
                    $actionicon = "🟩";
                    $charticon = "📉";

                    $position = "📦 *Position size*: " . $array["additionalData"]["position_size"];
                }

                $text = $actionicon . " *Alert for " . strtoupper($array["additionalData"]["ticker"]) . "!*";

                $subtitle = $array["text"];
                if ($subtitle != "") {
                    $text = $text . "\n" . $subtitle;
                }

                $text = $text . "\n\n💵 *" . strtoupper($array["additionalData"]["action"]) . "*: " . $array["additionalData"]["contracts"] . " " . $array["additionalData"]["basecurrency"] .
                    "\n" . $charticon . " *Price*: " . $array["additionalData"]["price"] . " " . $array["additionalData"]["currency"];
                if ($position != "") {
                    $text = $text . "\n" . $position;
                }

                return $text;

            } catch (\Throwable $err) {

                return "Error creating text from " . json_encode($array);
            }
        }

        if ($array["text"]) {
            return $array["text"];
        }

        return "NO message";
    }
}
