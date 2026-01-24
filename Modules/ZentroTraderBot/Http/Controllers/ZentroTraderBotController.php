<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

use App\Models\Metadatas;
use Illuminate\Http\Request;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\ZentroTraderBot\Entities\Suscriptions;
use App\Http\Controllers\JsonsController;
use Modules\TelegramBot\Traits\UsesTelegramBot;
use Illuminate\Support\Facades\Log;
use Modules\TelegramBot\Entities\TelegramBots;
use Illuminate\Support\Facades\Lang;

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
        $array = $this->getCommand($this->message["text"]);
        $suscriptor = Suscriptions::where("user_id", $this->actor->user_id)->first();

        $this->strategies["suscribemenu"] = function () use ($suscriptor) {
            return $this->suscribeMenu($suscriptor);
        };
        $this->strategies["suscribelevel0"] =
            $this->strategies["suscribelevel1"] =
            $this->strategies["suscribelevel2"] =
            function () use ($suscriptor, $array) {
                return $this->suscribeMenu(
                    $suscriptor,
                    str_replace("suscribelevel", "", strtolower($array["command"]))
                );
            };

        $this->strategies["actionmenu"] = function () {
            if ($this->actor->isLevel(1, $this->telegram["username"]))
                return $this->actionMenu();
            return $this->mainMenu($this->actor);
        };
        $this->strategies["actionlevel1"] =
            $this->strategies["actionlevel2"] =
            function () use ($array) {
                return $this->actionMenu(
                    str_replace("actionlevel", "", strtolower($array["command"]))
                );
            };

        $this->strategies["clienturl"] =
            function () {
                $reply = [
                    "text" => "",
                ];

                $uri = str_replace("telegram/bot/ZentroTraderBot", "tradingview/client/{$this->actor->user_id}", request()->fullUrl());
                $reply["text"] = "🌎 " . Lang::get("zentrotraderbot::bot.prompts.clienturl.header") . ":\n{$uri}\n\n👆 " . Lang::get("zentrotraderbot::bot.prompts.clienturl.warning") . ".";
                $reply["markup"] = json_encode([
                    "inline_keyboard" => [
                        [
                            ["text" => "🔙 " . Lang::get("zentrotraderbot::bot.options.backtosuscribemenu"), "callback_data" => "suscribemenu"],
                        ],
                    ],
                ]);

                return $reply;
            };

        // /swap 5 POL USDC
        $this->strategies["/swap"] =
            function () use ($array) {
                $wc = new TraderWalletController();
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
                    "text" => "✅ " . Lang::get("zentrotraderbot::bot.prompts.txsuccess") . ": " . $array["explorer"],
                );

                return $reply;
            };

        // /balance POL
        $this->strategies["/balance"] =
            function () use ($array) {
                $reply = [
                    "text" => "",
                ];

                $wc = new TraderWalletController();

                try {
                    $result = array();
                    if (isset($array["pieces"][1]))
                        $result = $wc->getBalance($this->actor->user_id, $array["pieces"][1]);
                    else
                        $result = $wc->getBalance($this->actor->user_id);

                    $text = "🫆 `" . $result["address"] . "`\n";
                    foreach ($result["portfolio"] as $values) {
                        foreach ($values["assets"] as $token => $balance) {
                            $text .= "💰 " . $balance . " *" . $token . "*\n";
                        }
                    }

                    $reply = array(
                        "text" => $text,
                    );
                } catch (\Exception $e) {
                    $reply = array(
                        "text" => "❌ " . Lang::get("telegrambot::bot.errors.header") . ": " . $e->getMessage(),
                    );
                }

                return $reply;
            };

        // /withdraw POL 0x1aafFCaB3CB8Ec9b207b191C1b2e2EC662486666
        // /withdraw 5 POL 0x1aafFCaB3CB8Ec9b207b191C1b2e2EC662486666
        $this->strategies["/withdraw"] =
            function () use ($array) {
                $reply = [
                    "text" => "",
                ];

                $wc = new TraderWalletController();

                try {
                    $tokenSymbol = $array["pieces"][count($array["pieces"]) - 1];
                    $toAddress = $array["pieces"][count($array["pieces"])];
                    $amount = null;
                    if (count($array["pieces"]) == 3)
                        $amount = $array["pieces"][1];

                    $result = $wc->withdraw($this->actor->user_id, $toAddress, $tokenSymbol, $amount);

                    if (isset($result["explorer"]))
                        $reply = array(
                            "text" => "✅ " . Lang::get("zentrotraderbot::bot.prompts.txsuccess") . ": " . $result["explorer"],
                        );

                    if (isset($result["message"]))
                        $reply = array(
                            "text" => "❌ " . Lang::get("zentrotraderbot::bot.prompts.txfail") . ": " . $result["message"],
                        );

                } catch (\Exception $e) {
                    $reply = array(
                        "text" => "❌ " . Lang::get("telegrambot::bot.errors.header") . ": " . $e->getMessage(),
                    );
                }

                return $reply;
            };

        return $this->getProcessedMessage();
    }

    public function mainMenu($actor)
    {
        $suscriptor = Suscriptions::where("user_id", $actor->user_id)->first();

        $menu = [];
        array_push($menu, [
            ["text" => "🛒 " . Lang::get("zentrotraderbot::bot.options.buyoffer"), "callback_data" => "notimplemented"],
            ["text" => "💰 " . Lang::get("zentrotraderbot::bot.options.selloffer"), "callback_data" => "notimplemented"],
        ]);
        array_push($menu, [
            ["text" => "🔔 " . Lang::get("zentrotraderbot::bot.options.subscribtion"), "callback_data" => "suscribemenu"]
        ]);

        $wallet = array();
        // si el usuario no tiene wallet es recien suscrito y hay q completar su estructura
        if (!isset($suscriptor->data["wallet"])) {
            // crear el suscriptor para poderle generar wallet
            $actor = Actors::where("user_id", $this->actor->user_id)->first();
            $suscriptor = Suscriptions::where("user_id", $this->actor->user_id)->first();
            if (!$suscriptor)
                $suscriptor = new Suscriptions($actor->toArray());
            $suscriptor->data = array();
            $suscriptor->save();

            $wc = new TraderWalletController();
            $wallet = $wc->getWallet($this->actor->user_id);
            if (isset($wallet["address"])) {
                $suscriptor = Suscriptions::where("user_id", $this->actor->user_id)->first();
                $array = $suscriptor->data;
                $array["admin_level"] = 0;
                $array["suscription_level"] = 0;
                $array["last_bot_callback_data"] = 0;
                $suscriptor->data = $array;
                $suscriptor->save();
            }

            $array = $this->AgentsController->getRoleMenu($actor->user_id, 0);
            array_push($array["menu"], [["text" => "❌ " . Lang::get("telegrambot::bot.options.delete"), "callback_data" => "confirmation|deleteuser-{$actor->user_id}|menu"]]);
            $this->notifyUserWithNoRole($actor->user_id, $array);
        } else
            $wallet = $suscriptor->data["wallet"];
        $description = "";
        if (isset($wallet["address"])) {
            //$description = "_" . Lang::get("zentrotraderbot::bot.mainmenu.description") . ":_\n🫆 `" . $wallet["address"] . "`\n\n";
            $description = "_" . Lang::get("zentrotraderbot::bot.mainmenu.description") . ":_\n\n" .
                Lang::get("zentrotraderbot::bot.mainmenu.body") . "\n\n";
        }


        return $this->getMainMenu(
            $suscriptor,
            $menu,
            $description
        );
    }

    public function adminMenu($suscriptor)
    {
        $menu = [];
        array_push($menu, [
            ["text" => "🫡 " . Lang::get("zentrotraderbot::bot.options.actionmenu"), "callback_data" => "suscribemenu"]
        ]);


        return $this->getAdminMenu(
            $suscriptor,
            $menu
        );
    }

    public function actionMenu($action = -1)
    {
        if ($action > -1) {
            $item = Metadatas::where("name", "=", "app_zentrotraderbot_tradingview_alert_action_level")->first();
            $item->value = $action;
            $item->save();
        }

        /*
        Acciones a realizar al recibir alerta desde TradingView [1: alertar en canal, 2: alertar y ejecutar ordenes en DEX]
         */
        $action_settings_menu = [];
        $option = "";
        switch (config("metadata.system.app.zentrotraderbot.tradingview.alert.action.level")) {
            case 1:
                $option = "NOTIFICATIONS";
                array_push($action_settings_menu, ["text" => "💵 " . Lang::get("zentrotraderbot::bot.options.actionlevel2"), "callback_data" => "actionlevel2"]);
                break;
            case 2:
                $option = "EXECUTE ORDERS";
                array_push($action_settings_menu, ["text" => "📣 " . Lang::get("zentrotraderbot::bot.options.actionlevel1"), "callback_data" => "actionlevel1"]);
                break;
            default:
                break;
        }
        $reply = array(
            "text" => "🔔 *" . Lang::get("zentrotraderbot::bot.actionmenu.header") . "*\n\n_" .
                Lang::get("zentrotraderbot::bot.actionmenu.line1") . ":\n" .
                "📣 " . Lang::get("zentrotraderbot::bot.actionmenu.line2") . ".\n" .
                "💵 " . Lang::get("zentrotraderbot::bot.actionmenu.line3") . "._\n\n" .
                "✅ " . Lang::get("zentrotraderbot::bot.actionmenu.line4", ["option" => $option]) . "\n\n" .
                "👇 " . Lang::get("telegrambot::bot.prompts.chooseoneoption") . ":",
        );

        $reply["markup"] = json_encode([
            "inline_keyboard" => [
                $action_settings_menu,
                [
                    ["text" => "↖️ " . Lang::get("telegrambot::bot.options.backtomainmenu"), "callback_data" => "menu"],
                ],
            ],
        ]);

        return $reply;
    }

    public function configMenu($actor)
    {
        return $this->getConfigMenu(
            $actor
        );
    }

    public function suscribeMenu($suscriptor, $level = -1)
    {
        if ($level > -1) {
            $this->ActorsController->updateData(Suscriptions::class, "user_id", $this->actor->user_id, "suscription_level", $level);
            $suscriptor = Suscriptions::where("user_id", $this->actor->user_id)->first();
        }


        $suscription_settings_menu = array();
        $extrainfo = "";
        switch ($suscriptor->data["suscription_level"]) {
            case 1:
            case "1":
                array_push($suscription_settings_menu, [
                    "text" => Lang::get("zentrotraderbot::bot.options.subscribtionlevel", ["icon" => "🅰️", "char" => "A"]),
                    "callback_data" => "suscribelevel0"
                ]);
                array_push($suscription_settings_menu, [
                    "text" => Lang::get("zentrotraderbot::bot.options.subscribtionlevel", ["icon" => "🆎", "char" => "AB"]),
                    "callback_data" => "suscribelevel2"
                ]);
                $extrainfo = "🌎 _" . Lang::get("zentrotraderbot::bot.subscribtionmenu.line6", ["level" => "🅱️"]) . " " .
                    Lang::get("zentrotraderbot::bot.subscribtionmenu.therefore") . "._\n\n";
                break;
            case 2:
            case "2":
                array_push($suscription_settings_menu, [
                    "text" => Lang::get("zentrotraderbot::bot.options.subscribtionlevel", ["icon" => "🅰️", "char" => "A"]),
                    "callback_data" => "suscribelevel0"
                ]);
                array_push($suscription_settings_menu, [
                    "text" => Lang::get("zentrotraderbot::bot.options.subscribtionlevel", ["icon" => "🅱️", "char" => "B"]),
                    "callback_data" => "suscribelevel1"
                ]);
                $extrainfo = "🌎 _" . Lang::get("zentrotraderbot::bot.subscribtionmenu.line6", ["level" => "🆎"]) . " " .
                    Lang::get("zentrotraderbot::bot.subscribtionmenu.therefore") . "._\n\n";
                break;

            default:
                array_push($suscription_settings_menu, [
                    "text" => Lang::get("zentrotraderbot::bot.options.subscribtionlevel", ["icon" => "🅱️", "char" => "B"]),
                    "callback_data" => "suscribelevel1"
                ]);
                array_push($suscription_settings_menu, [
                    "text" => Lang::get("zentrotraderbot::bot.options.subscribtionlevel", ["icon" => "🆎", "char" => "AB"]),
                    "callback_data" => "suscribelevel2"
                ]);
                $extrainfo = "🌎 _" . Lang::get("zentrotraderbot::bot.subscribtionmenu.line6", ["level" => "🅰️"]) . "._\n\n";
                break;
        }
        $reply = array(
            "text" => "🔔 *" . Lang::get("zentrotraderbot::bot.subscribtionmenu.header") . "*\n" .
                Lang::get("zentrotraderbot::bot.subscribtionmenu.line1") . ":\n\n" .
                "🧩 _" . Lang::get("zentrotraderbot::bot.subscribtionmenu.line2") . ":_\n" .
                "🅰️ _" . Lang::get("zentrotraderbot::bot.subscribtionmenu.line3") . "._\n" .
                "🅱️ _" . Lang::get("zentrotraderbot::bot.subscribtionmenu.line4") . "._\n" .
                "🆎 _" . Lang::get("zentrotraderbot::bot.subscribtionmenu.line5") . "._\n\n" .
                $extrainfo .
                "👇 " . Lang::get("telegrambot::bot.prompts.chooseoneoption") . ":",
        );
        if ($suscriptor->data["suscription_level"] > 0) {
            array_push($suscription_settings_menu, ["text" => "🌎 " . Lang::get("zentrotraderbot::bot.options.clienturl"), "callback_data" => "clienturl"]);
        }

        $reply["markup"] = json_encode([
            "inline_keyboard" => [
                $suscription_settings_menu,
                [
                    ["text" => "↖️ " . Lang::get("telegrambot::bot.options.backtomainmenu"), "callback_data" => "menu"],
                ],
            ],
        ]);

        return $reply;
    }

}
