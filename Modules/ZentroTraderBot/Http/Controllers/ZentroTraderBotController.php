<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Models\Metadatas;
use Illuminate\Http\Request;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\ZentroTraderBot\Entities\TradingSuscriptions;
use App\Http\Controllers\JsonsController;
use Modules\TelegramBot\Traits\UsesTelegramBot;
use Illuminate\Support\Facades\Log;
use Modules\TelegramBot\Entities\TelegramBots;

class ZentroTraderBotController extends JsonsController
{
    protected $engine;

    use UsesTelegramBot;

    public function __construct($botname, $instance = false)
    {
        $this->engine = new ZeroExController();

        $this->ActorsController = new ActorsController();
        $this->TelegramController = new TelegramController();

        if ($instance === false)
            $instance = $botname;
        $response = false;
        try {
            $bot = $this->getFirst(TelegramBots::class, "name", "=", "@{$instance}");
            $this->token = $bot->token;
            $this->data = $bot->data;

            $response = json_decode($this->TelegramController->getBotInfo($this->token), true);
        } catch (\Throwable $th) {
        }
        if (!$response)
            $response = array(
                "result" => array(
                    "username" => $instance
                )
            );

        $this->telegram = $response["result"];
    }

    public function processMessage()
    {
        $array = $this->getCommand($this->message["text"]);
        //var_dump($array);
        //die;
        //echo strtolower($array["command"]);
        switch (strtolower($array["command"])) {
            case "/start":
                //https://t.me/bot?start=816767995
                // /start 816767995
                $reply = array(
                    "text" => "👋 Hola, bienvenido",
                );
                break;


            case "/jbjknsadkm":
                $wc = new WalletController();
                $privateKey = $wc->getDecryptedPrivateKey($this->actor->user_id);


                // --- CONFIGURACIÓN DE LA PRUEBA ---
                $from = 'USDC';   // Token que vendes
                $to = 'POL';  // Token que compras
                $amount = 0.530563;     // Cantidad a vender (Empieza suave, ej. 2 POL)

                $array = $this->engine->swap($from, $to, $amount, $privateKey, true);
                $reply = array(
                    "text" => "✅ TX Exitosa: " . $array['explorer'],
                );

                /*
                                try {
                                    $txHash = null;
                                    $action = "sell_matic";//$data['action'] ?? '';
                                    $amount = 10;//$data['amount'] ?? 0;

                                    // 2. Decidir qué hacer
                                    switch ($action) {
                                        // TradingView dice "buy_matic" (LONG) -> Usamos USDC para comprar POL
                                        case 'buy_matic':
                                            $txHash = $this->engine->swap('USDC', 'POL', $amount);
                                            break;

                                        // TradingView dice "sell_matic" (SHORT) -> Vendemos POL para tener USDC
                                        case 'sell_matic':

                                            $array = $this->engine->checkPrice('POL', 'USDC', $amount);
                                            var_dump($array);
                                            die;
                                            $txHash = $array["data"];
                                            //$txHash = $this->engine->swap('POL', 'USDC', $amount);
                                            break;

                                        default:
                                            return response()->json(['error' => 'Acción desconocida'], 400);
                                    }

                                    $reply = array(
                                        "text" => "✅ TX Exitosa: $txHash",
                                    );

                                    /*
                                                        return response()->json([
                                                            'status' => 'success',
                                                            'tx_hash' => $txHash,
                                                            'explorer' => "https://polygonscan.com/tx/$txHash"
                                                        ]);
                                                       // cerrar aqui

                                } catch (\Exception $e) {
                                    $reply = array(
                                        "text" => "❌ Error: " . $e->getMessage(),
                                    );
                                }
                */



                break;
            default:
                $reply = array(
                    "text" => "🤷🏻‍♂️ No se que responderle a “{$this->message['text']}”.",
                );
                break;

        }

        return $reply;
    }

}
