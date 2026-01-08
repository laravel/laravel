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
    public $engine;

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

        $suscriptor = TradingSuscriptions::where('user_id', $this->actor->user_id)->first();

        $array = $this->getCommand($this->message["text"]);
        //var_dump($array);
        //die;
        //echo strtolower($array["command"]);
        switch (strtolower($array["command"])) {
            case "/start":
            case "start":
            case "/menu":
            case "menu":
                $reply = $this->mainmenu($suscriptor);
                break;
            case "suscribemenu":
                $reply = $this->suscribemenu($suscriptor);
                break;
            case "suscribelevel0":
            case "suscribelevel1":
            case "suscribelevel2":
                $reply = $this->suscribemenu(
                    $suscriptor,
                    str_replace("suscribelevel", "", strtolower($array["command"]))
                );
                break;

            case "/swap":
                // /swap 5 POL USDC
                $wc = new WalletController();
                $privateKey = $wc->getDecryptedPrivateKey($this->actor->user_id);
                $amount = $array["pieces"][1];     // Cantidad a vender (Empieza suave, ej. 2 POL)
                $from = $array["pieces"][2];   // Token que vendes
                $to = $array["pieces"][3];  // Token que compras

                $bot = $this;
                $userId = $this->actor->user_id;
                $array = $this->engine->swap(
                    $from,
                    $to,
                    $amount,
                    $privateKey,
                    function ($text, $autodestroy) use ($bot, $userId) {
                        $bot->TelegramController->sendMessage(
                            array(
                                "message" => array(
                                    "text" => $text,
                                    "chat" => array(
                                        "id" => $userId,
                                    )
                                ),
                            ),
                            $bot->token,
                            $autodestroy
                        );
                    },
                    true
                );
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

            default:
                $reply = array(
                    "text" => "🤷🏻‍♂️ No se que responderle a “{$this->message['text']}”.",
                );
                break;

        }

        return $reply;
    }

    public function mainmenu($suscriptor)
    {
        $mainmenu = array();

        array_push($mainmenu, ["text" => '🔔 Subscribtion', "callback_data" => 'suscribemenu']);

        $wallet = array();
        // si el usuario no tiene wallet es recien suscrito y hay q completar su estructura
        if (!isset($suscriptor->data["wallet"])) {
            // crear el suscriptor para poderle generar wallet
            $actor = Actors::where('user_id', $this->actor->user_id)->first();
            $suscriptor = new TradingSuscriptions($actor->toArray());
            $suscriptor->data = array();
            $suscriptor->save();

            $wc = new WalletController();
            $wallet = $wc->generateWallet($this->actor->user_id);
            if (isset($wallet["address"])) {
                $array = $suscriptor->data;
                $array["admin_level"] = 0;
                $array["suscription_level"] = 0;
                $array["last_bot_callback_data"] = 0;
                $suscriptor->data = $array;
                $suscriptor->save();
            }
        }
        if ($suscriptor->data["admin_level"] > 1) {
            array_push($mainmenu, ["text" => '👮‍♂️ Admin', "callback_data" => 'adminmenu']);
        }

        $text = "👋 Hola, bienvenido";
        if (isset($this->message["from"]["username"]) && $this->message["from"]["username"] != "")
            $text .= " `" . $this->message["from"]["username"] . "`";
        else
            $text .= " `" . $this->actor->user_id . "`";
        if (isset($wallet["address"]))
            $text .= "\n\n🫆 *Esta es tu wallet personal* en este bot: `" . $wallet["address"] . "`";
        $text .= "\n👇 Que desea hacer ahora?";

        $reply["text"] = $text;
        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                $mainmenu,
            ],
        ]);

        return $reply;
    }

    public function suscribemenu($suscriptor, $level = -1)
    {
        if ($level > -1) {
            $this->ActorsController->updateData(TradingSuscriptions::class, "user_id", $this->actor->user_id, "suscription_level", $level);
            $suscriptor = TradingSuscriptions::where('user_id', $this->actor->user_id)->first();
        }


        $suscription_settings_menu = array();
        $extrainfo = "";
        switch ($suscriptor->data["suscription_level"]) {
            case 1:
            case "1":
                array_push($suscription_settings_menu, ["text" => '🆎 Level', "callback_data" => 'suscribelevel2']);
                $extrainfo = "🌎 _You are a level 🅱️ subscriber; therefore, you can use the 'Client URL button' to get your TradingView alerts link._\n\n";
                break;
            case 2:
            case "2":
                array_push($suscription_settings_menu, ["text" => '🅰️ Level', "callback_data" => 'suscribelevel0']);
                $extrainfo = "🌎 _You are a level 🆎 subscriber; therefore, you can use the 'Client URL button' to get your TradingView alerts link._\n\n";
                break;

            default:
                array_push($suscription_settings_menu, ["text" => '🅱️ Level', "callback_data" => 'suscribelevel1']);
                $extrainfo = "🌎 _You are a level 🅰️ subscriber._\n\n";
                break;
        }
        $reply = array(
            "text" => "🔔 *Subscribtions menu*\nHere you can adjust your preferences:\n\n_🧩 Using the 'Level' button, you can switch between 3 levels:\n🅰️ you will only receive signals from the community.\n🅱️ you will only receive your personal alerts.\n🆎 you will receive both community alerts and your personal ones._\n\n{$extrainfo}👇 Choose one of the following options:",
        );
        if ($suscriptor->data["suscription_level"] > 0) {
            array_push($suscription_settings_menu, ["text" => '🌎 Client URL', "callback_data" => 'clienturl']);
        }

        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                $suscription_settings_menu,
                [
                    ["text" => '🔙 Return to main menu', "callback_data" => 'menu'],
                ],
            ],
        ]);

        return $reply;
    }

}
