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
use Modules\ZentroTraderBot\Entities\Positions;

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
        $text = "📡 TradingViewController webhook for " . $request["alert"];
        if (isset($request["user"]))
            $text .= " " . $request["user"];
        $text .= ": ";
        Log::info($text, $data);

        $bot = new ZentroTraderBotController("ZentroTraderBot");

        $user_id = $request["user"];
        $wc = new WalletController();

        switch (strtoupper(trim($request["action"]))) {
            // /swap 10 USDC POL
            case "BUY":
                $from = $request["base"];
                $to = $request["currency"];

                $this->openLongPosition($bot, $user_id, $to, $from);

                break;
            // /swap 50 POL USDC
            case "SELL":
                $from = $request["currency"];  // Token que vendes
                $to = $request["base"]; // Token que compras


                $this->closeLongPosition($bot, $user_id, $from, $to);
                break;
            default:
                break;
        }

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

    private function openLongPosition($bot, $userId, $asset, $quote)
    {
        // 2. Configuración de riesgo
        $percent = 10; // compramos el 10% del saldo disponible

        $wc = new WalletController();

        // Obtenemos el saldo DISPONIBLE del token que vamos a gastar
        $balanceData = $wc->getBalance($userId, $quote);

        if (!isset($balanceData['portfolio'])) {
            return response()->json(['status' => 'error', 'message' => 'Error leyendo balance'], 500);
        }

        // Extraer el saldo numérico 
        $networkName = array_key_first($balanceData['portfolio']); // Ej: "Polygon"
        $assets = $balanceData['portfolio'][$networkName]['assets'];
        $balanceAvailable = (float) ($assets[$quote] ?? 0);

        if ($balanceAvailable <= 0) {
            Log::info("⚠️ Saldo insuficiente de $quote.");
            return response()->json(['status' => 'skipped', 'message' => "Sin saldo en $quote"]);
        }

        // Calculamos cuánto vamos a gastar
        $amount = $balanceAvailable * ($percent / 100);

        Log::info("➕ AGREGANDO POSICIÓN (DCA): Invirtiendo $amount $quote");

        // 5. EJECUTAR SWAP

        $privateKey = $wc->getDecryptedPrivateKey($userId);
        $result = $bot->engine->swap($quote, $asset, $amount, $privateKey, true);

        // 6. 💾 GUARDAR "CAPA" EN BD
        // Como 0x no nos dice exacto cuánto compramos en la respuesta simple,
        // haremos una estimación basada en el precio de mercado para rellenar 'amount_out',
        // O mejor aún: Dejamos 'amount_out' en 0 y lo calculamos al cerrar consultando la wallet.
        // Pero para ser ordenados, intentaremos guardar un estimado si es posible, o 0.

        Positions::create([
            'user_id' => $userId,
            'network' => $result['network'],
            'pair' => "$asset/$quote",
            'side' => 'LONG',
            'amount_in' => $amount,
            'amount_out' => $result['amount_received'],
            'tx_hash_open' => $result['tx_hash'],
            'status' => 'OPEN'
        ]);


        // mandarle mensaje directamente al suscriptor
        $bot->TelegramController->sendMessage(
            array(
                "message" => array(
                    "text" => "👉 Completado swap de $amount $asset a $quote...",
                    "chat" => array(
                        "id" => $userId,
                    ),
                ),
            ),
            $bot->token
        );

        return response()->json(['status' => 'success', 'action' => 'DCA ORDER ADDED', 'tx' => $result['tx_hash']]);
    }

    /**
     * CERRAR DCA: Vende TODO lo acumulado para ese par
     */
    private function closeLongPosition($bot, $userId, $asset, $quote)
    {
        // 1. Buscar TODAS las posiciones abiertas de este par (Collection)
        $openPositions = Positions::where('user_id', $userId)
            ->where('pair', "$asset/$quote")
            ->where('status', 'OPEN')
            ->get();

        if ($openPositions->isEmpty()) {
            return response()->json(['status' => 'skipped', 'msg' => 'No open positions to close']);
        }

        // 🧮 SUMAR LO QUE COMPRAMOS
        // Si compramos 10, luego 20 y luego 30, el objetivo es vender 60.
        $targetSellAmount = $openPositions->sum('amount_out');
        if ($targetSellAmount <= 0.00000001) {
            // Seguridad: Si por error la BD dice 0, evitamos intentar un swap de 0.
            return response()->json(['status' => 'error', 'msg' => 'Error: La suma de las posiciones es 0.']);
        }

        Log::info("📉 SEÑAL DE SALIDA: Cerrando " . $openPositions->count() . " órdenes acumuladas: $targetSellAmount $asset");

        // 2. Determinar SALDO TOTAL REAL en Wallet
        // Al final del día, lo que importa es lo que hay en la blockchain, no en la BD.
        $walletCtrl = new WalletController();
        $privKey = $walletCtrl->getDecryptedPrivateKey($userId);

        $balanceData = $walletCtrl->getBalance($userId, $asset);
        $network = array_key_first($balanceData['portfolio']);
        $totalAssetBalance = (float) ($balanceData['portfolio'][$network]['assets'][$asset] ?? 0);

        // 3. 🛡️ CÁLCULO SEGURO DE GAS
        // Vendemos todo lo que hay en la cartera de ese token, respetando la reserva de gas.
        $amountToSell = $this->calculateSafeSellAmount($asset, $totalAssetBalance, $targetSellAmount);

        if ($amountToSell <= 0) {
            return response()->json(['status' => 'error', 'msg' => 'Saldo insuficiente en wallet para vender']);
        }

        // 4. EJECUTAR SWAP DE SALIDA (Venta Masiva)
        $result = $bot->engine->swap($asset, $quote, $amountToSell, $privKey, true);

        // 5. ACTUALIZAR BD (Cerrar todas las fichas)
        // Distribuimos el monto vendido entre las posiciones (proporcional o simplemente cerramos)
        // Para simplificar, marcamos todas como cerradas con el mismo hash de salida.

        foreach ($openPositions as $pos) {
            $pos->update([
                'status' => 'CLOSED',
                'tx_hash_close' => $result['tx_hash'],
            ]);
        }


        // mandarle mensaje directamente al suscriptor
        $bot->TelegramController->sendMessage(
            array(
                "message" => array(
                    "text" => "👉 Completado swap de $amountToSell $asset a $quote...",
                    "chat" => array(
                        "id" => $userId,
                    ),
                ),
            ),
            $bot->token
        );

        return response()->json([
            'status' => 'success',
            'action' => 'CLOSE ALL DCA',
            'closed_count' => $openPositions->count(),
            'tx' => $result['tx_hash']
        ]);
    }

    /**
     * Calcula el monto seguro para vender, reservando Gas si es nativo.
     * @param string $tokenSymbol El token que queremos vender (ej: POL)
     * @param float $balanceTotal El saldo total en la wallet
     * @param float $amountTarget La cantidad que queremos vender (según la BD)
     * @return float La cantidad real que podemos vender
     */
    public function calculateSafeSellAmount(string $tokenSymbol, float $balanceTotal, float $amountTarget)
    {
        $tokenSymbol = strtoupper($tokenSymbol);

        // Lista de Tokens Nativos que se usan para Gas
        $nativeTokens = ['POL', 'MATIC', 'BNB', 'ETH', 'AVAX'];

        // Si NO es un token nativo (ej: vendemos USDT), podemos venderlo todo.
        if (!in_array($tokenSymbol, $nativeTokens)) {
            // Solo verificamos no intentar vender más de lo que tenemos
            return min($balanceTotal, $amountTarget);
        }

        // SI ES NATIVO (POL): Debemos dejar una reserva.
        // Reserva sugerida: 2 POL (o lo que consideres seguro para unas 10 transacciones)
        $gasReserve = 2.0;

        $maxSellable = $balanceTotal - $gasReserve;

        if ($maxSellable <= 0) {
            // Estamos en zona crítica, no se puede vender nada
            return 0.0;
        }

        // Si la BD dice "Vende 10", pero solo tengo "8" libres de gas, vendo "8".
        // Si la BD dice "Vende 10", y tengo "100" libres, vendo "10".
        return min($maxSellable, $amountTarget);
    }
}
