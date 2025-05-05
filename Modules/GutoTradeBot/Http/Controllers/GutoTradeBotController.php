<?php
namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\FileController;
use App\Http\Controllers\GraphsController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\JsonsController;
use App\Traits\UsesModuleConnection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\GutoTradeBot\Entities\Accounts;
use Modules\GutoTradeBot\Entities\Capitals;
use Modules\GutoTradeBot\Entities\Moneys;
use Modules\GutoTradeBot\Entities\Payments;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\TelegramBot\Traits\UsesTelegramBot;

class GutoTradeBotController extends JsonsController
{
    use UsesTelegramBot;
    use UsesModuleConnection;

    public $PaymentsController;
    public $CapitalsController;
    public $AccountsController;
    public $CommentsController;
    public $ProfitsController;
    public $AgentsController;
    public $TextController;
    public $PenaltiesController;
    public $CoingeckoController;

    public static $STOLEN_FUNDS = 600;
    public static $NOTIFY_FOR_DEBUG = false;
    public static $NOTIFY_NO_ENOUGH_CAPITAL = false;

    public function __construct($botname, $instance = false)
    {
        $this->ActorsController = new ActorsController();
        $this->TelegramController = new TelegramController();
        $this->TextController = new TextController();
        $this->PenaltiesController = new PenaltiesController();
        $this->AccountsController = new AccountsController();
        $this->CommentsController = new CommentsController();
        $this->PaymentsController = new PaymentsController();
        $this->CapitalsController = new CapitalsController();
        $this->ProfitsController = new ProfitsController();
        $this->AgentsController = new AgentsController();
        $this->CoingeckoController = new CoingeckoController();

        if (!$instance)
            $instance = $botname;
        $response = json_decode($this->TelegramController->getBotInfo($this->getToken($instance)), true);
        $this->telegram = $response["result"];
    }


