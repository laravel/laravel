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

class ZentroTraderBotController extends JsonsController
{
    use UsesTelegramBot;

    public $BingXController;
    public $ApexProController;

    public function __construct($botname, $instance = false)
    {
        $this->ActorsController = new ActorsController();
        $this->TelegramController = new TelegramController();
        $this->BingXController = new BingXController();
        $this->ApexProController = new ApexProController();

        if (!$instance)
            $instance = $botname;
        $response = json_decode($this->TelegramController->getBotInfo($this->getToken($instance)), true);
        $this->telegram = $response["result"];
    }

    public function processMessage()
    {
        $exchanges = TradingSuscriptions::$EXCHANGES;

        $reply = array();

        $array = $this->getCommand($this->message["text"]);

        switch (strtolower($array["command"])) {
            case "/start":
            case "start":
            case "/menu":
            case "menu":
                $reply = $this->mainmenu($this->actor);
                break;
            case "suscribemenu":
                $reply = $this->suscribemenu($this->actor->user_id);
                break;
            case "suscribelevel0":
                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "suscription_level", 0, $this->telegram["username"]);
                $reply = $this->suscribemenu($this->actor->user_id);
                break;
            case "suscribelevel1":
                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "suscription_level", 1, $this->telegram["username"]);
                $reply = $this->suscribemenu($this->actor->user_id);
                break;
            case "suscribelevel2":
                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "suscription_level", 2, $this->telegram["username"]);
                $reply = $this->suscribemenu($this->actor->user_id);
                break;
            case "suscribe":
                $reply["text"] = "🧩 *Exchange selection* \n👉 Choose the exchange to start the subscription wizard:";
                $reply["markup"] = json_encode([
                    'inline_keyboard' => [
                        [
                            ["text" => '🏦 BingX', "callback_data" => 'getbingx'],
                        ],
                        [
                            ["text" => '🦍 ApexPro Mainnet', "callback_data" => 'getapexpromainnet'],
                            ["text" => '🙊 ApexPro Testnet', "callback_data" => 'getapexprotestnet'],
                        ],
                        [
                            ["text" => '🔙 Return to subscribtions menu', "callback_data" => 'suscribemenu'],
                        ],
                    ],
                ]);
                break;
            case "getbingx":
                $reply["text"] = "🏦 *BingX Suscription Wizard* \n_(step 1/2)_\n⌨️ Please provide your *API key*:";
                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "getbingxapikey", $this->telegram["username"]);
                break;

            case "getbingxmenu":
                $reply = $this->getBingXMenu($this->actor->user_id);
                break;

            case "pausebingx":
                $this->pauseExchange($this->actor->user_id, "bingx");
                $reply = $this->getBingXMenu($this->actor->user_id);
                break;
            case "pauseapexprotestnet":
                $this->pauseExchange($this->actor->user_id, "apexprotestnet");
                $reply = $this->getApexProMenu($this->actor->user_id, "apexprotestnet");
                break;
            case "pauseapexpromainnet":
                $this->pauseExchange($this->actor->user_id, "apexpromainnet");
                $reply = $this->getApexProMenu($this->actor->user_id, "apexpromainnet");
                break;

            case "playbingx":
                $this->playExchange($this->actor->user_id, "bingx");
                $reply = $this->getBingXMenu($this->actor->user_id);
                break;
            case "playapexprotestnet":
                $this->playExchange($this->actor->user_id, "apexprotestnet");
                $reply = $this->getApexProMenu($this->actor->user_id, "apexprotestnet");
                break;
            case "playapexpromainnet":
                $this->playExchange($this->actor->user_id, "apexpromainnet");
                $reply = $this->getApexProMenu($this->actor->user_id, "apexpromainnet");
                break;

            case "getapexprotestnet":
                $reply = $this->getApexPro($this->actor->user_id, "apexprotestnet");
                break;
            case "getapexpromainnet":
                $reply = $this->getApexPro($this->actor->user_id, "apexpromainnet");
                break;

            case "getapexprotestnetmenu":
                $reply = $this->getApexProMenu($this->actor->user_id, "apexprotestnet");
                break;
            case "getapexpromainnetmenu":
                $reply = $this->getApexProMenu($this->actor->user_id, "apexpromainnet");
                break;

            case "getapexprotestnetbalance":
                $reply = $this->getApexProBalance($this->actor->user_id, "apexprotestnet");
                break;
            case "getapexpromainnetbalance":
                $reply = $this->getApexProBalance($this->actor->user_id, "apexpromainnet");
                break;

            case "confirmunsuscribebingx":
                $reply = $this->confirmUnsuscribeExchange($this->actor->user_id, "bingx");
                break;
            case "confirmunsuscribeapexprotestnet":
                $reply = $this->confirmUnsuscribeExchange($this->actor->user_id, "apexprotestnet");
                break;
            case "confirmunsuscribeapexpromainnet":
                $reply = $this->confirmUnsuscribeExchange($this->actor->user_id, "apexpromainnet");
                break;

            case "unsuscribe":
                $reply = $this->unsuscribeExchange($this->actor->user_id);
                break;

            case "/help":
            case "help":
                $reply["text"] = "Available commands:\n /help - for assistance \n\n";
                break;
            case "clienturl":
                $uri = str_replace("/" . request()->route()->uri, "/tradingview/client/{$this->actor->user_id}", request()->fullUrl());
                $reply["text"] = "🌎 Your client URL is as follows:\n{$uri}\n\n👆 This is the address you should use in TradingView to notify the bot that you want to work with a custom strategy alert.";
                $reply["markup"] = json_encode([
                    'inline_keyboard' => [
                        [
                            ["text" => '🔙 Return to subscribtions menu', "callback_data" => 'suscribemenu'],
                        ],
                    ],
                ]);
                break;
            case "setbingxbaseordersize":
                $reply = $this->setBingxBaseOrderSize($this->actor->user_id);
                break;
            case "setapexprotestnetbaseordersize":
                $reply = $this->setApexProBaseOrderSize($this->actor->user_id, "apexprotestnet");
                break;
            case "setapexpromainnetbaseordersize":
                $reply = $this->setApexProBaseOrderSize($this->actor->user_id, "apexpromainnet");
                break;
            case "spotbalance":
                $reply = $this->spotbalance($this->actor->user_id);
                break;
            case "adminmenu":
                $reply = $this->adminmenu($this->actor->user_id);
                break;
            case "/users":
                $suscriptors = $this->ActorsController->get(Actors::class, "id", ">", 0);
                foreach ($suscriptors as $suscriptor) {
                    $response = json_decode($this->TelegramController->getUserInfo($suscriptor->user_id, $this->getToken($this->telegram["username"])), true);
                    $text = "👤 ";
                    if (isset($response["result"]["first_name"])) {
                        $text .= $response["result"]["first_name"];
                    }

                    if (isset($response["result"]["last_name"])) {
                        $text .= " " . $response["result"]["last_name"];
                    }

                    if (isset($response["result"]["username"])) {
                        $text .= " \n✉️ @" . $response["result"]["username"];
                    }

                    $text .= " \n🆔 " . $suscriptor->user_id;

                    $balanceoptions = array();

                    $array = $suscriptor->data[$this->telegram["username"]];
                    unset($array["exchanges"]["active"]);
                    foreach ($array["exchanges"] as $id => $exchange) {
                        if ($suscriptor->isReadyForExchange($id)) {
                            array_push($balanceoptions, ["text" => $exchanges[$id]["icon"] . " balance", "callback_data" => "get{$id}balance-" . $suscriptor->user_id]);
                        }
                    }

                    $response = json_decode($this->TelegramController->sendMessage(
                        array(
                            "message" => array(
                                "text" => $text,
                                "chat" => array(
                                    "id" => $this->actor->user_id,
                                ),
                                //"reply_to_message_id" => $replyTo, // responder al mensaje q origino esta interaccion del bot
                                "reply_markup" => json_encode([
                                    'inline_keyboard' => [
                                        $balanceoptions,
                                    ],
                                ]),
                            ),
                        ),
                        $this->getToken($this->telegram["username"])
                    ), true);
                }
                $reply = $this->adminmenu($this->actor->user_id);
                break;
            case "actionmenu":
                $reply = $this->actionmenu($this->actor->user_id);
                break;
            case "actionlevel1":
                $item = Metadatas::where('name', '=', 'app_zentrotraderbot_tradingview_alert_action_level')->first();
                $item->value = 1;
                $item->save();

                $reply = $this->actionmenu($this->actor->user_id);
                break;
            case "actionlevel2":
                $item = Metadatas::where('name', '=', 'app_zentrotraderbot_tradingview_alert_action_level')->first();
                $item->value = 2;
                $item->save();

                $reply = $this->actionmenu($this->actor->user_id);
                break;
            default:
                // podria ser un comando previo pidiendo datos adicionales en modo texto
                $suscriptor = $this->ActorsController->getFirst(Actors::class, 'user_id', '=', $this->actor->user_id);

                $reply["text"] = "🙇🏻 I’m not sure what to reply to “{$array["command"]} {$this->message["text"]}”.\n You can try interacting with this bot using /menu or check out the /help for assistance.";
                if ($suscriptor && $suscriptor->id > 0) {
                    $ismainnet = stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1;
                    switch ($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"]) {
                        case "getbingxapikey":
                            // delete floating prev message
                            $this->deleteLastBotMessage($this->actor->user_id, $suscriptor->data["last_bot_message_id"]);

                            $array = $suscriptor->data[$this->telegram["username"]];
                            $array["exchanges"]["bingx"]["api_key"] = trim("{$array["command"]} {$this->message["text"]}");
                            $array[$this->telegram["username"]]["last_bot_callback_data"] = "getbingxapisecret";
                            $suscriptor->data = $array;
                            $suscriptor->save();

                            $reply["text"] = "🏦 *BingX Suscription Wizard* \n_(step 2/2)_\n⌨️ Now, provide your *API secret*:";
                            break;
                        case "getbingxapisecret":
                            // delete floating prev message
                            $this->deleteLastBotMessage($this->actor->user_id, $suscriptor->data["last_bot_message_id"]);

                            $array = $suscriptor->data[$this->telegram["username"]];
                            $array["exchanges"]["bingx"]["secret_key"] = trim("{$array["command"]} {$this->message["text"]}");
                            $array[$this->telegram["username"]]["last_bot_callback_data"] = "";
                            $suscriptor->data = $array;
                            $suscriptor->save();

                            $response = json_decode($this->BingXController->doRequest(
                                "https",
                                "open-api.bingx.com",
                                "/openApi/spot/v1/user/commissionRate",
                                "GET",
                                $array["exchanges"]["bingx"]["api_key"],
                                $array["exchanges"]["bingx"]["secret_key"],
                                [
                                    "symbol" => "SOL-USDT",
                                ]
                            ), true);
                            if ($response["code"] > 0) { // error!
                                // si no es valida darle mensaje de error y opcion de volver con suscribe o menu principal
                                $reply["text"] = "🔴 *Your subscription to BingX could not be completed*:\n😔 It seems your API Key “" . $array["exchanges"]["bingx"]["api_key"] . "” or API Secret “" . $array["exchanges"]["bingx"]["secret_key"] . "” credentials are incorrect.";
                                $reply["markup"] = json_encode([
                                    'inline_keyboard' => [
                                        [
                                            //try again
                                            ["text" => '🔁 Try to suscribe again', "callback_data" => 'getbingx'],
                                            ["text" => '🔙 Return to subscribtions menu', "callback_data" => 'suscribemenu'],
                                        ],
                                    ],
                                ]);
                            } else { // ok
                                // si es valida activar este exchange, darle mensaje de bienvenido y opcion de volver al menu principal
                                array_push($array["exchanges"]["active"], "bingx");
                                $suscriptor->data = $array;
                                $suscriptor->save();

                                $reply["text"] = "🟢 *Your subscription to BingX has been successfully completed*!\n🎉 Thank you for choosing our services.";
                                $reply["markup"] = json_encode([
                                    'inline_keyboard' => [
                                        [
                                            ["text" => '🔙 Return to subscribtions menu', "callback_data" => 'suscribemenu'],
                                        ],
                                    ],
                                ]);
                            }
                            break;

                        case "bingxbaseordersizeconfirm":
                            // delete floating prev message
                            $this->deleteLastBotMessage($this->actor->user_id, $suscriptor->data["last_bot_message_id"]);

                            $value = trim("{$array["command"]} {$this->message["text"]}");
                            if (is_numeric($value)) {
                                if ($value == 0) {
                                    $value = "0";
                                }

                                $array = $suscriptor->data[$this->telegram["username"]];
                                $array["exchanges"]["bingx"]["base_order_size"] = $value;
                                $array[$this->telegram["username"]]["last_bot_callback_data"] = "";
                                $suscriptor->data = $array;
                                $suscriptor->save();

                                $reply = $this->getBingXMenu($this->actor->user_id);
                            } else {
                                $reply = $this->setBingxBaseOrderSize($this->actor->user_id);
                            }

                            break;

                        case "apexprotestnetbaseordersizeconfirm":
                            $reply = $this->setApexProBaseOrderSizeConfirm($this->actor->user_id, "apexprotestnet", trim("{$array["command"]} {$this->message["text"]}"));
                            break;
                        case "apexpromainnetbaseordersizeconfirm":
                            $reply = $this->setApexProBaseOrderSizeConfirm($this->actor->user_id, "apexpromainnet", trim("{$array["command"]} {$this->message["text"]}"));
                            break;

                        case "getapexprotestnetaccountid":
                        case "getapexpromainnetaccountid":
                            $reply = $this->getApexConfig(
                                $ismainnet ? "apexpromainnet" : "apexprotestnet",
                                $this->actor->user_id,
                                "account_id",
                                trim("{$array["command"]} {$this->message["text"]}"),
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "getapexpromainnetapikey" : "getapexprotestnetapikey",
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "🦍 *ApexPro Mainnet Suscription Wizard* \n_(step 2/7)_\n⌨️ Now, provide your *API Key*:" : "🙊 *ApexPro Testnet Suscription Wizard* \n_(step 2/7)_\n⌨️ Now, provide your *API Key*:",

                            );
                            break;
                        case "getapexprotestnetapikey":
                        case "getapexpromainnetapikey":
                            $reply = $this->getApexConfig(
                                $ismainnet ? "apexpromainnet" : "apexprotestnet",
                                $this->actor->user_id,
                                "api_key",
                                trim("{$array["command"]} {$this->message["text"]}"),
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "getapexpromainnetapikeypassphrase" : "getapexprotestnetapikeypassphrase",
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "🦍 *ApexPro Mainnet Suscription Wizard* \n_(step 3/7)_\n⌨️ Now, provide your *API Key Passphrase*:" : "🙊 *ApexPro Testnet Suscription Wizard* \n_(step 3/7)_\n⌨️ Now, provide your *API Key Passphrase*:",

                            );
                            break;
                        case "getapexprotestnetapikeypassphrase":
                        case "getapexpromainnetapikeypassphrase":
                            $reply = $this->getApexConfig(
                                $ismainnet ? "apexpromainnet" : "apexprotestnet",
                                $this->actor->user_id,
                                "api_key_passphrase",
                                trim("{$array["command"]} {$this->message["text"]}"),
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "getapexpromainnetapikeysecret" : "getapexprotestnetapikeysecret",
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "🦍 *ApexPro Mainnet Suscription Wizard* \n_(step 4/7)_\n⌨️ Now, provide your *API Key Secret*:" : "🙊 *ApexPro Testnet Suscription Wizard* \n_(step 4/7)_\n⌨️ Now, provide your *API Key Secret*:",

                            );
                            break;
                        case "getapexprotestnetapikeysecret":
                        case "getapexpromainnetapikeysecret":
                            $reply = $this->getApexConfig(
                                $ismainnet ? "apexpromainnet" : "apexprotestnet",
                                $this->actor->user_id,
                                "api_key_secret",
                                trim("{$array["command"]} {$this->message["text"]}"),
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "getapexpromainnetstarkkeyprivate" : "getapexprotestnetstarkkeyprivate",
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "🦍 *ApexPro Mainnet Suscription Wizard* \n_(step 5/7)_\n⌨️ Now, provide your *Stark Key Private*:" : "🙊 *ApexPro Testnet Suscription Wizard* \n_(step 5/7)_\n⌨️ Now, provide your *Stark Key Private*:",

                            );
                            break;
                        case "getapexprotestnetstarkkeyprivate":
                        case "getapexpromainnetstarkkeyprivate":
                            $reply = $this->getApexConfig(
                                $ismainnet ? "apexpromainnet" : "apexprotestnet",
                                $this->actor->user_id,
                                "stark_key_private",
                                trim("{$array["command"]} {$this->message["text"]}"),
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "getapexpromainnetstarkkeypublic" : "getapexprotestnetstarkkeypublic",
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "🦍 *ApexPro Mainnet Suscription Wizard* \n_(step 6/7)_\n⌨️ Now, provide your *Stark Key Public*:" : "🙊 *ApexPro Testnet Suscription Wizard* \n_(step 6/7)_\n⌨️ Now, provide your *Stark Key Public*:",

                            );
                            break;
                        case "getapexprotestnetstarkkeypublic":
                        case "getapexpromainnetstarkkeypublic":
                            $reply = $this->getApexConfig(
                                $ismainnet ? "apexpromainnet" : "apexprotestnet",
                                $this->actor->user_id,
                                "stark_key_public",
                                trim("{$array["command"]} {$this->message["text"]}"),
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "getapexpromainnetstarkkeypublickeyycoordinate" : "getapexprotestnetstarkkeypublickeyycoordinate",
                                stripos($suscriptor->data[$this->telegram["username"]]["last_bot_callback_data"], "main") > -1 ? "🦍 *ApexPro Mainnet Suscription Wizard* \n_(step 7/7)_\n⌨️ Now, provide your *Stark Key PublicKeyYCoordinate*:" : "🙊 *ApexPro Testnet Suscription Wizard* \n_(step 7/7)_\n⌨️ Now, provide your *Stark Key PublicKeyYCoordinate*:",

                            );
                            break;
                        case "getapexprotestnetstarkkeypublickeyycoordinate":
                        case "getapexpromainnetstarkkeypublickeyycoordinate":
                            $this->getApexConfig(
                                $ismainnet ? "apexpromainnet" : "apexprotestnet",
                                $this->actor->user_id,
                                "stark_key_public_key_y_coordinate",
                                trim("{$array["command"]} {$this->message["text"]}"),
                            );

                            $suscriptor = $this->ActorsController->getFirst(Actors::class, 'user_id', '=', $this->actor->user_id);
                            $array = $suscriptor->data[$this->telegram["username"]];

                            $response = json_decode(
                                $this->ApexProController->accountBalanceV2ByPass(
                                    $ismainnet ? "mainnet" : "testnet",
                                    $ismainnet ? $array["exchanges"]["apexpromainnet"]["api_key"] : $array["exchanges"]["apexprotestnet"]["api_key"],
                                    $ismainnet ? $array["exchanges"]["apexpromainnet"]["api_key_secret"] : $array["exchanges"]["apexprotestnet"]["api_key_secret"],
                                    $ismainnet ? $array["exchanges"]["apexpromainnet"]["api_key_passphrase"] : $array["exchanges"]["apexprotestnet"]["api_key_passphrase"],
                                    $ismainnet ? $array["exchanges"]["apexpromainnet"]["stark_key_private"] : $array["exchanges"]["apexprotestnet"]["stark_key_private"],
                                    $ismainnet ? $array["exchanges"]["apexpromainnet"]["account_id"] : $array["exchanges"]["apexprotestnet"]["account_id"],
                                ),
                                true
                            );

                            if ($response["code"] > 0) { // error!
                                // si no es valida darle mensaje de error y opcion de volver con suscribe o menu principal
                                $reply["text"] = "🔴 *Your subscription to ApexPro could not be completed*:\n😔 It seems your credentials are incorrect.";
                                $reply["markup"] = json_encode([
                                    'inline_keyboard' => [
                                        [
                                            //try again
                                            ["text" => '🔁 Try to suscribe again', "callback_data" => 'getapexprotestnet'],
                                            ["text" => '🔙 Return to subscribtions menu', "callback_data" => 'suscribemenu'],
                                        ],
                                    ],
                                ]);
                            } else { // ok
                                // si es valida activar este exchange, darle mensaje de bienvenido y opcion de volver al menu principal
                                array_push($array["exchanges"]["active"], $ismainnet ? "apexpromainnet" : "apexprotestnet");
                                $suscriptor->data = $array;
                                $suscriptor->save();

                                $reply["text"] = "🟢 *Your subscription to ApexPro has been completed*!\n🎉 Thank you for choosing our services.";
                                $reply["markup"] = json_encode([
                                    'inline_keyboard' => [
                                        [
                                            ["text" => '🔙 Return to subscribtions menu', "callback_data" => 'suscribemenu'],
                                        ],
                                    ],
                                ]);
                            }
                            break;
                        default:
                            $array = explode("-", strtolower($array["command"]));
                            switch ($array[0]) {
                                case "getapexprotestnetbalance":
                                    $reply = $this->getApexProBalance($array[1], "apexprotestnet");
                                    break;
                                case "getapexpromainnetbalance":
                                    $reply = $this->getApexProBalance($array[1], "apexpromainnet");
                                    break;
                                case "getbingxbalance":
                                    $reply = $this->spotbalance($array[1]);
                                    break;
                                default:
                                    break;
                            }
                            break;
                    }

                }
                break;

        }

        return $reply;
    }

    public function deleteLastBotMessage($user_id, $last_bot_message_id)
    {
        if ($last_bot_message_id && $last_bot_message_id != "") {
            $this->TelegramController->deleteMessage(
                [
                    "message" => [
                        "id" => $last_bot_message_id,
                        "chat" => array(
                            "id" => $user_id,
                        ),
                    ],
                ],
                $this->getToken($this->telegram["username"])
            );

            $this->ActorsController->updateData(Actors::class, "user_id", $user_id, "last_bot_message_id", "", $this->telegram["username"]);
        }
    }

    public function setApexProBaseOrderSize($user_id, $exchange)
    {
        $exchanges = TradingSuscriptions::$EXCHANGES;

        $this->ActorsController->updateData(Actors::class, "user_id", $user_id, "last_bot_callback_data", "{$exchange}baseordersizeconfirm", $this->telegram["username"]);
        return array(
            "text" => "💵 *" . $exchanges[$exchange]["name"] . " Order size*\n\n_ℹ️ In " . $exchanges[$exchange]["name"] . ", each currency pair has its own minimum order size and a decimal step to increase the quantities. Therefore, the order size used by this bot is the minimum order size of the pair multiplied by the number of times (x) you want to maximize your performance._\n\n⌨️ Define *how many times (x)* you want to multiply the minimum value to open orders:",
        );
    }

    public function setApexProBaseOrderSizeConfirm($suscriptor, $exchange, $value)
    {
        // delete floating prev message
        $this->deleteLastBotMessage($suscriptor->user_id, $suscriptor->data["last_bot_message_id"]);

        if (is_numeric($value)) {
            if ($value == 0) {
                $value = "0";
            }

            $array = $suscriptor->data[$this->telegram["username"]];
            $array["exchanges"][$exchange]["base_order_size"] = $value;
            $array[$this->telegram["username"]]["last_bot_callback_data"] = "";
            $suscriptor->data = $array;
            $suscriptor->save();

            return $this->getApexProMenu($suscriptor, $exchange);
        } else {
            return $this->setApexProBaseOrderSize($suscriptor->user_id, $exchange);
        }

    }

    public function setBingxBaseOrderSize($user_id)
    {
        $this->ActorsController->updateData(Actors::class, "user_id", $user_id, "last_bot_callback_data", "bingxbaseordersizeconfirm", $this->telegram["username"]);
        return array(
            "text" => "💵 *BingX Order size*\n⌨️ Define *how many USDT* you want to use in each of your operations:",
        );
    }

    public function mainmenu($actor)
    {
        $suscriptor = new TradingSuscriptions($actor->toArray());

        $mainmenu = array();
        $exchangesmenu = array();

        array_push($mainmenu, ["text" => '🔔 Subscribtion', "callback_data" => 'suscribemenu']);

        $exchanges = TradingSuscriptions::$EXCHANGES;
        if ($suscriptor) {
            // si el usuario no tiene exchanges es recien suscrito y hay q completar su estructura
            if (!isset($suscriptor->data[$this->telegram["username"]]["exchanges"])) {
                $array = $actor->data;
                $array[$this->telegram["username"]] = TradingSuscriptions::getSuscriptorTemplate();
                $actor->data = $array;
                $actor->save();
                $suscriptor = new TradingSuscriptions($actor->toArray());
            }

            $array = $suscriptor->data[$this->telegram["username"]];
            $active = $array["exchanges"]["active"];
            unset($array["exchanges"]["active"]);
            foreach ($array["exchanges"] as $id => $exchange) {
                if ($suscriptor->isReadyForExchange($id)) {
                    $text = $exchanges[$id]["icon"] . " " . $exchanges[$id]["name"];
                    if (!in_array($id, $active)) {
                        $text .= " ▫️";
                    }

                    array_push($exchangesmenu, ["text" => $text, "callback_data" => "get{$id}menu"]);
                }

            }

            if ($suscriptor->data[$this->telegram["username"]]["admin_level"] > 1) {
                array_push($mainmenu, ["text" => '👮‍♂️ Admin', "callback_data" => 'adminmenu']);
            }

        }

        $reply["text"] = "👋 *Welcome to ZentroTraderBot*!\n\n🤖 I’m your virtual companion. I’m here to assist you.\n👇 Choose wisely, and let’s embark on this adventure together.";
        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                $exchangesmenu,
                $mainmenu,
            ],
        ]);

        return $reply;
    }

    public function suscribemenu($suscriptor)
    {
        $suscription_exchanges_menu = [
            ["text" => '🧩 Exchange selection', "callback_data" => 'suscribe'],
        ];
        $suscription_settings_menu = array();
        $extrainfo = "";
        switch ($suscriptor->data["suscription_level"]) {
            case 1:
                array_push($suscription_settings_menu, ["text" => '🅱️ Level', "callback_data" => 'suscribelevel2']);
                $extrainfo = "🌎 _You are a level 🅱️ subscriber; therefore, you can use the 'Client URL button' to get your TradingView alerts link._\n\n";
                break;
            case 2:
                array_push($suscription_settings_menu, ["text" => '🆎 Level', "callback_data" => 'suscribelevel0']);
                $extrainfo = "🌎 _You are a level 🆎 subscriber; therefore, you can use the 'Client URL button' to get your TradingView alerts link._\n\n";
                break;

            default:
                array_push($suscription_settings_menu, ["text" => '🅰️ Level', "callback_data" => 'suscribelevel1']);
                break;
        }
        $reply = array(
            "text" => "🔔 *Subscribtions menu*\nHere you can adjust your preferences:\n\n_🧩 Using the 'Exchange selection' button you can link your exchanges to auto BUY and SELL.\n\nUsing the 'Level' button, you can switch between 3 levels:\n🅰️ you will only receive signals from the community.\n🅱️ you will only receive your personal alerts.\n🆎 you will receive both community alerts and your personal ones._\n\n{$extrainfo}👇 Choose one of the following options:",
        );
        if ($suscriptor->data["suscription_level"] > 0) {
            array_push($suscription_settings_menu, ["text" => '🌎 Client URL', "callback_data" => 'clienturl']);
        }

        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                $suscription_exchanges_menu,
                $suscription_settings_menu,
                [
                    ["text" => '🔙 Return to main menu', "callback_data" => 'menu'],
                ],
            ],
        ]);

        return $reply;
    }

    public function pauseExchange($suscriptor, $exchange)
    {
        $array = $suscriptor->data[$this->telegram["username"]];
        $key = array_search($exchange, $array["exchanges"]["active"]);
        while ($key !== false) {
            unset($array["exchanges"]["active"][$key]);
            $array["exchanges"]["active"] = array_values($array["exchanges"]["active"]);

            $suscriptor->data = $array;
            $suscriptor->save();
            $key = array_search($exchange, $array["exchanges"]["active"]);
        }

    }

    public function playExchange($suscriptor, $exchange)
    {
        $array = $suscriptor->data[$this->telegram["username"]];
        if (!in_array($exchange, $array["exchanges"]["active"])) {
            array_push($array["exchanges"]["active"], $exchange);
        }

        $suscriptor->data = $array;
        $suscriptor->save();
    }

    public function getBingXMenu($suscriptor)
    {
        $exchanges = TradingSuscriptions::$EXCHANGES;

        $array = $suscriptor->data[$this->telegram["username"]];

        $isactive = isset($array["exchanges"]["active"]) && in_array("bingx", $array["exchanges"]["active"]);

        $main_menu = [
            ["text" => '💵 Balance', "callback_data" => "spotbalance"],
        ];
        $settings_menu = array();
        $extrainfo = "";

        array_push($settings_menu, ["text" => "💲 " . $array["exchanges"]["bingx"]["base_order_size"] . " USDT", "callback_data" => "setbingxbaseordersize"]);

        if ($isactive) {
            array_push($settings_menu, ["text" => '⏸ Pause', "callback_data" => 'pausebingx']);
            $extrainfo = "💲 _" . $array["exchanges"]["bingx"]["base_order_size"] . " USDT: you can adjust the size of base and safety orders that the bot opens in your account. You can use zero to pause order creation and only receive alerts._\n▶️ _You are active on BingX; you can use the 'PAUSE button' to temporarily interrupt the execution of orders on this exchange._\n";
        } else {
            array_push($settings_menu, ["text" => '▶️ Play', "callback_data" => 'playbingx']);
            $extrainfo = "💲 _" . $array["exchanges"]["bingx"]["base_order_size"] . " USDT: you can adjust the size of base and safety orders that the bot opens in your account. You can use zero to pause order creation and only receive alerts._\n⏸ _You have BingX paused; you can use the 'PLAY button' to continue executing orders on this exchange._\n";
        }
        $extrainfo = "{$extrainfo}❌ _You can use the 'Cancel button' to unsuscribe this exchange._\n\n";

        $reply = array(
            "text" => "🏦 *BingX menu*\n\n_💵 Using the 'Balance' you can check the assets you currently have._\n\n{$extrainfo}👇 Select what you want to do next:",
        );
        array_push($settings_menu, ["text" => "❌ Cancel", "callback_data" => "confirmunsuscribebingx"]);

        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                $main_menu,
                $settings_menu,
                [
                    ["text" => '🔙 Return to main menu', "callback_data" => 'menu'],
                ],
            ],
        ]);

        return $reply;
    }

    public function confirmUnsuscribeExchange($suscriptor, $exchange)
    {
        $array = $suscriptor->data[$this->telegram["username"]];
        $array[$this->telegram["username"]]["last_bot_callback_data"] = $exchange;
        $suscriptor->data = $array;
        $suscriptor->save();

        $exchanges = TradingSuscriptions::$EXCHANGES;
        $reply = array(
            "text" => "🤔 *Are you sure* you wish to cancel your subscription to " . $exchanges[$exchange]["name"] . "?",
        );
        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                [
                    ["text" => "🟢 Yes, cancel my subscription", "callback_data" => "unsuscribe"],
                ],
                [
                    ["text" => "🔴 No, take me to the menu", "callback_data" => "get{$exchange}menu"],
                ],
            ],
        ]);

        return $reply;
    }
    public function unsuscribeExchange($suscriptor)
    {
        $array = $suscriptor->data[$this->telegram["username"]];

        $template = TradingSuscriptions::getTemplate();

        $exchange = $array[$this->telegram["username"]]["last_bot_callback_data"];

        $this->pauseExchange($suscriptor, $exchange);

        $array["exchanges"][$exchange] = $template["exchanges"][$exchange];

        $array[$this->telegram["username"]]["last_bot_callback_data"] = "";
        $suscriptor->data = $array;
        $suscriptor->save();

        $exchanges = TradingSuscriptions::$EXCHANGES;
        $reply["text"] = "🟢 *Your subscription to " . $exchanges[$exchange]["name"] . " has been successfully canceled*!\n😔 We’re sorry to see you go, but if you change your mind, we’ll be delighted to welcome you back!";
        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                [
                    ["text" => '👌 Suscribe again', "callback_data" => "get{$exchange}"],
                    ["text" => '🔙 Return to main menu', "callback_data" => 'menu'],
                ],
            ],
        ]);

        return $reply;
    }

    public function adminmenu($suscriptor)
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

    public function spotbalance($user_id)
    {
        $suscriptor = $this->ActorsController->getFirst(Actors::class, 'user_id', '=', $user_id);
        $response = json_decode($this->BingXController->spotAccountQueryAssets($suscriptor->data["exchanges"]["bingx"]["api_key"], $suscriptor->data["exchanges"]["bingx"]["secret_key"], array()), true);

        $reply["text"] = "💵 *SPOT Balance*:\n";
        $count = 0;
        foreach ($response["data"]["balances"] as $asset) {
            if ($asset["free"] > 0 || $asset["locked"] > 0) {
                $reply["text"] = $reply["text"] .
                    "\n✅ *" . (floatval($asset["free"]) + floatval($asset["locked"])) . " " . $asset["asset"] . "*";
                if ($asset["locked"] > 0) {
                    $reply["text"] = $reply["text"] .
                        " 🔐 " . $asset["locked"] . " | ☑️ " . $asset["free"];
                }

                $count++;
            }
        }

        if ($count == 0) {
            $reply["text"] = $reply["text"] . " 0 Assets";
        }

        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                [
                    ["text" => '♻️ Refresh balance', "callback_data" => 'spotbalance'],
                ],
                [
                    ["text" => '🔙 Return to main menu', "callback_data" => 'menu'],
                ],
            ],
        ]);

        return $reply;
    }

    public function actionmenu($suscriptor)
    {
        /*
        Acciones a realizar al recibir alerta desde TradingView [1: alertar en canal, 2: alertar y ejecutar ordenes en CEX]
         */
        $action_settings_menu = [];
        $option = "";
        switch (config('metadata.system.app.tradingview.alert.action.level')) {
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

    public function getApexProBalance($suscriptor, $exchange)
    {
        $network = str_replace("apexpro", "", $exchange);

        $exchanges = TradingSuscriptions::$EXCHANGES;

        $array = $suscriptor->data[$this->telegram["username"]];

        $response = json_decode($this->ApexProController->accountBalanceV2ByPass(
            $network,
            $array["exchanges"][$exchange]["api_key"],
            $array["exchanges"][$exchange]["api_key_secret"],
            $array["exchanges"][$exchange]["api_key_passphrase"],
            $array["exchanges"][$exchange]["stark_key_private"],
            $array["exchanges"][$exchange]["account_id"]
        ), true);

        $reply["text"] = "*" . $exchanges[$exchange]["icon"] . " " . $exchanges[$exchange]["name"] . "* Balance:\n";
        $count = 0;

        $assets = array(
            "usdt" => array(
                "asset" => "USDT",
                "free" => $response["usdtBalance"]["availableBalance"],
                "locked" => $response["usdtBalance"]["totalEquityValue"] - $response["usdtBalance"]["availableBalance"],
            ),
            "usdc" => array(
                "asset" => "USDC",
                "free" => $response["usdcBalance"]["availableBalance"],
                "locked" => $response["usdcBalance"]["totalEquityValue"] - $response["usdcBalance"]["availableBalance"],
            ),
        );
        foreach ($assets as $key => $asset) {
            if ($asset["free"] > 0 || $asset["locked"] > 0) {
                $reply["text"] = $reply["text"] .
                    "\n✅ *" . floatval($asset["free"]) . "*";
                if ($asset["locked"] > 0) {
                    $reply["text"] = $reply["text"] .
                        " 🔐 " . $asset["locked"];
                }

                $reply["text"] = $reply["text"] . " *" . $asset["asset"] . "*";
                $count++;
            }
        }

        if ($count == 0) {
            $reply["text"] = $reply["text"] . " 0 Assets";
        }

        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                [
                    ["text" => "🔃 Refresh", "callback_data" => "get{$exchange}balance"],
                ],
                [
                    ["text" => "🔙 Return to " . $exchanges[$exchange]["name"] . " menu", "callback_data" => "get{$exchange}menu"],
                ],
            ],
        ]);

        return $reply;
    }

    public function getApexPro($user_id, $exchange)
    {
        $exchanges = TradingSuscriptions::$EXCHANGES;

        $reply["text"] = "*" . $exchanges[$exchange]["icon"] . " " . $exchanges[$exchange]["name"] . "  Suscription Wizard* \n_(step 1/7)_\n⌨️ Please provide your *Account ID*:";

        $this->ActorsController->updateData(Actors::class, "user_id", $user_id, "last_bot_callback_data", "get{$exchange}accountid", $this->telegram["username"]);

        return $reply;
    }

    public function getApexProMenu($suscriptor, $exchange)
    {
        $exchanges = TradingSuscriptions::$EXCHANGES;

        $array = $suscriptor->data[$this->telegram["username"]];

        $isactive = isset($array["exchanges"]["active"]) && in_array($exchange, $array["exchanges"]["active"]);

        $main_menu = [
            ["text" => '💵 Balance', "callback_data" => "get{$exchange}balance"],
        ];
        $settings_menu = array();
        $extrainfo = "";

        array_push($settings_menu, ["text" => "💲 x" . $array["exchanges"][$exchange]["base_order_size"], "callback_data" => "set{$exchange}baseordersize"]);

        if ($isactive) {
            array_push($settings_menu, ["text" => '⏸ Pause', "callback_data" => "pause{$exchange}"]);
            $extrainfo = "▶️ _You are active on " . $exchanges[$exchange]["name"] . "; you can use the 'PAUSE button' to temporarily interrupt the execution of orders on this exchange._\n";
        } else {
            array_push($settings_menu, ["text" => '▶️ Play', "callback_data" => "play{$exchange}"]);
            $extrainfo = "⏸ _You have " . $exchanges[$exchange]["name"] . " paused; you can use the 'PLAY button' to continue executing orders on this exchange._\n";
        }
        $extrainfo = "{$extrainfo}❌ _You can use the 'Cancel button' to unsuscribe this exchange._\n\n";

        $reply = array(
            "text" => " *" . $exchanges[$exchange]["icon"] . " " . $exchanges[$exchange]["name"] . " menu*\n\n_💵 Using the 'Balance' you can check the assets you currently have._\n\n{$extrainfo}👇 Select what you want to do next:",
        );
        array_push($settings_menu, ["text" => "❌ Cancel", "callback_data" => "confirmunsuscribe{$exchange}"]);

        $reply["markup"] = json_encode([
            'inline_keyboard' => [
                $main_menu,
                $settings_menu,
                [
                    ["text" => '🔙 Return to main menu', "callback_data" => 'menu'],
                ],
            ],
        ]);

        return $reply;
    }

    public function getApexConfig($exchange, $suscriptor, $config, $value, $last_bot_callback_data = "", $replytext = "")
    {
        $array = $suscriptor->data[$this->telegram["username"]];

        $this->deleteLastBotMessage($suscriptor->user_id, $suscriptor->data["last_bot_message_id"]);

        $array = $suscriptor->data[$this->telegram["username"]];
        $array["exchanges"][$exchange][$config] = trim("{$value}");
        $array[$this->telegram["username"]]["last_bot_callback_data"] = $last_bot_callback_data;
        $suscriptor->data = $array;
        $suscriptor->save();

        $reply = array(
            "text" => $replytext,
        );
        return $reply;
    }
}
