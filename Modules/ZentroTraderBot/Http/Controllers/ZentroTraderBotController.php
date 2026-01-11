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
    public $AgentsController;

    public function __construct($botname, $instance = false)
    {
        $this->engine = new ZeroExController();

        $this->ActorsController = new ActorsController();
        $this->TelegramController = new TelegramController();
        $this->AgentsController = new AgentsController();

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
        $reply = [
            "text" => "🙇🏻 No se que responderle a “{$this->message['text']}”.\n Ud puede interactuar con este bot usando /menu o chequee /ayuda para temas de ayuda.",
        ];

        $suscriptor = TradingSuscriptions::where('user_id', $this->actor->user_id)->first();

        $array = $this->getCommand($this->message["text"]);
        //var_dump($array);
        //die("\n");
        //echo strtolower($array["command"]);
        switch (strtolower($array["command"])) {
            case "/start":
            case "start":
            case "/menu":
            case "menu":
                $reply = $this->mainmenu($suscriptor);
                break;
            case "adminmenu":
                $reply = $this->adminmenu();
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

            case "actionmenu":
                $reply = $this->actionmenu();
                break;
            case "actionlevel1":
            case "actionlevel2":
                $reply = $this->actionmenu(
                    str_replace("actionlevel", "", strtolower($array["command"]))
                );
                break;

            case "/users":
            case "getsuscriptors":
                $reply = $this->mainMenu($this->actor);
                if (
                    $this->actor->isLevel(1, $this->telegram["username"]) ||
                    $this->actor->isLevel(4, $this->telegram["username"])
                ) {
                    $reply = $this->AgentsController->findSuscriptors($this, $this->actor);
                }

                break;

            case "/user":
                $reply = $this->mainMenu($this->actor);
                if (
                    $this->actor->isLevel(1, $this->telegram["username"]) ||
                    $this->actor->isLevel(4, $this->telegram["username"])
                ) {
                    $reply = $this->AgentsController->findSuscriptor($this, $array["message"]);
                }

                break;

            case "promote0":
            case "promote1":
            case "promote2":
                $role = str_replace("promote", "", strtolower($array["command"]));
                // promover a rol de GESTOR
                $this->ActorsController->updateData(
                    Actors::class,
                    "user_id",
                    $array["pieces"][1],
                    "admin_level",
                    $role,
                    $this->telegram["username"]
                );
                $this->AgentsController->notifyRoleChange($this, $array["pieces"][1]);
                $reply = $this->AgentsController->notifyAfterModifyRole($this, $array["pieces"][1], $role);
                break;
            case "deleteuser":
                // eliminar un usuario
                $user = $this->ActorsController->getFirst(Actors::class, "user_id", "=", $array["pieces"][1]);
                $user->delete();

                $reply = $this->ActorsController->notifyAfterDelete();
                break;

            case "/usermetadata":
                $reply = $this->ActorsController->getApplyMetadataPrompt($this, "promptusermetadata-" . $array["message"], $this->actor->getBackOptions("✋ Cancelar", $this->telegram["username"], [1]));
                break;


            case "clienturl":
                $uri = str_replace("telegram/bot/ZentroTraderBot", "tradingview/client/{$this->actor->user_id}", request()->fullUrl());
                $reply["text"] = "🌎 Your client URL is as follows:\n{$uri}\n\n👆 This is the address you should use in TradingView to notify the bot that you want to work with a custom strategy alert.";
                $reply["markup"] = json_encode([
                    'inline_keyboard' => [
                        [
                            ["text" => '🔙 Return to subscribtions menu', "callback_data" => 'suscribemenu'],
                        ],
                    ],
                ]);
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
                $array = $this->actor->data;
                if (isset($array[$this->telegram["username"]]["last_bot_callback_data"])) {
                    $array = $this->getCommand($array[$this->telegram["username"]]["last_bot_callback_data"]);
                    switch ($array["command"]) {
                        case "promptusermetadata":
                            // resetear el comando obtenido a traves de la BD
                            $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);

                            if (count($array["pieces"]) == 2) {
                                $message = explode(":", $this->message["text"]);

                                $suscriptor = $this->ActorsController->getFirst(Actors::class, "user_id", "=", $array["pieces"][1]);
                                //$this->getToken($this->telegram["username"])
                                $suscriptordata = $suscriptor->data;
                                if (!isset($suscriptordata[$this->telegram["username"]]["metadatas"]))
                                    $suscriptordata[$this->telegram["username"]]["metadatas"] = array();
                                $suscriptordata[$this->telegram["username"]]["metadatas"][trim($message[0])] = trim($message[1]);

                                $suscriptor->data = $suscriptordata;
                                $suscriptor->save();
                            }

                            $reply = $this->ActorsController->notifyAfterMetadataChange($array["pieces"][1]);
                            break;
                        default:
                            break;
                    }
                }
                break;

        }

        return $reply;
    }

    public function mainmenu($suscriptor)
    {
        $menu = [];
        array_push($menu, [
            ["text" => '🔔 Subscribtion', "callback_data" => 'suscribemenu']
        ]);

        $wallet = array();
        // si el usuario no tiene wallet es recien suscrito y hay q completar su estructura
        if (!isset($suscriptor->data["wallet"])) {
            // crear el suscriptor para poderle generar wallet
            $actor = Actors::where('user_id', $this->actor->user_id)->first();
            $suscriptor = TradingSuscriptions::where('user_id', $this->actor->user_id)->first();
            if (!$suscriptor)
                $suscriptor = new TradingSuscriptions($actor->toArray());
            $suscriptor->data = array();
            $suscriptor->save();

            $wc = new WalletController();
            $wallet = $wc->generateWallet($this->actor->user_id);
            if (isset($wallet["address"])) {
                $suscriptor = TradingSuscriptions::where('user_id', $this->actor->user_id)->first();
                $array = $suscriptor->data;
                $array["admin_level"] = 0;
                $array["suscription_level"] = 0;
                $array["last_bot_callback_data"] = 0;
                $suscriptor->data = $array;
                $suscriptor->save();
            }

            $array = $this->AgentsController->getRoleMenu($actor->user_id, 0);
            array_push($array["menu"], [["text" => "❌ Eliminar", "callback_data" => "confirmation|deleteuser-{$actor->user_id}|menu"]]);
            $this->notifyUserWithNoRole($actor->user_id, $array);
        } else
            $wallet = $suscriptor->data["wallet"];
        $description = "";
        if (isset($wallet["address"]))
            $description = "_Esta es tu wallet personal en este bot:_\n🫆 `" . $wallet["address"] . "`\n\n";

        return $this->getMainMenu(
            $suscriptor,
            $menu,
            $description
        );
    }

    public function adminmenu()
    {
        $reply = array(
            "text" => "👮‍♂️ *Admin menu*!\nHere you can adjust everything:",
        );

        $admin_options_menu = [];
        array_push($admin_options_menu, ["text" => '👣 Suscriptors', "callback_data" => 'getsuscriptors']);
        array_push($admin_options_menu, ["text" => '🫡 Action Level', "callback_data" => 'actionmenu']);

        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                $admin_options_menu,
                [
                    ["text" => '🔙 Return to main menu', "callback_data" => 'menu'],
                ],
            ],
        ]);

        return $reply;
    }

    public function actionmenu($action = -1)
    {
        if ($action > -1) {
            $item = Metadatas::where('name', '=', 'app_zentrotraderbot_tradingview_alert_action_level')->first();
            $item->value = $action;
            $item->save();
        }

        /*
        Acciones a realizar al recibir alerta desde TradingView [1: alertar en canal, 2: alertar y ejecutar ordenes en DEX]
         */
        $action_settings_menu = [];
        $option = "";
        switch (config('metadata.system.app.zentrotraderbot.tradingview.alert.action.level')) {
            case 1:
                $option = "NOTIFICATIONS";
                array_push($action_settings_menu, ["text" => '💵 Execute orders', "callback_data" => 'actionlevel2']);
                break;
            case 2:
                $option = "EXECUTE ORDERS";
                array_push($action_settings_menu, ["text" => '📣 Notifications', "callback_data" => 'actionlevel1']);
                break;
            default:
                break;
        }
        $reply = array(
            "text" => "🔔 *Action menu*!\n\n_Using the 'Action button', you can switch between 2 levels:\n📣 Notifications: When a signal appears, the bot only notifies users using the channel.\n💵 Execute orders: The bot notifies community users and executes the corresponding orders in the exchange._\n\n✅ At this moment option *{$option}* is selected\n\n👇 Choose one of the following options:",
        );

        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                $action_settings_menu,
                [
                    ["text" => '🔙 Return to main menu', "callback_data" => 'menu'],
                ],
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
                array_push($suscription_settings_menu, ["text" => '🅰️ Level', "callback_data" => 'suscribelevel0']);
                array_push($suscription_settings_menu, ["text" => '🆎 Level', "callback_data" => 'suscribelevel2']);
                $extrainfo = "🌎 _You are a level 🅱️ subscriber; therefore, you can use the 'Client URL button' to get your TradingView alerts link._\n\n";
                break;
            case 2:
            case "2":
                array_push($suscription_settings_menu, ["text" => '🅰️ Level', "callback_data" => 'suscribelevel0']);
                array_push($suscription_settings_menu, ["text" => '🅱️ Level', "callback_data" => 'suscribelevel1']);
                $extrainfo = "🌎 _You are a level 🆎 subscriber; therefore, you can use the 'Client URL button' to get your TradingView alerts link._\n\n";
                break;

            default:
                array_push($suscription_settings_menu, ["text" => '🅱️ Level', "callback_data" => 'suscribelevel1']);
                array_push($suscription_settings_menu, ["text" => '🆎 Level', "callback_data" => 'suscribelevel2']);
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