    public function processMessage()
    {
        $reply = [
            "text" => "🙇🏻 No se que responderle a “{$this->message['text']}”.\n Ud puede interactuar con este bot usando /menu o chequee /ayuda para temas de ayuda.",
        ];
        if (isset($this->message["text"]) && $this->message["text"] != "") {
            $array = $this->getCommand($this->message["text"]);

            switch (strtolower($array["command"])) {
                case "/start":
                case "start":
                    //https://t.me/GutoTradeBot?start=816767995
                    // /start 816767995
                    $reply = $this->mainMenu($this->actor);
                case "/menu":
                case "menu":
                    $reply = $this->mainMenu($this->actor);
                    break;

                case "/help":
                case "help":
                case "/ayuda":
                case "ayuda":
                    $text = "📖 *¿Cómo usar este bot?*.\n_He aquí los principales elementos que debe conocer:_\n\n";
                    $text .= "1️⃣ *Acceder al menú principal*: /menu\n_Escriba “menu” o simplemente cliquee en el comando_\n";
                    $text .= "2️⃣ *Buscar pago*: /buscar\n_Escriba el comando para obtener el asistente de búsquedas. Si conoce el ID del pago puede usar el comando así: /buscar 1234_\n";
                    $text .= "3️⃣ *Establecer zona horaria*: /utc\n_Escriba el comando para obtener el asistente correspondiente. Establecer su zona horaria le mostrará los pagos segun su fecha y hora local_\n\n";
                    $text .= "📚 *Manual de usuario*:\n_Puede encontrar el manual de usuario para REMESADORES aquí:_ [" . request()->root() . "/Bot.pdf]\n\n";
                    $text .= "👮‍♂️ *Términos y condiciones*:\n_Para usar nuestro servicio ud debe ACEPTAR nuestros términos que puede examinar aquí:_ [" . request()->root() . "/TermsAndConditions.pdf]\n*Usar este bot se considera una ACEPTACIÓN IMPLÍCITA*";
                    $reply = [
                        "text" => $text,
                        "markup" => json_encode([
                            "inline_keyboard" => [
                                [
                                    ["text" => "↖️ Ir al menú principal", "callback_data" => "menu"],
                                ],

                            ],
                        ]),
                    ];
                    break;

                case "adminmenu":
                    $reply = $this->adminMenu($this->actor);
                    break;

                case "configmenu":
                    $reply = $this->configMenu($this->actor);
                    break;

                case "/payments":
                    $reply = $this->mainMenu($this->actor);
                    if ($this->actor->isLevel(1, $this->telegram["username"]) || $this->actor->isLevel(4, $this->telegram["username"])) {
                        $reply = $this->PaymentsController->getMenu($this, $this->actor);
                    }
                    break;
                case "/capitals":
                    $reply = $this->mainMenu($this->actor);
                    if ($this->actor->isLevel(1, $this->telegram["username"]) || $this->actor->isLevel(4, $this->telegram["username"])) {
                        $reply = $this->CapitalsController->getMenu($this->actor);
                    }

                    break;

                case "/buscar":
                    if ($array["message"] && strlen($array["message"]) > 1) {

                        $reply = $this->PaymentsController->renderPaymentsByAny(
                            $this,
                            $array["message"],
                            "Reporte de pago encontrado"
                        );
                        /*
                        try {
                            $reply = $this->PaymentsController->renderPaymentsByAny(
                                $this,
                                $array["message"],
                                "Reporte de pago encontrado"
                            );
                        } catch (\Throwable $th) {
                            Log::error("GutoTradeBotController /buscar ERROR CODE {$th->getCode()} line {$th->getLine()}: {$th->getMessage()}");
                            //Log::error("GutoTradeBotController TraceAsString: " . $th->getTraceAsString());

                            $reply = [
                                "text" => "😬 *Ha ocurrido un error {$th->getCode()}*\n_{$th->getMessage()}_",
                            ];
                        }
                        */
                    } else {
                        $reply = $this->notifyShortSearchParameter($this->actor->user_id, $array["message"]);
                    }

                    break;
                case "buscar":
                    $reply = $this->PaymentsController->getSearchPrompt($this, "getpaymentbyvalue", $this->getBackOptions($this->actor, "✋ Cancelar"));
                    break;

                case "promptpaymentdaysold":
                    $reply = $this->PaymentsController->getDaysPrompt($this, "getpaymentbydaysold", $this->getBackOptions($this->actor, "✋ Cancelar"));
                    break;

                case "sendannouncement":
                    $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "getannouncement", $this->telegram["username"]);
                    $reply = [
                        "text" => "🚨 *Enviar anuncio*\n\n👇 Escriba el anuncio que desea enviar:",
                        "markup" => json_encode([
                            "inline_keyboard" => [
                                [["text" => "✋ Cancelar", "callback_data" => "adminmenu"]],
                            ],
                        ]),
                    ];
                    break;

                case "notimplemented":
                    $reply = $this->notifyNotImplemented($this->actor->user_id);
                    break;

                case "senderpaymentmenu":
                    $reply = $this->PaymentsController->getPrompt($this, "getsenderpaymentscreenshot");
                    break;

                case "sendercapitalmenu":
                    $reply = $this->CapitalsController->getPrompt($this, "getsendercapitalscreenshot");
                    break;

                case "supervisorpaymentmenu":
                    $reply = $this->PaymentsController->getPrompt($this, "getsupervisorpaymentscreenshot");
                    break;

                case "supervisorcapitalmenu":
                    $reply = $this->CapitalsController->getPrompt($this, "getsupervisorcapitalscreenshot");
                    break;

                case "/confirm":
                    $reply = $this->mainMenu($this->actor);
                    if (
                        $this->actor->isLevel(1, $this->telegram["username"]) ||
                        $this->actor->isLevel(3, $this->telegram["username"]) ||
                        $this->actor->isLevel(4, $this->telegram["username"])
                    ) {
                        $reply = $this->PaymentsController->getUnconfirmedMenuForUsers($this);
                    }

                    break;

                case "getadminunconfirmedcapitalsmenu":
                    $reply = $this->CapitalsController->getUnconfirmedMenuForUsers($this);
                    break;

                case "/liquidate":
                    $reply = $this->mainMenu($this->actor);
                    if (
                        $this->actor->isLevel(1, $this->telegram["username"]) ||
                        $this->actor->isLevel(4, $this->telegram["username"])
                    ) {
                        $reply = $this->PaymentsController->getUnliquidatedMenuForUsers($this);
                    }

                    break;

                case "getadminallpaymentsmenu":
                    $reply = $this->PaymentsController->getAllMenuForUsers($this);
                    break;

                case "getadminallcapitalsmenu":
                    $reply = $this->CapitalsController->getAllMenuForUsers($this);
                    break;

                case "/users":
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
                        $this->actor->isLevel(3, $this->telegram["username"]) ||
                        $this->actor->isLevel(4, $this->telegram["username"])
                    ) {
                        $reply = $this->AgentsController->findSuscriptor($this, $array["message"]);
                    }

                    break;

                case "/usermetadata":
                    $reply = $this->ActorsController->getApplyMetadataPrompt($this, "promptusermetadata-" . $array["message"], $this->getBackOptions($this->actor, "✋ Cancelar"));
                    break;

                case "/market":
                    $reply = $this->mainMenu($this->actor);
                    if (
                        $this->actor->isLevel(1, $this->telegram["username"]) ||
                        $this->actor->isLevel(4, $this->telegram["username"])
                    ) {
                        $amount = 100;
                        $rate = $this->CoingeckoController->getRate(Carbon::now()->format("Y-m-d"));

                        $flow = $this->ProfitsController->calculateFlow($amount, $rate["inverse"]);

                        $capitals = Capitals::query()
                            ->select([
                                DB::raw('DATE(created_at) as date'),
                                DB::raw('SUM(amount) as amount'),
                                DB::raw('SUM(comment) as arrival'),
                                DB::raw('COUNT(id) as count'),
                                DB::raw('JSON_ARRAYAGG(JSON_OBJECT("id", id, "amount", amount, "comment", comment, "screenshot", screenshot, "sender_id", sender_id, "supervisor_id", supervisor_id, "data", data)) as items'),
                            ])
                            ->whereNotNull(DB::raw("JSON_EXTRACT(data, '$.rate')"))
                            ->groupBy(DB::raw('DATE(created_at)'))
                            ->orderByDesc(DB::raw('DATE(created_at)'))
                            ->limit(10)
                            ->get()
                            ->toArray();
                        for ($i = 0; $i < count($capitals); $i++) {
                            $items = json_decode($capitals[$i]["items"], true);
                            foreach ($items as $key => $item) {
                                if (isset($item["data"]["rate"])) {
                                    $capitals[$i]["data"] = $items[$key]["data"];
                                    break;
                                }
                            }
                        }

                        $symbol = "➰";
                        if (count($capitals) > 0 && isset($capitals[0]["data"]["rate"])) {
                            if ($rate["inverse"] > $capitals[0]["data"]["rate"]["oracle"]["inverse"]) {
                                $symbol = "📈";
                            }
                            if ($rate["inverse"] < $capitals[0]["data"]["rate"]["oracle"]["inverse"]) {
                                $symbol = "📉";
                            }
                        }

                        $outputsymbol = " ";
                        if ($flow["output"]["percent"] > 0)
                            $outputsymbol = " +";

                        $text = "ℹ️ *Estadísticas del sistema*\n_Este es el comportamiento del mercado HOY:_\n\n" .
                            "💰  *100.00* 💶 _Capital inicial_\n" .
                            "{$symbol}  " . Moneys::format($rate["inverse"], 4) . " 💱 _" . $rate["direct"] . "_\n" .
                            "🛬  *" . Moneys::format($flow["arrival"]) . "* 💵 _Netos_\n" .
                            "➰    - " . Moneys::format($flow["waste"]["amount"]) . " 💵 _Gastos " . $flow["waste"]["percent"] . "%_\n" .
                            "🏭  *" . Moneys::format($flow["capital"]) . "* 💵 _Procesable_\n" .
                            "➿   " . $outputsymbol . Moneys::format($flow["output"]["amount"]) . " 💱 _Cliente " . $flow["output"]["percent"] . "%_\n" .
                            "🛫  *" . Moneys::format($flow["profit"]["amount"]) . "* 💶 _Resultado_ *" . Moneys::format($flow["profit"]["percent"]) . "%*\n\n";

                        $dates = [];
                        $percents = [];
                        $sender = [];
                        $sendersum = 0;
                        $receiver = [];
                        $receiversum = 0;
                        for ($i = 0; $i < count($capitals); $i++) {
                            if (isset($capitals[$i]["data"]["rate"])) {
                                $symbol = "〰️";
                                if ($i < count($capitals) - 1) {
                                    $next = $capitals[$i + 1]["data"]["rate"]["oracle"]["inverse"];
                                    if ($capitals[$i]["data"]["rate"]["oracle"]["inverse"] > $next) {
                                        $symbol = "📈";
                                    } else {
                                        $symbol = "📉";
                                    }
                                }
                                $flow = $this->ProfitsController->calculateFlow($amount, $capitals[$i]["data"]["rate"]["oracle"]["inverse"], $capitals[$i]["data"]["profit"]["salary"], $capitals[$i]["data"]["profit"]["profit"]);

                                $dates[] = $capitals[$i]["date"];
                                $percent = $flow["profit"]["percent"];
                                $percents[] = $percent;

                                $sernderamount = $capitals[$i]["amount"] * $percent / 100;
                                $sendersum += $sernderamount;
                                $sender[] = $sernderamount;

                                $receiveramount = $capitals[$i]["arrival"] * $flow["waste"]["percent"] / 100;
                                $receiversum += $receiveramount;
                                $receiver[] = $receiveramount;

                                //die($capitals[$i]["date"] . " = " . $sernderamount . " / " . $receiveramount);

                                if ($capitals[$i]["date"] == date("Y-m-d")) {
                                    $found = true;
                                }

                                $date = Carbon::createFromDate($capitals[$i]["date"]);
                                $text .= $symbol . " " . $date->format("Y-m-d") . " 💱 " . Moneys::format($capitals[$i]["data"]["rate"]["oracle"]["inverse"], 4) . " 👉 " . Moneys::format($percent) . "%\n";
                            }
                        }

                        $dates = array_reverse($dates);
                        $percents = array_reverse($percents);
                        $sender = array_reverse($sender);
                        $receiver = array_reverse($receiver);

                        $filename = false;
                        if (count($dates) > 0) {
                            $filename = GraphsController::generateGroupBarsGraph($dates, [
                                [
                                    "values" => [$percents],
                                    "weight" => 3,
                                    "color" => ["black"],
                                    "label" => ["Percent"],
                                    "trend" => [
                                        "style" => "solid",
                                        "weight" => 2,
                                    ],
                                ],
                                [
                                    "values" => [[$receiver, $sender]],
                                    "color" => [["#fbdfaa", "#aeffae"]],
                                    "label" => [["Waste", "Profit"]],
                                    "y" => true,
                                ],
                            ]);
                        }

                        $reply = [
                            "photo" => $filename ? request()->root() . FileController::$AUTODESTROY_DIR . "/{$filename}.jpg" : null,
                            "text" => $text,
                            "markup" => json_encode([
                                "inline_keyboard" => [
                                    [
                                        ["text" => "🔃 Volver a cargar", "callback_data" => "/market"],
                                    ],
                                    [
                                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                                    ],

                                ],
                            ]),
                        ];
                    }

                    break;

                case "/stats":
                    $reply = $this->mainMenu($this->actor);
                    if (
                        $this->actor->isLevel(1, $this->telegram["username"]) ||
                        $this->actor->isLevel(4, $this->telegram["username"])
                    ) {
                        $array = explode(" ", $array["message"]);
                        $current_date = false;
                        $days = false;
                        if (count($array) == 2) {
                            $current_date = $array[0];
                            $days = $array[1];
                        }
                        //$reply = $this->PaymentsController->matchAny($this, $array[0], $array[1]);
                        $reply = $this->notifyStats($this->actor, $current_date, $days);
                    }
                    break;

                case "/cashflow":
                    $reply = $this->mainMenu($this->actor);
                    if (
                        $this->actor->isLevel(1, $this->telegram["username"]) ||
                        $this->actor->isLevel(4, $this->telegram["username"])
                    ) {
                        $reply = $this->PaymentsController->getAllCash($this);
                    }
                    break;

                case "/flow":
                    $reply = $this->mainMenu($this->actor);
                    if (
                        $this->actor->isLevel(1, $this->telegram["username"]) ||
                        $this->actor->isLevel(4, $this->telegram["username"])
                    ) {
                        $array = explode(" ", $array["message"]);
                        $current_date = false;
                        $days = 14;
                        if (count($array) == 2) {
                            $current_date = $array[0];
                            $days = $array[1];
                        }
                        //$reply = $this->PaymentsController->matchAny($this, $array[0], $array[1]);
                        $reply = $this->notifyFlow($this->actor, $current_date, $days);
                    }
                    break;

                case "promptaccountoperations":
                    $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "promptaccountoperations-" . $array["pieces"][1], $this->telegram["username"]);
                    $reply = $this->AccountsController->getOperationsPrompt();
                    break;

