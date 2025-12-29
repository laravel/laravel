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
                $text = "👋 Hola, bienvenido";
                if (isset($this->message["from"]["username"]) && $this->message["from"]["username"] != "")
                    $text .= " `" . $this->message["from"]["username"] . "`";
                else
                    $text .= " `" . $this->actor->user_id . "`";

                $wc = new WalletController();
                $result = $wc->generateWallet($this->actor->user_id);
                if (isset($result["address"]))
                    $text .= "\n *Esta es tu wallet personal* en este bot: `" . $result["address"] . "`";

                $reply = array(
                    "text" => $text,
                );
                break;

            case "/swap":
                // /swap 5 POL USDC
                $wc = new WalletController();
                $privateKey = $wc->getDecryptedPrivateKey($this->actor->user_id);
                $amount = $array["pieces"][1];     // Cantidad a vender (Empieza suave, ej. 2 POL)
                $from = $array["pieces"][2];   // Token que vendes
                $to = $array["pieces"][3];  // Token que compras

                $array = $this->engine->swap($from, $to, $amount, $privateKey, true);
                $reply = array(
                    "text" => "✅ TX Exitosa: " . $array['explorer'],
                );
                break;

            case "/balance":
                // /balance POL
                $wc = new WalletController();

                try {
                    $result = array();
                    if (isset($array["pieces"][1]))
                        $result = $wc->getBalance($this->actor->user_id, $array["pieces"][1]);
                    else
                        $result = $wc->getBalance($this->actor->user_id);

                    $text = "🫆 `" . $result["address"] . "`\n";
                    foreach ($result["portfolio"] as $network => $values) {
                        foreach ($values["assets"] as $token => $balance) {
                            $text .= "💰 " . $balance . " *" . $token . "*\n";
                        }
                    }

                    $reply = array(
                        "text" => $text,
                    );
                } catch (\Exception $e) {
                    $reply = array(
                        "text" => "❌ Error: " . $e->getMessage(),
                    );
                }
                break;

            case "/withdraw":
                // /withdraw POL 0x1aafFCaB3CB8Ec9b207b191C1b2e2EC662486666
                // /withdraw 5 POL 0x1aafFCaB3CB8Ec9b207b191C1b2e2EC662486666
                $wc = new WalletController();

                try {
                    $tokenSymbol = $array["pieces"][count($array["pieces"]) - 1];
                    $toAddress = $array["pieces"][count($array["pieces"])];
                    $amount = null;
                    if (count($array["pieces"]) == 3)
                        $amount = $array["pieces"][1];

                    $result = $wc->withdraw($this->actor->user_id, $toAddress, $tokenSymbol, $amount);

                    if (isset($result['explorer']))
                        $reply = array(
                            "text" => "✅ TX Exitosa: " . $result['explorer'],
                        );

                    if (isset($result['message']))
                        $reply = array(
                            "text" => "❌ TX Fallida: " . $result['message'],
                        );

                } catch (\Exception $e) {
                    $reply = array(
                        "text" => "❌ Error: " . $e->getMessage(),
                    );
                }
                break;


            case "/jbjknsadkm":


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