                case "promptmoneyamount":
                    $reply = $this->PaymentsController->getRevalorizationPrompt($this, $array["pieces"][1]);
                    break;

                case "promptmoneycomment":
                    $reply = $this->PaymentsController->getRecommentPrompt($this, $array["pieces"][1]);
                    break;

                case "/utc":
                    $reply = $this->ActorsController->getUTCPrompt($this);
                    break;

                case "/accounts":
                    $reply = $this->AccountsController->getActiveAccounts($this);
                    break;
                case "accountactivation":
                    $account = $this->AccountsController->getFirst(Accounts::class, "id", "=", $array["pieces"][1]);
                    $account->is_active = $array["pieces"][2] == "true";
                    $account->save();

                    $reply = $this->AccountsController->getMessageTemplate($account->toArray(), $this->actor->user_id);
                    $reply = $reply["message"];
                    $reply["markup"] = $reply["reply_markup"];
                    break;

                case "configdeleteprevmessages":
                    $array = $this->actor->data;
                    if (isset($array[$this->telegram["username"]]["config_delete_prev_messages"])) {
                        unset($array[$this->telegram["username"]]["config_delete_prev_messages"]);
                    } else {
                        $array[$this->telegram["username"]]["config_delete_prev_messages"] = true;
                    }

                    $this->actor->data = $array;
                    $this->actor->save();

                    $reply = $this->configMenu($this->actor);
                    break;

                case "confirmation":
                    $reply = $this->getAreYouSurePrompt($array["pieces"][1], $array["pieces"][2]);
                    break;

                //------------------------------

                case "promote1":
                    // promover a rol de GESTOR
                    $this->ActorsController->updateData(Actors::class, "user_id", $array["pieces"][1], "admin_level", 1, $this->telegram["username"]);
                    $this->AgentsController->notifyRoleChange($this, $array["pieces"][1]);
                    $reply = $this->AgentsController->notifyAfterModifyRole($this, $array["pieces"][1], 1);
                    break;
                case "promote2":
                    // promover a rol de REMESADOR
                    $this->ActorsController->updateData(Actors::class, "user_id", $array["pieces"][1], "admin_level", 2, $this->telegram["username"]);
                    $this->AgentsController->notifyRoleChange($this, $array["pieces"][1]);
                    $reply = $this->AgentsController->notifyAfterModifyRole($this, $array["pieces"][1], 2);
                    break;
                case "promote3":
                    // promover a rol de RECEPTOR
                    $this->ActorsController->updateData(Actors::class, "user_id", $array["pieces"][1], "admin_level", 3, $this->telegram["username"]);
                    $this->AgentsController->notifyRoleChange($this, $array["pieces"][1]);
                    $reply = $this->AgentsController->notifyAfterModifyRole($this, $array["pieces"][1], 3);
                    break;
                case "promote4":
                    // promover a rol de CAPITAL
                    $this->ActorsController->updateData(Actors::class, "user_id", $array["pieces"][1], "admin_level", 4, $this->telegram["username"]);
                    $this->AgentsController->notifyRoleChange($this, $array["pieces"][1]);
                    $reply = $this->AgentsController->notifyAfterModifyRole($this, $array["pieces"][1], 4);
                    break;
                case "deleteuser":
                    // eliminar un usuario
                    $user = $this->ActorsController->getFirst(Actors::class, "user_id", "=", $array["pieces"][1]);
                    $user->delete();

                    $reply = $this->ActorsController->notifyAfterDelete();
                    break;

                //------------------------------

                case "asignpaymentsupervisor":
                    // asignar un RECEPTOR a un pago
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][2]);
                    $this->PaymentsController->asignSupervisor([$payment], $array["pieces"][1]);

                    $supervisorsmenu = $this->PaymentsController->getOptionsMenuForThisOne($this, $payment, 3);
                    $this->actor = $this->ActorsController->getFirst(Actors::class, "user_id", "=", $array["pieces"][1]);
                    $payment->sendAsTelegramMessage(
                        $this,
                        $this->actor,
                        "Nuevo reporte de pago",
                        $this->message["text"],
                        true,
                        $supervisorsmenu
                    );

                    $reply = $this->PaymentsController->notifyAfterAsign($this, $array["pieces"][1]);
                    break;
                case "confirmpayment":
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                    if (!$payment->isConfirmed()) {
                        $this->PaymentsController->confirm([$payment], $this->actor->user_id);
                        $this->PaymentsController->notifyConfirmationToOwner($this, $payment);

                        if (GutoTradeBotController::$NOTIFY_NO_ENOUGH_CAPITAL) {
                            // notificar nivel de capital bajo
                            $reply = $this->notifyFlow(
                                $this->actor,
                                false,
                                14,
                                "🚨 *ADVERTENCIA del sistema*\n_No hay capital suficiente para liquidar lo confirmado:_"
                            );
                            if ($reply["data"]["stock"] < 0) {
                                $admins = $this->ActorsController->getData(Actors::class, [
                                    [
                                        "contain" => true,
                                        "name" => "admin_level",
                                        "value" => [1, 4],
                                    ],
                                ], $this->telegram["username"]);
                                for ($i = 0; $i < count($admins); $i++) {
                                    $reply["chat"]["id"] = $admins[$i]->user_id;

                                    $array = array(
                                        "message" => $reply,
                                    );
                                    $array["message"]["reply_markup"] = $array["message"]["markup"];
                                    $this->TelegramController->sendPhoto($array, $this->getToken($this->telegram["username"]));
                                }
                            }
                        }

                    }
                    $reply = $this->PaymentsController->notifyConfirmationToAdmin($this);
                    break;

                case "/match":
                    $reply = $this->mainMenu($this->actor);
                    if (
                        $this->actor->isLevel(1, $this->telegram["username"]) ||
                        $this->actor->isLevel(3, $this->telegram["username"]) ||
                        $this->actor->isLevel(4, $this->telegram["username"])
                    ) {
                        $array = explode(" ", $array["message"]);
                        $reply = $this->PaymentsController->matchAny($this, $array[0], $array[1]);
                    }
                    break;
                case "matchpayments":
                    // se notifica la recepcion al remesador en la llamada a match
                    // y aqui se notifica al admin q esta haciendo la operacion
                    $reply = $this->PaymentsController->matchAny($this, $array["pieces"][1], $array["pieces"][2]);
                    break;
                case "asignpaymentsender":
                    // asignar un REMESADOR a un pago
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][2]);
                    $this->PaymentsController->asignSender([$payment], $array["pieces"][1]);
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][2]);

                    // notificar la recepcion al remesador
                    $this->PaymentsController->notifyConfirmationToOwner($this, $payment);

                    // notificar la accion al q la esta haciendo
                    $reply = $this->PaymentsController->notifyAfterAsign($this, $array["pieces"][1]);
                    break;
                case "requestpaymentconfirmation":
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                    $this->PaymentsController->requestConfirmation($this, [$payment]);
                    $reply = $this->PaymentsController->notifyAfterStatusRequest();
                    break;
                case "requestpaymentcomments":
                    $reply = $this->PaymentsController->getComments($this, $array["pieces"][1], $this->actor->user_id);
                    break;
                case "commentpayment":
                    $reply = $this->PaymentsController->getCommentPrompt($this, "payment", $array["pieces"][1]);
                    break;
                case "liquidatepayment":
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                    $this->PaymentsController->liquidate([$payment], $this->actor->user_id);
                    $reply = $this->PaymentsController->notifyAfterLiquidate();
                    break;
                case "unconfirmedpayments":
                    $reply = $this->PaymentsController->getUnconfirmed($this, $array["pieces"][1], $this->actor->user_id);
                    break;
                case "unliquidatedpayments":
                    $reply = $this->PaymentsController->getUnliquidated($this, $array["pieces"][1], $this->actor->user_id);
                    break;
                case "floatingpayments":
                    $reply = $this->PaymentsController->getFloating($this, $array["pieces"][1], $this->actor->user_id);
                    break;
                case "changepaymentscreenshot":
                    $reply = $this->PaymentsController->getScreenshotChangePrompt($this, "getnewpaymentscreenshot-" . $array["pieces"][1]);
                    break;
                case "notyetpayment":
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                    $this->PaymentsController->notifyStatusNotYetToOwner($this, $payment);

                    $reply = $this->PaymentsController->notifyStatusNotYetToAdmin();
                    break;
                case "allpayments":
                    $reply = $this->PaymentsController->getAllList($this, $array["pieces"][1], $this->actor->user_id);
                    break;
                case "deletepayment":
                    // eliminar un pago
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                    if ($payment->sender_id && $payment->sender_id > 0) {
                        $owner = $this->ActorsController->getFirst(Actors::class, 'user_id', '=', $payment->sender_id);
                        $payment->sendAsTelegramMessage(
                            $this,
                            $owner,
                            "Reporte de pago ELIMINADO",
                            "⚠️ _Este pago ha sido eliminado de la base de datos_",
                            true,
                            [
                                [
                                    ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                                ],

                            ]
                        );
                    }
                    //$bot, $actor, $title, $message = false, $show_owner_id = true, $menu = false, $demo = false
                    $payment->delete();

                    $reply = $this->PaymentsController->notifyAfterDelete();
                    break;
                case "unconfirmpayment":
                    // desconfirmar un pago
                    $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                    $array = $payment->data;

                    unset($array["confirmation_date"]);
                    unset($array["confirmation_message"]);

                    $payment->data = $array;
                    $payment->save();

                    $reply = $this->PaymentsController->notifyAfterUnconfirm();
                    break;

                //------------------------------

                case "asigncapitalsupervisor":
                    // asignar un RECEPTOR a un aporte
                    $capital = $this->CapitalsController->getFirst(Capitals::class, "id", "=", $array["pieces"][2]);
                    $this->CapitalsController->asignSupervisor([$capital], $array["pieces"][1]);

                    $this->actor = $this->ActorsController->getFirst(Actors::class, "user_id", "=", $array["pieces"][1]);

                    $supervisorsmenu = $this->CapitalsController->getOptionsMenuForThisOne($this, $capital, 3);
                    $this->CapitalsController->notifyNew($this, $capital, $this->actor, $supervisorsmenu);

                    $reply = $this->CapitalsController->notifyAfterAsign($this, $array["pieces"][1]);
                    break;
                case "confirmcapital":
                    $capital = $this->CapitalsController->getFirst(Capitals::class, "id", "=", $array["pieces"][1]);
                    $this->CapitalsController->confirm([$capital], $this->actor->user_id);
                    $this->CapitalsController->notifyConfirmationToOwner($this, $capital);
                    $reply = $this->CapitalsController->notifyConfirmationToAdmin($this);
                    break;
                case "requestcapitalconfirmation":
                    $capital = $this->CapitalsController->getFirst(Capitals::class, "id", "=", $array["pieces"][1]);
                    $this->CapitalsController->requestConfirmation($this, [$capital]);
                    $reply = $this->CapitalsController->notifyAfterStatusRequest();
                    break;
                case "notyetcapital":
                    $capital = $this->CapitalsController->getFirst(Capitals::class, "id", "=", $array["pieces"][1]);
                    $this->CapitalsController->notifyStatusNotYetToOwner($this, $capital);

                    $reply = $this->CapitalsController->notifyStatusNotYetToAdmin();
                    break;
                case "unconfirmedcapitals":
                    $reply = $this->CapitalsController->getUnconfirmed($this, $array["pieces"][1], $this->actor->user_id);
                    break;
                case "allcapitals":
                    $reply = $this->CapitalsController->getAllList($this, $array["pieces"][1], $this->actor->user_id);
                    break;
                case "deletecapital":
                    // eliminar un pago
                    $capital = $this->CapitalsController->getFirst(Capitals::class, "id", "=", $array["pieces"][1]);
                    $capital->delete();

                    $reply = $this->CapitalsController->notifyAfterDelete();
                    break;

                case "/comments":
                    $reply = $this->PaymentsController->getComments($this, $array["message"], $this->actor->user_id);
                    break;
                case "/comment":
                    $id = $this->getIdOfRepliedMessage();
                    if ($id && $id > 0) {
                        $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $id);

                        $this->CommentsController->create($array["message"], $payment->screenshot, $this->actor->user_id, $payment->id);
                        $reply = $this->CommentsController->notifyAfterComment();
                    }
                    break;

                case "/profit":
                    $reply = $this->mainMenu($this->actor);
                    if (
                        $this->actor->isLevel(1, $this->telegram["username"])
                    ) {
                        $reply = $this->ProfitsController->getPrompt($this);
                    }
                    break;

                case "/asign":
                case "/assign":
                    $id = $this->getIdOfRepliedMessage();
                    if ($id && $id > 0) {
                        $suscriptor = $this->AgentsController->getSuscriptor($this, $array["message"], true);
                        if ($suscriptor) {
                            $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $id);
                            $payment->sender_id = $suscriptor->user_id;
                            $payment->save();
                            $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $id);
                            // preparar el menu de opciones sobre este pago
                            $menu = $this->PaymentsController->getOptionsMenuForThisOne($this, $payment, $this->actor->data[$this->telegram["username"]]["admin_level"]);
                            $payment->sendAsTelegramMessage(
                                $this,
                                $this->actor,
                                "Reporte de pago ACTUALIZADO",
                                "⚠️ _Este pago ha sido actualizado_",
                                true,
                                $menu
                            );
                            // Haciendo q no haya respuesta adicional
                            $reply = [
                                "text" => "",
                            ];
                        }
                    }
                    break;

                default:
                    $array = $this->actor->data;
                    if (isset($array[$this->telegram["username"]]["last_bot_callback_data"])) {
                        $array = $this->getCommand($array[$this->telegram["username"]]["last_bot_callback_data"]);
                        switch ($array["command"]) {
                            case "getpaymentbyvalue":
                                // resetear el comando obtenido a traves de la BD
                                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);
                                $reply = $this->PaymentsController->renderPaymentsByAny(
                                    $this,
                                    $this->message["text"],
                                    "Reporte de pago encontrado",
                                    [
                                        [
                                            ["text" => "↖️ Volver al menú de pagos", "callback_data" => "/payments"],
                                        ],
                                    ]
                                );
                                break;
                            case "getpaymentbydaysold":
                                // resetear el comando obtenido a traves de la BD
                                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);
                                $reply = $this->PaymentsController->renderPaymentsByDate(
                                    $this,
                                    $this->message["text"],
                                    "Reporte de pago rezagado",
                                    [
                                        [
                                            ["text" => "↖️ Volver al menú de pagos", "callback_data" => "/payments"],
                                        ],
                                    ]
                                );
                                break;
                            case "promptaccountoperations":
                                $account = $this->AccountsController->getFirst(Accounts::class, "id", "=", $array["pieces"][1]);
                                $array = $account->data;
                                $array["remain_operations"] = $this->message["text"];
                                $account->data = $array;
                                $account->save();

                                $reply = $this->AccountsController->getMessageTemplate($account->toArray(), $this->actor->user_id);
                                $reply = $reply["message"];
                                $reply["markup"] = $reply["reply_markup"];
                                break;
                            case "promptmoneyamount":
                                $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                                $payment->amount = $this->message["text"];
                                $payment->save();

                                // preparar el menu de opciones sobre este pago
                                $menu = $this->PaymentsController->getOptionsMenuForThisOne($this, $payment, $this->actor->data[$this->telegram["username"]]["admin_level"]);
                                $payment->sendAsTelegramMessage(
                                    $this,
                                    $this->actor,
                                    "Pago modificado",
                                    false,
                                    true,
                                    $menu
                                );
                                $reply = [
                                    "text" => "",
                                ];
                                break;
                            case "promptmoneycomment":
                                $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                                $payment->comment = $this->message["text"];
                                $payment->save();

                                // preparar el menu de opciones sobre este pago
                                $menu = $this->PaymentsController->getOptionsMenuForThisOne($this, $payment, $this->actor->data[$this->telegram["username"]]["admin_level"]);
                                $payment->sendAsTelegramMessage(
                                    $this,
                                    $this->actor,
                                    "Pago modificado",
                                    false,
                                    true,
                                    $menu
                                );
                                $reply = [
                                    "text" => "",
                                ];
                                break;
                            case "/utc":
                                if (is_numeric($this->message["text"])) {

                                    $array = $this->actor->data;
                                    $array[$this->telegram["username"]]["time_zone"] = $this->message["text"];
                                    if ($this->message["text"] == 0 || $this->message["text"] == "0") {
                                        unset($array[$this->telegram["username"]]["time_zone"]);
                                    }

                                    $this->actor->data = $array;
                                    $this->actor->save();

                                    $reply = $this->ActorsController->notifyAfterUTCChange($this->message["text"]);
                                } else {
                                    $reply = $this->ActorsController->notifyBadUTCValue($this->message["text"]);
                                }
                                break;
                            case "promptpaymentcomment":
                                // resetear el comando obtenido a traves de la BD
                                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);

                                $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $array["pieces"][1]);
                                //$comment, $screenshot, $sender_id, $payment_id, $data = array()
                                $this->CommentsController->create($this->message["text"], $payment->screenshot, $this->actor->user_id, $array["pieces"][1]);

                                switch ($this->actor->data[$this->telegram["username"]]["admin_level"]) {
                                    // si lo ha escrito un remesador se notifica a los supervisores o a los admin4
                                    case "2":
                                    case 2:
                                        if ($payment->supervisor_id && $payment->supervisor_id > 0) {
                                            $supervisor = $this->ActorsController->getFirst(Actors::class, "user_id", "=", $payment->supervisor_id);
                                            $menu = $this->PaymentsController->getOptionsMenuForThisOne($this, $payment, 3);
                                            $payment->sendAsTelegramMessage(
                                                $this,
                                                $supervisor,
                                                "Comentario sobre:",
                                                $this->message["text"],
                                                true,
                                                $menu
                                            );
                                        } else {
                                            $this->PaymentsController->notifyToCapitals($this, $payment, $this->message["text"], "Comentario sobre:");
                                        }
                                        break;
                                    // si lo ha escrito cualquier otro se le notifica al remesador
                                    default:
                                        if ($payment->sender_id && $payment->sender_id > 0) {
                                            $sender = $this->ActorsController->getFirst(Actors::class, "user_id", "=", $payment->sender_id);
                                            $menu = $this->PaymentsController->getOptionsMenuForThisOne($this, $payment, 2);
                                            $payment->sendAsTelegramMessage(
                                                $this,
                                                $sender,
                                                "Comentario sobre:",
                                                $this->message["text"],
                                                true,
                                                $menu
                                            );
                                        }
                                        break;
                                }

                                $reply = $this->CommentsController->notifyAfterComment();
                                break;
                            case "getannouncement":
                                // resetear el comando obtenido a traves de la BD
                                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);
                                $amount = 0;
                                $suscriptors = $this->ActorsController->getAll();
                                foreach ($suscriptors as $suscriptor) {
                                    $array = [
                                        "message" => [
                                            "text" => $this->message["text"],
                                            "chat" => [
                                                "id" => $suscriptor->user_id,
                                            ],
                                            "reply_markup" => json_encode([
                                                "inline_keyboard" => [
                                                    [
                                                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                                                    ],
                                                ],
                                            ]),
                                        ],
                                    ];
                                    $array = json_decode($this->TelegramController->sendMessage($array, $this->getToken($this->telegram["username"])), true);
                                    if (isset($array["result"]) && isset($array["result"]["message_id"])) {
                                        $this->TelegramController->pinMessage([
                                            "message" => [
                                                "chat" => [
                                                    "id" => $suscriptor->user_id,
                                                ],
                                                "message_id" => $array["result"]["message_id"],
                                            ],
                                        ], $this->getToken($this->telegram["username"]));
                                        $amount++;
                                    }
                                }

                                $reply = [
                                    "text" => "🚨 *Anuncio enviado*\nEl anuncio ha sido enviado correctamente a {$amount} suscriptores.\n\n👇 Qué desea hacer ahora?",
                                    "markup" => json_encode([
                                        "inline_keyboard" => [
                                            [
                                                ["text" => "↖️ Volver al menú de administrador", "callback_data" => "adminmenu"],
                                            ],

                                        ],
                                    ]),
                                ];

                                break;
                            case "promptprofit":
                                // resetear el comando obtenido a traves de la BD
                                $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);

                                $array = explode(":", $this->message["text"]);

                                if (count($array) == 2) {
                                    $stats = $this->CapitalsController->getStats($this);
                                    // la cantidad de USDT en la wallet q aun no se han procesado
                                    $amount = $stats["usdt"]["pending"] + $stats["usdt"]["unconfirmed"];
                                    if ($amount > 0) {
                                        $negative = -1 * $amount;
                                        $data = [
                                            "confirmation_date" => date("Y-m-d H:i:s"),
                                            "confirmation_message" => request("message")["message_id"],
                                        ];
                                        $this->CapitalsController->create(
                                            $this->ProfitsController->getEURtoSendWithActiveRate($negative),
                                            $negative,
                                            "AgACAgEAAxkBAALd_GcZYv85lMhzVQ-Ue8oWgwABZORGwAACQLAxG7X30UQcBx3z45dK6AEAAwIAA3kAAzYE",
                                            $this->actor->user_id,
                                            $this->actor->user_id,
                                            $data
                                        );
                                    }

                                    $this->ProfitsController->update($array[0], $array[1]);

                                    // ajustar el capital restante
                                    if ($amount > 0)
                                        $this->CapitalsController->create(
                                            $this->ProfitsController->getEURtoSendWithActiveRate($amount),
                                            $amount,
                                            "AgACAgEAAxkBAALd_GcZYv85lMhzVQ-Ue8oWgwABZORGwAACQLAxG7X30UQcBx3z45dK6AEAAwIAA3kAAzYE",
                                            $this->actor->user_id,
                                            $this->actor->user_id,
                                            $data
                                        );

                                    $reply = $this->ProfitsController->notifyAfterChange();
                                }
                                break;
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

            // Responder al texto recibido
            return $reply;
        }

        if ((isset($this->message["photo"])) || isset($this->message["document"])) {
            // para poder analizar fotos y documentos para procesarlos como pago debe existir el actor previamente
            // si es una animacion no es un pago, es un mal manejo
            if ($this->actor && $this->actor->id > 0 && !isset($this->message["animation"])) {
                $array = $this->actor->data;

                Log::info("GutoTradeBotController photo actor->data = " . json_encode($array));

                //$array = $this->getCommand($this->message["text"]);
                $command = "";
                if (isset($array[$this->telegram["username"]]["last_bot_callback_data"]))
                    $command = $array[$this->telegram["username"]]["last_bot_callback_data"];

                $commandarray = $this->getCommand($command);
                $commandarray = $commandarray["pieces"];
                if (count($commandarray) > 1)
                    $command = $commandarray[0];

                //Log::info("GutoTradeBotController photo command='{$command}'");
                switch ($command) {
                    case "getsenderpaymentscreenshot":
                        $reply = $this->PaymentsController->processMoney($this, 2, 2);
                        break;
                    case "getsupervisorpaymentscreenshot":
                        $reply = $this->PaymentsController->processMoney($this, 3, 2);
                        break;
                    case "getsendercapitalscreenshot":
                        $reply = $this->CapitalsController->processMoney($this, 4, 1);
                        break;
                    case "getsupervisorcapitalscreenshot":
                        $reply = $this->CapitalsController->processMoney($this, 1, 1);
                        break;
                    case "getnewpaymentscreenshot":
                        // Guardar la captura y devolver la ruta
                        $path = $this->getScreenshotPath();

                        $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $commandarray[1]);
                        $this->PaymentsController->updateScreenshot($payment, $path);
                        $payment = $this->PaymentsController->getFirst(Payments::class, "id", "=", $commandarray[1]);

                        $reply = $this->PaymentsController->getMessageTemplate(
                            $this,
                            $payment,
                            $this->actor->user_id,
                            "Reporte de pago",
                            "🖼 _Así ha quedado el pago luego de actualizar su captura_",
                            false,
                            [
                                [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
                            ]
                        );
                        $reply = $reply["message"];
                        $reply["markup"] = $reply["reply_markup"];
                        break;
                    case "":
                        // NO BORRAR ESTE CASE: Sirve para reenviar pagos desde otras cuentas de telegram
                        $message = request()->input('message', []);

                        unset($message["text"]);
                        if (!isset($message["message_id"]))
                            $message["message_id"] = "0";
                        if (!isset($message["caption"]))
                            $message["caption"] = "SIN DATOS 0";
                        if (isset($message["forward_from"]))
                            $message["from"]["id"] = $message["forward_from"]["id"];

                        request()->merge(['message' => $message]);

                        $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "getforwardedpaymentscreenshot", $this->telegram["username"]);

                        $reply = $this->PaymentsController->processMoney($this, 1);

                        break;
                    default:
                        // intentando resolver llamadas previas para el nuevo intento de reenviar un pago
                        $this->ActorsController->updateData(Actors::class, "user_id", $this->actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);
                        break;
                }

            }
        }

        return $reply;
    }

    public function mainMenu($actor)
    {
        $reply = [];

        $text = "👋 *Bienvenido al " . $this->telegram["username"] . "*!\n" .
            "_Este bot esta diseñado para gestionar los pagos recibidos_.\n\n";
        if (isset($actor->data[$this->telegram["username"]]["parent_id"]) && $actor->data[$this->telegram["username"]]["parent_id"] > 0) {
            $parent = $this->ActorsController->getFirst(Actors::class, "user_id", "=", $actor->data[$this->telegram["username"]]["parent_id"]);
            if ($parent && $parent->id > 0 && $parent->data) {
                if (isset($parent->data[$this->telegram["username"]]["config_allow_referals_to_myreferals"])) {
                    $text .= "_Si otras personas trabajan para ud puede entregarle su enlace de referido:_\n`https://t.me/" . $this->telegram["username"] . "?start={$actor->user_id}`\n\n";
                }
            }
        } else {
            $text .= "_Si otras personas trabajan para ud puede entregarle su enlace de referido:_\n`https://t.me/" . $this->telegram["username"] . "?start={$actor->user_id}`\n\n";
        }

        $menu = [];

        $this->ActorsController->updateData(Actors::class, "user_id", $actor->user_id, "last_bot_callback_data", "", $this->telegram["username"]);

        // admin_level = 1 Admnistrador, 2 Remesador, 3 Receptor, 4 Admin de capital
        switch ($actor->data[$this->telegram["username"]]["admin_level"]) {
            case "0":
            case 0:
                $this->notifyUserWithNoRole($actor->user_id, $this->AgentsController->getRoleMenu($actor->user_id, 0));
                $text .= "🤔 *Por alguna razón ud aun no tiene rol asignado. Le hemos enviado notficación a los administradores para que lo corrijan*.\n\n";
                break;
            case "1":
            case 1:
                array_push($menu, [["text" => "👍 Recepción de capital", "callback_data" => "supervisorcapitalmenu"]]);
                array_push($menu, [["text" => "👮‍♂️ Admin", "callback_data" => "adminmenu"]]);
                array_push($menu, [["text" => "🏦 Cuentas activas", "callback_data" => "/accounts"]]);
                break;
            case "2":
            case 2:
                array_push($menu, [["text" => "💶 Reportar pago realizado", "callback_data" => "senderpaymentmenu"]]);
                array_push($menu, [
                    ["text" => "🤷🏻‍♂️ Sin confirmar", "callback_data" => "unconfirmedpayments-{$actor->user_id}"],
                    ["text" => "🫰🏻 Sin liquidar", "callback_data" => "unliquidatedpayments-{$actor->user_id}"],
                ]);
                array_push($menu, [["text" => "🔎 Buscar", "callback_data" => "buscar"]]);
                array_push($menu, [["text" => "📝 Exportar pagos", "callback_data" => "allpayments-{$actor->user_id}"]]);
                array_push($menu, [["text" => "🏦 Cuentas activas", "callback_data" => "/accounts"]]);
                break;
            case "3":
            case 3:
                array_push($menu, [["text" => "👍 Recepción de pago", "callback_data" => "supervisorpaymentmenu"]]);
                array_push($menu, [
                    ["text" => "🤷🏻‍♂️ Sin confirmar", "callback_data" => "/confirm"],
                ]);
                break;
            case "4":
            case 4:
                array_push($menu, [["text" => "👍 Recepción de pago", "callback_data" => "supervisorpaymentmenu"]]);
                array_push($menu, [["text" => "💰 Aporte de capital", "callback_data" => "sendercapitalmenu"]]);
                array_push($menu, [["text" => "👮‍♂️ Admin", "callback_data" => "adminmenu"]]);
                array_push($menu, [["text" => "🏦 Cuentas activas", "callback_data" => "/accounts"]]);
                break;
            default:
                break;
        }

        $text .= "👇 En qué le puedo ayudar hoy?";

        array_push($menu, [
            ["text" => "⚙️ Configuración", "callback_data" => "configmenu"],
            ["text" => "🆘 Ayuda", "callback_data" => "help"],
        ]);

        $reply = [
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        ];

        return $reply;
    }

    public function adminMenu($actor)
    {
        $reply = [];

        $menu = [];

        array_push($menu, [
            ["text" => "💶 Pagos", "callback_data" => "/payments"],
            ["text" => "💰 Capital", "callback_data" => "/capitals"],
        ]);
        // admin_level = 1 Admnistrador, 4 Admin de capital
        switch ($actor->data[$this->telegram["username"]]["admin_level"]) {
            case "1":
            case 1:
                array_push($menu, [["text" => "🧮 Estadísticas", "callback_data" => "/stats"]]);
                array_push($menu, [["text" => "💹 Flujo de Caja", "callback_data" => "/cashflow"]]);
                array_push($menu, [["text" => "🚨 Anuncio", "callback_data" => "sendannouncement"]]);
                array_push($menu, [["text" => "🤑 Ajustar ganancias", "callback_data" => "/profit"]]);
                array_push($menu, [["text" => "🫂 Usuarios suscritos", "callback_data" => "/users"]]);
                break;
            case "4":
            case 4:
                array_push($menu, [["text" => "🧮 Estadísticas", "callback_data" => "/stats"]]);
                array_push($menu, [["text" => "💹 Flujo de Caja", "callback_data" => "/cashflow"]]);
                array_push($menu, [["text" => "🚨 Anuncio", "callback_data" => "sendannouncement"]]);
                break;
            default:
                break;
        }
        array_push($menu, [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]]);

        $reply = [
            "text" => "👮‍♂️ *Menú de administrador*!\n\n_Aquí encontrará herramientas útiles para la gestión integral del bot_\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        ];

        return $reply;
    }

    public function getBackOptions($actor, $text)
    {
        $backoption = ["text" => $text, "callback_data" => "menu"];
        if ($actor->isLevel(1, $this->telegram["username"]) || $actor->isLevel(4, $this->telegram["username"])) {
            $backoption = ["text" => $text, "callback_data" => "adminmenu"];
        }

        return $backoption;
    }

    public function configMenu($actor)
    {
        $reply = [];

        $array = $actor->data;

        $menu = [];

        // Opciones para todos los usuarios:
        if (isset($array[$this->telegram["username"]]["config_delete_prev_messages"])) {
            array_push($menu, [["text" => "🟢 No eliminar mensajes previos", "callback_data" => "configdeleteprevmessages"]]);
        } else {
            array_push($menu, [["text" => "🔴 Eliminar mensajes previos", "callback_data" => "configdeleteprevmessages"]]);
        }
        $timezone = "UTC";
        if (isset($array[$this->telegram["username"]]["time_zone"])) {
            $timezone .= $array[$this->telegram["username"]]["time_zone"];
        }
        array_push($menu, [["text" => "⏰ Zona horaria {$timezone}", "callback_data" => "/utc"]]);

        array_push($menu, [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]]);

        $reply = [
            "text" => "⚙️ *Menú de configuraciones*!\n\n_Aquí encontrará ajustes del comportamiento del bot_\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        ];

        return $reply;
    }

    public function notifyShortSearchParameter($user_id, $message)
    {
        $reply = [
            "text" => "ℹ️ *Muy pocos parametros*\n\n_El texto “{$message}” es muy corto para realizar la búsqueda y puede retornar muchos resultados. Intente nuevamente con un texto más largo para limitar resultados._\n\n👇 Qué desea hacer ahora?",
            "chat" => [
                "id" => $user_id,
            ],
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "🔎 Buscar otro", "callback_data" => "buscar"],
                    ],
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        ];

        return $reply;
    }

    public function getDebt()
    {
        $debt = 0;
        switch (strtolower($this->telegram["username"])) {
            case "gutotradebot":
                $debt = GutoTradeBotController::$STOLEN_FUNDS;
                break;

            default:
                break;
        }
        return $debt;
    }

    /**
     * Summary of notifyStats
     * @param mixed $actor
     * @param mixed $start_date Y-m-d
     * @param mixed $end_date Y-m-d / days
     * @return array
     */
    public function notifyStats($actor, $start_date = false, $end_date = false)
    {
        $array = $this->PaymentsController->getPaymentsStats($this, $start_date, $end_date);
        $from_date = $array["from_date"];
        $to_date = $array["to_date"];
        $array = $array["items"];

        //dd($array);

        $stats = "";
        //if ($current_date) {
        $stats .= "🛬 *Recibido*: " . Moneys::format($array["received"]["amount"]) . " 💵" .
            "\n🏷 *A enviar*: " . Moneys::format($array["received"]["tosend"]) . " 💶" .
            "\n🛫 *Enviado*: " . Moneys::format($array["sent"]["amount"]) . " 💶 (" . Moneys::format($array["sent"]["percent"]) . "%)" .
            "\n🏭 *Pendiente*: " . Moneys::format($array["pending"]["amount"]) . " 💶 (" . Moneys::format($array["pending"]["percent"]) . "%)";
        //}

        $stats .= "\n\n🤷🏻‍♂️ *Sin confirmar*: " . Moneys::format($array["unconfirmed"]) . " 💶" .
            "\n🫰🏻 *Sin liquidar*: " . Moneys::format($array["unsettled"]) . " 💶";

        $stats .= "\n\n💰 *USDT Físico*: " . Moneys::format($array["stock"]) . " 💵";

        $value = $array["stock"] + $this->ProfitsController->getProfit($array["stock"]);

        $stats .= "\n💱 *Equivalentes a*: " . Moneys::format($value) . " 💶";

        if ($actor->isLevel(1, $this->telegram["username"])) {
            $stats .= "\n\n☑ *Debería tener*: " . Moneys::format($array["should"]) . " 💵";
            if ($array["having"] >= $array["should"]) {
                $stats .= "\n✅ ";
            } else {
                if ($array["having"] >= $array["unsettled"]) {
                    $stats .= "\n😳 ";
                } else {
                    $stats .= "\n🥵 ";
                }
            }

            $stats .= "*Tengo*: " . Moneys::format($array["having"]) . " 💵";
        }

        $debt = $this->getDebt();
        if ($debt > 0)
            $stats .= "\n\n🩸 *A recuperar*: " . Moneys::format($debt) . " 💵";

        $records = $this->PaymentsController->getRecords($from_date, $to_date);
        if (count($records["dates"]) == 0) {
            array_push($records["dates"], Carbon::now()->subDays(1)->toDateString());
            array_push($records["dates"], Carbon::now()->toDateString());
            array_push($records["receiveds"], 0);
            array_push($records["receiveds"], 0);
            array_push($records["confirmeds"], 0);
            array_push($records["confirmeds"], 0);
            array_push($records["sents"], 0);
            array_push($records["sents"], 0);
            array_push($records["balances"], 0);
            array_push($records["balances"], 0);
            array_push($records["confirmed_balances"], 0);
            array_push($records["confirmed_balances"], 0);
        }

        $filename = GraphsController::generateLinesGraph(
            $records["dates"],
            [
                [
                    "values" => $records["receiveds"],
                    "weight" => 2,
                    "color" => "green",
                    "label" => "Recibido",
                ],
                [
                    "values" => $records["sents"],
                    "style" => "dashed",
                    "weight" => 2,
                    "color" => "orange",
                ],
                [
                    "values" => $records["confirmeds"],
                    "weight" => 2,
                    "color" => "orange",
                    "label" => "Enviado",
                ],
                [
                    "values" => $records["confirmed_balances"],
                    "weight" => 3,
                    "color" => "#FF0000",
                    "label" => "Balance",
                    "trend" => [
                        "style" => "dashed",
                        "weight" => 2,
                        "color" => [
                            "positive" => "green",
                            "negative" => "red",
                        ],
                    ],
                ],
                [
                    "values" => $records["balances"],
                    "style" => "dashed",
                    "weight" => 2,
                    "color" => "#FF0000",
                ],
            ]
        );

        $text = "el momento";
        if ($end_date) {
            $text = "{$to_date->format("Y-m-d")}";
        }

        //$records["balances"]
        //$records["confirmeds"]
        $icon = "ℹ️";
        $limit = $records["balances"][count($records["balances"]) - 2] + $records["receiveds"][count($records["receiveds"]) - 1];
        if ($records["confirmeds"][count($records["confirmeds"]) - 1] < $limit) {
            $icon = "❇️";
        } else {
            if ($array["stock"] > 0) {
                $icon = "📳";
            } else {
                $icon = "🆘";
            }
        }

        $text = "{$icon} *Estadísticas del sistema*\n_Estos son los resultados hasta {$text}:_";

        $menu = [];
        $adminmenu = [];
        if (
            $actor->isLevel(1, $this->telegram["username"]) ||
            $actor->isLevel(4, $this->telegram["username"])
        ) {
            if ($array["unconfirmed"] > 0) {
                array_push($adminmenu, ["text" => "👍 Confirmar", "callback_data" => "/confirm"]);
            }
        }
        if ($actor->isLevel(1, $this->telegram["username"])) {
            if ($array["unsettled"] > 0) {
                array_push($adminmenu, ["text" => "🫰🏻 Liquidar", "callback_data" => "/liquidate"]);
            }
        }

        if (count($adminmenu) > 0) {
            array_push($menu, $adminmenu);
        }

        array_push($menu, [["text" => "🔃 Volver a cargar", "callback_data" => "/stats"]]);
        array_push($menu, [["text" => "↖️ Volver al menú de administrador", "callback_data" => "adminmenu"]]);

        $reply = [
            "text" => $text . "\n\n{$stats}\n\n👇 Qué desea hacer ahora?",
            "photo" => request()->root() . FileController::$AUTODESTROY_DIR . "/{$filename}.jpg",
            "chat" => [
                "id" => $actor->user_id,
            ],
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        ];

        return $reply;
    }

    public function notifyFlow($actor, $start_date = false, $end_date = false, $text = false)
    {
        $array = $this->PaymentsController->getPaymentsStats($this, $start_date, $end_date);
        $from_date = $array["from_date"];
        $to_date = $array["to_date"];
        $array = $array["items"];
        //dd($array);

        $stats = "";

        $stats .= "💰 *USDT Físico*: " . Moneys::format($array["stock"]) . " 💵";

        $value = $array["stock"] + $this->ProfitsController->getProfit($array["stock"]);

        $stats .= "\n💱 *Equivalentes a*: " . Moneys::format($value) . " 💶";

        $records = $this->PaymentsController->getRecords($from_date, $to_date);
        //dd($records);
        $sendamount = 0;
        $records["sentprom"] = [];
        $receivedamount = 0;
        $records["receivedprom"] = [];
        for ($i = 0; $i < count($records["confirmeds"]); $i++) {
            $records["sents"][$i] -= $records["confirmeds"][$i];

            $sendamount += $records["confirmeds"][$i];
            $records["sentprom"][$i] = $sendamount / ($i + 1);

            $receivedamount += $records["receiveds"][$i];
            $records["receivedprom"][$i] = $receivedamount / ($i + 1);
        }

        $stats .= "\n\n🛬 *Recibido promedio*: " . Moneys::format($records["receivedprom"][count($records["receivedprom"]) - 1]) . " 💵";
        $stats .= "\n🛫 *Enviado promedio*: " . Moneys::format($records["sentprom"][count($records["sentprom"]) - 1]) . " 💶";

        if (count($records["dates"]) == 0) {
            array_push($records["dates"], Carbon::now()->subDays(1)->toDateString());
            array_push($records["dates"], Carbon::now()->toDateString());
            array_push($records["receiveds"], 0);
            array_push($records["receiveds"], 0);
            array_push($records["confirmeds"], 0);
            array_push($records["confirmeds"], 0);
            array_push($records["sents"], 0);
            array_push($records["sents"], 0);
            array_push($records["balances"], 0);
            array_push($records["balances"], 0);
            array_push($records["confirmed_balances"], 0);
            array_push($records["confirmed_balances"], 0);
        }

        $filename = GraphsController::generateGroupBarsGraph($records["dates"], [
            [
                "values" => [[$records["sents"], $records["confirmeds"]], $records["receiveds"]],
                "color" => [["#fafa8f", "#fbdfaa"], "#aeffae"],
                "label" => [[null, "Enviado"], "Recibido"],
            ],
            [
                "values" => [$records["sentprom"], $records["receivedprom"]],
                "weight" => 3,
                "color" => ["#eb6f01", "#12b512"],
                "label" => [null, null],
                "trend" => [
                    "style" => "solid",
                    "weight" => 2,
                ],
            ],
        ]);

        if (!$text) {
            $text = "ℹ️ *Estadísticas del sistema*\n_sobre envíos y recibos recientes:_";
        }

        $reply = [
            "text" => $text . "\n\n{$stats}\n\n👇 Qué desea hacer ahora?",
            "photo" => request()->root() . FileController::$AUTODESTROY_DIR . "/{$filename}.jpg",
            "chat" => [
                "id" => $actor->user_id,
            ],
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "🔃 Volver a cargar", "callback_data" => "/flow"],
                    ],
                    [
                        ["text" => "↖️ Volver al menú de administrador", "callback_data" => "adminmenu"],
                    ],

                ],
            ]),
            "data" => $array,
        ];

        return $reply;
    }

    public function getIdOfRepliedMessage()
    {
        $id = false;

        $message = request()->input('message', []);
        if (
            isset($message["reply_to_message"]) &&
            isset($message["reply_to_message"]["reply_markup"]) &&
            isset($message["reply_to_message"]["reply_markup"]["inline_keyboard"])
        ) {
            foreach ($message["reply_to_message"]["reply_markup"]["inline_keyboard"] as $row) {
                foreach ($row as $btn)
                    if (
                        isset($btn["callback_data"]) &&
                        stripos($btn["callback_data"], "/buscar") > -1
                    ) {
                        $id = str_ireplace("/buscar ", "", $btn["callback_data"]);
                        break;
                    }
            }
        }

        return $id;

    }
}
