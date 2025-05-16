<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use App\Http\Controllers\TextController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\GutoTradeBot\Entities\Capitals;
use Modules\GutoTradeBot\Entities\Payments;
use Modules\GutoTradeBot\Entities\Profits;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Http\Controllers\FileController;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MoneysController extends JsonsController
{
    public static $NOSCREENSHOT_PATH = "/screenshotnone.jpg";

    public function createByModel($model, $amount, $comment, $screenshot, $sender_id, $supervisor_id, $data = array())
    {
        $money = false;
        if (isset($data["message_id"])) {
            // comente esto porq produce el bug de pagos nuevos q no habia encontrado
            //$money = $model::where("data", "LIKE", "%" . $data["message_id"] . "%")->first();
            // lo ajuste asi para retomar la validacion y sin generar bug
            $money = $model::where("data", "LIKE", "%\"message_id\":" . $data["message_id"] . "%")->first();
        }

        if (!$money) {
            $money = $model::create([
                'amount' => $amount,
                'comment' => $comment,
                'screenshot' => $screenshot,
                'sender_id' => $sender_id,
                'supervisor_id' => $supervisor_id,
                'data' => $data,
            ]);
        }

        return $money;
    }

    /**
     * Chequea que el formato del texto de Money sea "Juan Perez 200"
     * @param mixed $text
     * @return array{success: bool, fullname: string, amount: float}
     */
    public function processCaption($text)
    {
        $array = [
            "success" => true,
        ];

        // Patrón actualizado para nombres y cantidades en diferentes formatos
        $pattern = '/([\p{L}\p{M}\.\s]+)\s+((€\s?[\d,.]+)|([\d,.]+\s?€)|([\d,.]+\s?euros)|([\d,.]+))/u';

        // Comprobar si el texto cumple con el patrón
        if (preg_match($pattern, $text, $matches)) {
            // Si hay coincidencias, extraemos nombre completo
            $array["fullname"] = trim($matches[1]);

            // Intentamos procesar la cantidad
            $amount = preg_replace('/[^\d,\.]/', '', $matches[2]);

            // Validar si el valor es numérico antes de continuar
            if ($amount === '' || !is_numeric(str_replace([',', '.'], '', $amount))) {
                // Si no es numérico, devolver un mensaje de advertencia o un valor predeterminado
                $array["success"] = false;
                $array["amount"] = 0;
                return $array;
            }

            // Manejo de formatos europeos y americanos
            if (strpos($amount, ',') !== false && strpos($amount, '.') === false) {
                $amount = str_replace(',', '.', $amount); // Convertir coma en separador decimal
            } elseif (strpos($amount, ',') !== false && strpos($amount, '.') !== false) {
                $amount = str_replace('.', '', $amount);  // Eliminar separador de miles
                $amount = str_replace(',', '.', $amount); // Convertir coma en separador decimal
            }

            // Asignamos el valor procesado
            $array["amount"] = $amount;
        } else {
            // Si no coincide con el patrón, retornar valores predeterminados
            $array["success"] = false;
            $array["fullname"] = "";
            $array["amount"] = 0;
        }

        return $array;
    }

    /**
     * Crea un mensaje para notificar que no se cumplen los requisitos del caption para un Money
     * @param mixed $bot
     * @param mixed $bad_caption
     * @param mixed $type: 1 es capital, 2 es un pago
     * @return array{markup: bool|string, text: string}
     */
    public function badCaptionReply($bot, $bad_caption, $type = 2)
    {
        // $type = 1 es capital, 2 es un pago
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", "", $bot->telegram["username"]);

        $icon = "💶";
        $text = "";
        $callback_data = "menu";
        switch ($type) {
            case 1:
                $icon = "💰";
                $text = "🤔 *Reporte de capital incompleto*\n\n_Su reporte de aporte de capital no tiene el formato adecuando en la descripción.\nDebe escribir el Nombre y Apellidos de la persona que envía y seguido la cantidad de USDT enviados separados por un espacio._\n\n1️⃣ Ejemplo:    *Eduardo Perez 20*\n2️⃣ Ejemplo:    *Anibal Cobo Lopez 190*\n\n❌ Recibido: {$bad_caption}\n\n👇 Qué desea hacer ahora?";
                $callback_data = "sendercapitalmenu";
                break;

            default:
                $text = "🤔 *Reporte de pago incompleto*\n\n_Su reporte de pago no tiene el formato adecuando en la descripción.\nDebe escribir el Nombre y Apellidos de la persona que envía y seguido la cantidad de euros enviados separados por un espacio._\n\n1️⃣ Ejemplo:    *Eduardo Perez 20*\n2️⃣ Ejemplo:    *Anibal Cobo Lopez 190*\n\n❌ Recibido: {$bad_caption}\n\n👇 Qué desea hacer ahora?";
                $callback_data = "senderpaymentmenu";
                break;
        }

        $reply = [
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "{$icon} Intentar nuevamente", "callback_data" => $callback_data],
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        ];

        return $reply;
    }

    /**
     * Summary of processMoney
     * @param mixed $bot
     * @param mixed $sender
     * @param mixed $type: 1 es capital, 2 es un pago
     */
    public function processMoney($bot, $sender = 2, $type = 2)
    {
        $reply = [];

        // Guardar la captura y devolver la ruta
        $path = $bot->getScreenshotPath();

        if (isset(request("message")["caption"])) {
            $caption = $this->processCaption(request("message")["caption"]);
            //Log::info("GutoTradeBotController processMoney caption " . json_encode($caption) . "\n");

            // Guardar el pago en la BD
            if ($caption["success"] && isset($caption["fullname"]) && isset($caption["amount"])) {
                $sender_id = $bot->actor->user_id;
                $supervisor_id = null;
                $data = [
                    "message_id" => request("message")["message_id"],
                ];
                if (($type == 2 && $sender == 3) || ($type == 1 && $sender == 1)) {
                    $sender_id = null;
                    $supervisor_id = $bot->actor->user_id;

                    $data["confirmation_date"] = date("Y-m-d H:i:s");
                    $data["confirmation_message"] = request("message")["message_id"];
                }
                // para los pagos reenviados por mi al bot
                if ($type == 2 && $sender == 1 && isset(request("message")["from"])) {
                    $sender_id = request("message")["from"]["id"];
                    $sender = 2;
                }

                try {
                    switch ($type) {
                        case 1:
                            $data["rate"] = array(
                                "internal" => 1,
                                "oracle" => $bot->CoingeckoController->getRate(Carbon::now()->format("Y-m-d")),
                                "receiver" => 1
                            );
                            $data["fullname"] = strtolower($caption["fullname"]);
                            $data["profit"] = [
                                "salary" => $bot->ProfitsController->getSalary(),
                                "profit" => $bot->ProfitsController->getProfit(),
                            ];

                            $capital = $bot->CapitalsController->create($bot->ProfitsController->getEURtoSendWithActiveRate($caption["amount"]), $caption["amount"], $path, $sender_id, $supervisor_id, $data);

                            // Si es un reporte enviado por un admin4 se notifica a los admin1 para q confirmen
                            if ($sender == 4) {
                                $bot->CapitalsController->notifyToGestors($bot, $capital);
                            }

                            $reply = $bot->CapitalsController->notifyAfterReceived($bot, $capital, $bot->actor->user_id);
                            break;
                        case 2:
                            $payment = $bot->PaymentsController->create($bot, $caption["amount"], $caption["fullname"], $path, $sender_id, $supervisor_id, $data);

                            $similar_message = "";
                            $payments_today = $bot->PaymentsController->getMoneysByDate(Payments::class, Carbon::now()->toDateString());
                            foreach ($payments_today as $today_payment) {
                                // comparar el pago con previos del mismo dia
                                if (
                                    $today_payment->sender_id != null &&  // si el pago previo tienen un sender_id valido (o sea, lo subio un remesador)
                                    $payment->id != $today_payment->id && // si son diferentes pagos
                                    $payment->amount == $today_payment->amount
                                ) { // y con el mismo amount
                                    $similarity = $bot->TextController->calculateSimilarityPercentage($payment->comment, $today_payment->comment);
                                    // si la similitud es superior a 70% hay probabilidad q sea el mismo pago
                                    if ($similarity >= 70) {
                                        $similar_message .= "🟡 `" . $today_payment->id . "` `" . $today_payment->comment . "` 👀\n";
                                    }
                                }
                            }

                            switch ($sender) {
                                // Si es enviado por un REMESADOR se notifica a los admins4 para q asignen o confirmen
                                case '2':
                                case 2:
                                    $bot->PaymentsController->notifyToCapitals($bot, $payment, $similar_message, "Nuevo reporte de pago");
                                    // si es sospechoso se notifica ademas a los admin1
                                    if (
                                        $similar_message != "" ||
                                        GutoTradeBotController::$NOTIFY_FOR_DEBUG
                                    ) {
                                        $bot->PaymentsController->notifyToGestors($bot, $payment, $similar_message, "Nuevo reporte de pago");
                                    }
                                    break;
                                // Si es enviado por un RECEPTOR o un admin4 se notifica a los admins1 por si hay q machearlo
                                case '3':
                                case 3:
                                case '4':
                                case 4:
                                    $bot->PaymentsController->notifyToGestors($bot, $payment, false, "Nuevo reporte de pago");
                                    break;
                                default:
                                    break;
                            }

                            $reply = $bot->PaymentsController->notifyAfterReceived($bot, $payment, $bot->actor->user_id);
                            break;

                        default:
                            break;
                    }
                } catch (\Throwable $th) {
                    Log::error("MoneysController processMoney ERROR CODE {$th->getCode()} line {$th->getLine()}: {$th->getMessage()} TraceAsString: " . $th->getTraceAsString());
                    $reply = [
                        "text" => "😬 *Ha ocurrido un error {$th->getCode()}*\n_{$th->getMessage()}_",
                        "markup" => json_encode([
                            "inline_keyboard" => [
                                [
                                    ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                                ],

                            ],
                        ]),
                    ];
                }
            } else {
                $reply = $this->badCaptionReply($bot, request("message")["caption"], $type);
            }
        } else {
            $reply = $this->badCaptionReply($bot, "Sin descripción", $type);
        }

        return $reply;
    }

    public function updateScreenshot($money, $screenshot)
    {
        $array = $money->data;

        if ($money->screenshot != MoneysController::$NOSCREENSHOT_PATH) {
            if (!isset($array["previous_screenshot"])) {
                $array["previous_screenshot"] = array();
            } else
                if (is_string($array["previous_screenshot"])) {
                    $array["previous_screenshot"] = array(
                        $array["previous_screenshot"],
                    );
                }
            $array["previous_screenshot"][] = $money->screenshot;
        }
        $money->screenshot = $screenshot;

        $money->data = $array;
        $money->save();
    }

    public function searchMoneysByField($model, $field, $symbol, $value)
    {
        return $model::where($field, $symbol, $value)->get();
    }
    public function searchMoneysByAny($model, $value)
    {
        $moneys = $model::where('comment', 'LIKE', "%{$value}%")
            ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', ["%{$value}%"])
            ->orWhereRaw('CAST(id AS CHAR) LIKE ?', ["%{$value}%"])
            ->get();

        return $moneys;
    }
    public function getUnmatchedMoneysByAmount($model, $amount, $distinct_id)
    {
        return $model::where('amount', $amount)
            ->where("id", "!=", $distinct_id)
            ->whereNull('sender_id')
            ->get();
    }
    public function getUnmatchedMoneys($model, $distinct_id)
    {
        return $model::where("id", "!=", $distinct_id)
            ->whereNull('sender_id')
            ->get();
    }

    public function getUnconfirmedQuery($bot, $model, $user_id = false)
    {
        $query = $model::whereNull(DB::raw("JSON_EXTRACT(data, '$.confirmation_date')"));

        switch (true) {
            case is_array($user_id):
                $query = $query->whereIn('sender_id', $user_id);
                break;
            // si es un int o un string q se puede convertir en int
            case ctype_digit($user_id):
                $array = [$user_id];

                $actors = $this->getData(Actors::class, [
                    [
                        "contain" => true,
                        "name" => "parent_id",
                        "value" => [$user_id],
                    ],
                ], $bot->telegram["username"]);
                foreach ($actors as $actor) {
                    $array[] = $actor->user_id;
                }
                $query = $query->whereIn('sender_id', $array);
                break;
            default:
                break;
        }

        return $query;

    }
    public function getUnconfirmedMoneys($bot, $model, $user_id = false)
    {
        return $this->getUnconfirmedQuery($bot, $model, $user_id)->get();
    }

    public function getUnconfirmedMoneysBeforeDate($bot, $model, $date, $time = null, $user_id = false)
    {
        // Si no se proporciona $time, se asume que es a las 23:59:59
        $dateTime = Carbon::parse($date . ' ' . ($time ?? '23:59:59'));

        $query = $this->getUnconfirmedQuery($bot, $model, $user_id);
        $query = $query->where('created_at', '<=', $dateTime);
        $moneys = $query->get();

        return $moneys;
    }

    public function getMoneysByDate($model, $date, $startTime = null, $endTime = null)
    {
        // Si no se proporciona $startTime, se asume que es a las 00:00:00
        $startDateTime = Carbon::parse($date . ' ' . ($startTime ?? '00:00:00'));

        // Si no se proporciona $endTime, se asume que es a las 23:59:59
        $endDateTime = Carbon::parse($date . ' ' . ($endTime ?? '23:59:59'));

        // Buscar pagos entre el rango de fecha y hora
        $moneys = $model::whereBetween('created_at', [$startDateTime, $endDateTime])->get();
        return $moneys;
    }
    public static function getCapitalsByDateRange($model, $startDate = null, $endDate = null)
    {
        $query = $model::query()->orderBy('created_at', 'desc');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->get();
    }

    public function getFloatingMoneys($model)
    {
        return $model::whereNull('sender_id')->get();
    }
    public function getAllMoneys($bot, $model, $user_id = false)
    {
        $query = $model::where("id", ">", 0);

        switch (true) {
            case is_array($user_id):
                $query = $query->whereIn('sender_id', $user_id);
                break;
            // si es un int o un string q se puede convertir en int
            case ctype_digit($user_id):
                $array = [$user_id];
                $actors = $this->getData(Actors::class, [
                    [
                        "contain" => true,
                        "name" => "parent_id",
                        "value" => [$user_id],
                    ],
                ], $bot->telegram["username"]);
                foreach ($actors as $actor) {
                    $array[] = $actor->user_id;
                }
                $query = $query->whereIn('sender_id', $array);
                break;
            default:
                break;
        }

        return $query->get();
    }

    public function getUnliquidatedQuery($bot, $model, $user_id = false)
    {
        $query = $model::whereNull(DB::raw("JSON_EXTRACT(data, '$.liquidation_date')"))
            ->whereNotNull(DB::raw("JSON_EXTRACT(data, '$.confirmation_date')"));

        switch (true) {
            case is_array($user_id):
                $query = $query->whereIn('sender_id', $user_id);
                break;
            // si es un int o un string q se puede convertir en int
            case ctype_digit($user_id):
                $array = [$user_id];
                $actors = $this->getData(Actors::class, [
                    [
                        "contain" => true,
                        "name" => "parent_id",
                        "value" => [$user_id],
                    ],
                ], $bot->telegram["username"]);
                foreach ($actors as $actor) {
                    $array[] = $actor->user_id;
                }
                $query = $query->whereIn('sender_id', $array);
                break;
            default:
                $query = $query->whereNotNull("sender_id"); // si los quieren todos no pueden salir los pagos flotantes
                break;
        }

        return $query;
    }
    public function getUnliquidatedMoneys($bot, $model, $user_id = false)
    {
        return $this->getUnliquidatedQuery($bot, $model, $user_id)->get();
    }

    public function getOptionsMenuForThisOne($bot, $money, $role = 3, $extra_options = false)
    {
        $controller = null;

        $menu = array();

        $clase = get_class($money);
        $type = "";
        if (stripos($clase, "payment") > -1) {
            $controller = new PaymentsController();
            $type = "payment";

            if ($role == 1 && $money->sender_id != null && $money->isConfirmed() && !$money->isLiquidated()) {
                array_push($menu, [
                    ["text" => "⚠️ Liquidar", "callback_data" => "liquidate{$type}-{$money->id}"],
                ]);
            }

        }
        if (stripos($clase, "capital") > -1) {
            $controller = new CapitalsController();
            $type = "capital";
        }

        if (!$money->isConfirmed() && ($role == 2 || (($role == 1 || $role == 4) && ($money->supervisor_id && $money->supervisor_id > 0)))) {
            array_unshift($menu, [
                ["text" => "⚠️ Solicitar confirmación", "callback_data" => "request{$type}confirmation-{$money->id}"],
            ]);
        }

        if (!$money->isConfirmed() && ($role == 1 || $role == 3 || $role == 4)) {
            // Confirmarlo por el propio admin o supervisor
            array_push($menu, [
                ["text" => "👍 Confirmarlo", "callback_data" => "confirm{$type}-{$money->id}"],
                ["text" => "🤷🏻‍♂️ Nada aún", "callback_data" => "notyet{$type}-{$money->id}"],
            ]);
        }

        // Ver si es el dueño o admin1 o REMESADOR
        if ($role == 1 || $role == 2) {
            array_push($menu, [
                ["text" => "🎆 Cambiar captura", "callback_data" => "change{$type}screenshot-{$money->id}"],
            ]);
        }

        $commentsmenu = [
            ["text" => "💬 Comentar", "callback_data" => "comment{$type}-{$money->id}"],
        ];
        if ($money && $money->hasComments()) {
            array_push($commentsmenu, ["text" => "🗣 Ver comentarios", "callback_data" => "request{$type}comments-{$money->id}"]);
        }
        array_unshift($menu, $commentsmenu);

        if ($role == 1) {
            // Opcion para q pueda ajustarse el nombre del money
            $ajustmentsmenu = array(
                ["text" => "✒ Renombrar", "callback_data" => "promptmoneycomment-{$money->id}"]
            );
            // Opcion para q pueda ajustarse el monto del money si no esta liquidado
            if (!$money->isLiquidated())
                array_push($ajustmentsmenu, ["text" => "🎲 Revalorizar", "callback_data" => "promptmoneyamount-{$money->id}"]);
            array_push($menu, $ajustmentsmenu);

            // Si el pago no esta confirmado se puede hacer match con un flotante
            // Si es un flotante se puede hacer match con uno no confirmado
            $moneys = array();
            // por defecto asumimos q se analiza el pago del remitente y q va onway
            $onway = true;
            if (!$money->sender_id || $money->sender_id == null) {
                // no va onway (el pago q se esta analizando es el flotante)
                $onway = false;
                // Machearlo con un pago sin confirmar reportado antes con similitud de un 50% o mas en el nombre del cliente
                $moneys = $controller->getUnconfirmedPayments($bot);
            } else {
                // Machearlo con un pago flotante reportado antes con similitud de un 50% o mas en el nombre del cliente
                $moneys = $controller->getUnmatched($money->id);
            }
            foreach ($moneys as $unmatched) {
                if ($money->id != $unmatched->id) {
                    $similarity = $bot->TextController->calculateSimilarityPercentage($money->comment, $unmatched->comment);
                    if ($similarity >= 40) {
                        array_push($menu, [
                            ["text" => "🔗 {$unmatched->id}: {$unmatched->comment} {$unmatched->amount}", "callback_data" => $onway ? "match{$type}s-{$money->id}-{$unmatched->id}" : "match{$type}s-{$unmatched->id}-{$money->id}"],
                        ]);
                    }

                }
            }

            // estas opciones solo pueden ser si el pago no esta confirmado
            if (!$money->isConfirmed()) {

                // Se agregan supervisores q confirmen
                $supervisors = array();
                switch ($type) {
                    case "payment":
                        // confirmarlo a traves de RECEPTORES
                        $supervisors = $bot->ActorsController->getData(Actors::class, [
                            [
                                "contain" => true,
                                "name" => "admin_level",
                                "value" => [3, 4],
                            ],
                        ], $bot->telegram["username"]);
                        break;
                    case "capital":
                        // confirmarlo a traves de ADMIN gestor
                        $supervisors = $bot->ActorsController->getData(Actors::class, [
                            [
                                "contain" => true,
                                "name" => "admin_level",
                                "value" => 1,
                            ],
                        ], $bot->telegram["username"]);
                        break;

                    default:
                        break;
                }
                foreach ($supervisors as $supervisor) {
                    if ($supervisor->user_id != $money->supervisor_id) {
                        $callback = "asignsupervisor";
                        if (stripos($clase, "payment") > -1) {
                            $callback = "asignpaymentsupervisor-{$supervisor->user_id}-{$money->id}";
                        }
                        if (stripos($clase, "capital") > -1) {
                            $callback = "asigncapitalsupervisor-{$supervisor->user_id}-{$money->id}";
                        }
                        $suscriptor = $bot->AgentsController->getSuscriptor($bot, $supervisor->sender_id, true);
                        if ($suscriptor && $suscriptor->id && $suscriptor->id > 0)
                            array_push($menu, [["text" => $suscriptor->getTelegramInfo($bot, "full_name"), "callback_data" => $callback]]);
                    }
                }
            } else {
                // Opcion para q un admin1 pueda desconfirmar
                if ($money->sender_id != null) {
                    if (!$money->isLiquidated())
                        array_push($menu, [
                            ["text" => "♨️ Desconfirmar", "callback_data" => "confirmation|unconfirm{$type}-{$money->id}|menu"],
                        ]);
                }
            }

            // Opcion para q un admin1 pueda eliminar un money si no ha sido confirmado
            if (!$money->isConfirmed())
                array_push($menu, [
                    ["text" => "❌ Eliminar", "callback_data" => "confirmation|delete{$type}-{$money->id}|menu"],
                ]);
        }

        array_push($menu, [["text" => "🔃 Volver a cargar", "callback_data" => "/buscar {$money->id}"]]);

        // si me pasan por parametros opciones adicionales las incluyo antes de VOLVER AL MENU PRINCIPAL
        if ($extra_options) {
            foreach ($extra_options as $option) {
                array_push($menu, $option);
            }
        }

        array_push($menu, [
            ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
        ]);

        return $menu;
    }

    public function getMessageTemplate($bot, $money, $to_id, $title, $message = false, $show_owner_id = true, $menu = false, $extra_capture = false)
    {
        $text = "💰 *{$title}*\n🆔 `{$money->id}`\n\n";

        if ($message && $message != "") {
            $text .= "{$message}\n\n";
        }

        $clase = get_class($money);
        if (stripos($clase, "payment") > -1) {
            $text .= "*🪪 A nombre de:\n👤 {$money->comment}: {$money->amount} 💶*\n";
        }
        if (stripos($clase, "capital") > -1) {
            $text .= "*🖍 Movimiento:\n🛬 Se reciben: {$money->comment} 💰\n🛫 Se enviarán: {$money->amount} 💶*\n";
        }

        $text .= "📅 *Fecha*: {$money->created_at}\n\n";

        if ($show_owner_id) {
            $suscriptor = $bot->AgentsController->getSuscriptor($bot, $money->sender_id, true);
            if ($suscriptor && $suscriptor->id > 0)
                $text .= "👨🏻‍💻 Reportado por:\n" . $suscriptor->getTelegramInfo($bot, "full_info") . "\n\n";

            if ($money->supervisor_id && $money->supervisor_id > 0) {
                $suscriptor = $bot->AgentsController->getSuscriptor($bot, $money->supervisor_id, true);
                if ($suscriptor && $suscriptor->id > 0)
                    $text .= "🕵️‍♂️ Asignado a:\n" . $suscriptor->getTelegramInfo($bot, "full_info") . "\n\n";
            }
        }

        if ($menu && count($menu) > 0) {
            $text .= "👇 Qué desea hacer?";
        }

        return array(
            "message" => array(
                "text" => $text,
                "photo" => $extra_capture && isset($money->data["previous_screenshot"]) ? $money->data["previous_screenshot"] : $money->screenshot,
                "chat" => array(
                    "id" => $to_id,
                ),
                "reply_markup" => json_encode([
                    "inline_keyboard" => $menu ? $menu : array(),
                ]),
            ),
        );
    }

    /**
     * Devuelve estadisticas dadas fecha de inicio y fin
     * @param mixed $from_date date
     * @param mixed $to_date date
     * @return array
     */
    public function getStats($bot, $from_date = false, $to_date = false)
    {
        // USD: total de usd recibidos sin extraer ni salario ni ganancias
        $total_usdt_recibido = (float) $this->getMoneysSumByDate(Capitals::class, "comment", $from_date, $to_date);
        $cantidad_euros_enviados = (float) $this->getMoneysSumByDate(Payments::class, "amount", $from_date, $to_date);

        // EUR: euros a enviar luego de procesar.
        $total_euros_a_enviar = (float) $this->getMoneysSumByDate(Capitals::class, "amount", $from_date, $to_date);

        // EUR
        $euros_enviados_sin_confirmar = (float) ($this->getUnconfirmedQuery($bot, Payments::class)->sum('amount'));
        $euros_enviados_sin_liquidar = (float) ($this->getUnliquidatedQuery($bot, Payments::class)->sum('amount'));

        // EUR
        $euros_pendientes_a_enviar = $total_euros_a_enviar - $cantidad_euros_enviados;

        return array(
            "usdt" => array(
                "received" => $total_usdt_recibido,
                "pending" => $bot->ProfitsController->getSpended($euros_pendientes_a_enviar),
                "unconfirmed" => $bot->ProfitsController->getSpended($euros_enviados_sin_confirmar),
            ),
            "eur" => array(
                "tosend" => $total_euros_a_enviar,
                "sent" => $cantidad_euros_enviados,
                "unconfirmed" => $euros_enviados_sin_confirmar,
                "unsettled" => $euros_enviados_sin_liquidar,
                "pending" => $euros_pendientes_a_enviar,
            ),
        );
    }

    public function getInfo($bot, $from_date = false, $to_date = false)
    {
        $stats = $this->getStats($bot, $from_date, $to_date);

        $sent_percent = 0;
        $pending_percent = 0;
        if ($stats["eur"]["tosend"] > 0) {
            $sent_percent = $stats["eur"]["sent"] * 100 / $stats["eur"]["tosend"];
            $pending_percent = $stats["eur"]["pending"] * 100 / $stats["eur"]["tosend"];
        }

        //echo $stats["usdt"]["pending"] . "\n";
        //echo $stats["usdt"]["unconfirmed"] . "\n";
        //die;

        $stock = $stats["usdt"]["pending"] + $stats["usdt"]["unconfirmed"];

        $debt = $bot->getDebt();
        if ($debt > 0)
            $stock -= $debt;

        return array(
            "received" => array(
                "amount" => $stats["usdt"]["received"],
                "tosend" => $stats["eur"]["tosend"],
            ),
            "sent" => array(
                "amount" => $stats["eur"]["sent"],
                "percent" => round((float) ($sent_percent), 2),
            ),
            "pending" => array(
                "amount" => $stats["eur"]["pending"],
                "percent" => round((float) ($pending_percent), 2),
            ),
            "unconfirmed" => $stats["eur"]["unconfirmed"],
            "unsettled" => $stats["eur"]["unsettled"],
            "stock" => $stock,
            "should" => $bot->ProfitsController->getUSDTtoSendWithActiveRate($stats["eur"]["unconfirmed"] + $stats["eur"]["unsettled"]),
            "having" => $stock + $bot->ProfitsController->getUSDTreceived($stats["eur"]["unsettled"]),
        );
    }

    /**
     *
     * @param mixed $from_date date
     * @param mixed $to_date date
     * @return array
     */
    public function getRecords($from_date, $to_date)
    {
        // EUR
        $confirmed_payments = $this->getMoneysSumGroupByDate(Payments::class, "amount", $from_date, $to_date, 1)->toArray();
        // EUR
        $payments = $this->getMoneysSumGroupByDate(Payments::class, "amount", $from_date, $to_date)->toArray();
        // antes tenia "amount" para capital pero eso es lo q se envia, el recibido correcto es "comment"
        $capitals = $this->getMoneysSumGroupByDate(Capitals::class, "comment", $from_date, $to_date)->toArray();

        $dates = array();
        while ($from_date <= $to_date) {
            $dates[] = $from_date->toDateString();
            $from_date->addDay();
        }
        //dd($dates);
        $confirmeds = array();
        $sents = array();
        $receiveds = array();
        $balances = array();
        $confirmed_balances = array();

        $array = array();
        foreach ($dates as $date) {
            $to_date = Carbon::createFromFormat("Y-m-d H:i:s", $date . " 23:59:59")->addDays(-1);
            //dd($to_date);
            $prev_payment = (float) $this->getMoneysSumByDate(Payments::class, "amount", false, $to_date);
            if (!$prev_payment) {
                $prev_payment = 0;
            }
            $prev_capital = (float) $this->getMoneysSumByDate(Capitals::class, "amount", false, $to_date);
            if (!$prev_capital) {
                $prev_capital = 0;
            }

            $received = isset($capitals[$date]) ? $capitals[$date] : 0;
            $confirmed = isset($confirmed_payments[$date]) ? $confirmed_payments[$date] : 0;

            $value = isset($payments[$date]) ? $payments[$date] : 0;

            /*
            $debug = array(
            "date" => $date,
            "prev_capital" => $prev_capital,
            "prev_payment" => $prev_payment,
            "received" => $received,
            "value" => $value,
            );
            dd($debug);
             */

            $unconfirmed_balance = $prev_capital - $prev_payment + $received - $value;
            $confirmed_balance = $prev_capital - $prev_payment + $received - $confirmed;

            $receiveds[] = $received;

            $sents[] = $value;
            $confirmeds[] = $confirmed;

            $balances[] = $unconfirmed_balance;
            $confirmed_balances[] = $confirmed_balance;

            $array[$date] = array(
                "send" => $value,
                "received" => $received,
                "confirmed" => $confirmed,
                "balance" => $confirmed_balance,
                "prev_capital" => $prev_capital,
                "prev_payment" => $prev_payment,
            );
        }

        return array(
            "bydate" => $array,
            "dates" => $dates,
            "sents" => $sents,
            "receiveds" => $receiveds,
            "confirmeds" => $confirmeds,
            "balances" => $balances,
            "confirmed_balances" => $confirmed_balances,
        );
    }

    public function getMoneysSumGroupByDate($model, $field, $startDate = false, $endDate = false, $confirm = 0, $liquidate = 0)
    {
        $query = $model::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(' . $field . ') as total_amount'));

        // Aplicar condiciones de rango de fechas si se proporcionan
        if ($startDate && $endDate) {
            // Si se proporcionan ambas fechas
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            // Solo fecha de inicio
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            // Solo fecha de fin
            $query->where('created_at', '<=', $endDate);
        }

        switch ($confirm) {
            case 1: // con moneys confirmados
                $query->whereNotNull(DB::raw("JSON_EXTRACT(data, '$.confirmation_date')"));
                break;
            case 2: // con moneys NO confirmados
                $query->whereNull(DB::raw("JSON_EXTRACT(data, '$.confirmation_date')"));
                break;
            default: // con todos los moneys
                break;
        }
        switch ($liquidate) {
            case 1: // con moneys liquidados
                $query->whereNotNull(DB::raw("JSON_EXTRACT(data, '$.confirmation_date')"))
                    ->whereNull(DB::raw("JSON_EXTRACT(data, '$.liquidation_date')"));
                break;
            case 2: // con moneys NO liquidados
                $query->whereNotNull(DB::raw("JSON_EXTRACT(data, '$.liquidation_date')"));
                break;
            default: // con todos los moneys
                break;
        }

        $results = $query->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        // Convertir el resultado a un array asociativo para un acceso más fácil
        return $results->mapWithKeys(function ($item) {
            return [$item->date => round((float) $item->total_amount, 2)];
        });
    }

    public function getMoneysSumByDate($model, $field, $startDate = false, $endDate = false)
    {
        $query = $model::select(DB::raw('SUM(' . $field . ') as total_amount'));

        // Aplicar condiciones de rango de fechas si se proporcionan
        if ($startDate && $endDate) {
            // Si se proporcionan ambas fechas
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            // Solo fecha de inicio
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            // Solo fecha de fin
            $query->where('created_at', '<=', $endDate);
        }

        $results = $query->get();

        return $results[0]->toArray()["total_amount"];
    }

    public function getScreenshotChangePrompt($bot, $method)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", $method, $bot->telegram["username"]);

        $reply = array(
            "text" => "🎆 *Cambiar captura*\n\n_Para cambiar la captura Ud solo debe enviar la nueva a continuación.\nNo es necesario agregar texto en la descripción._\n\n👇 Envíe la nueva captura:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✋ Cancelar", "callback_data" => "menu"]],
                ],
            ]),
        );

        return $reply;
    }

    public function getSearchPrompt($bot, $method, $backoption)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", $method, $bot->telegram["username"]);

        $reply = array(
            "text" => "🔎 *Buscar registro en la BD*\n_Ud puede escribir el ID, la cantidad, o parte del nombre del remitente para buscarlo. Tenga en cuenta que criterios muy cortos pueden generar muchos resultados._\n\n👇 Escriba el valor por el que desea buscar:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [$backoption],
                ],
            ]),
        );

        return $reply;
    }

    public function getDaysPrompt($bot, $method, $backoption)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", $method, $bot->telegram["username"]);

        $reply = array(
            "text" => "🔎 *Buscar registros en la BD*\n_Es posible buscar registros con cierta cantidad de días de antigüedad. Si escribe un valor positivo, se sumará esa cantidad de días a le fecha actual; si por el contrario el número escrito es negativo, se resta a la fecha actual los días especificados._\n\n👇 Escriba cuántos días desea buscar:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [$backoption],
                ],
            ]),
        );

        return $reply;
    }

    public function getRevalorizationPrompt($bot, $money_id)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", "promptmoneyamount-{$money_id}", $bot->telegram["username"]);

        $reply = array(
            "text" => "🎲 *Ajustar cantidad del envio*\n\n👇 Escriba el nuevo valor:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✋ Cancelar", "callback_data" => "menu"]],
                ],
            ]),
        );

        return $reply;
    }

    public function getRecommentPrompt($bot, $money_id)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", "promptmoneycomment-{$money_id}", $bot->telegram["username"]);

        $reply = array(
            "text" => "✒ *Ajustar el nombre del remitente*\n\n👇 Escriba el nuevo valor:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✋ Cancelar", "callback_data" => "menu"]],
                ],
            ]),
        );

        return $reply;
    }
    public function getCommentPrompt($bot, $type, $money_id)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", "prompt{$type}comment-{$money_id}", $bot->telegram["username"]);

        $reply = array(
            "text" => "💬 *Comentar sobre este registro*\n\n👇 Escriba el texto que desee a continuación:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✋ Cancelar", "callback_data" => "menu"]],
                ],
            ]),
        );

        return $reply;
    }

    public function exportCashFlow($cashflow)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->getCashFlowSheet($cashflow, $sheet);

        $writer = new Xlsx($spreadsheet);
        $filename = time() . ".xlsx";

        $path = public_path() . FileController::$AUTODESTROY_DIR;
        // Si la carpeta no existe, crearla
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        // Guardar el archivo en el sistema
        $writer->save($path . "/" . $filename);

        $array = explode(".", $filename);
        return array(
            "filename" => $array[0],
            "extension" => $array[1],
        );
    }

    public function getAllCash($bot)
    {
        $results = $bot->PaymentsController->getCashFlow($bot);
        $array = $bot->PaymentsController->exportCashFlow($results);
        $xlspath = request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"];

        $text = "🧮 *Flujo de caja*\n_Estos son los movimientos hasta el momento.";
        $menu = [
            [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
        ];
        $text .= "_\n\n" . $bot->getReportFileText($xlspath);

        $reply = array(
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        );

        return $reply;
    }

    public function getCashFlow($bot)
    {
        $paymentsByDate = Payments::select(
            DB::raw('GROUP_CONCAT(id SEPARATOR "; ") as ids'),
            DB::raw('DATE(created_at) as date'),
            DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.rate.internal")) AS DECIMAL(10,2)) as rate'),
            DB::raw('SUM(amount) as eur'),
        )
            //->whereNotNull(DB::raw("JSON_EXTRACT(data, '$.confirmation_date')"))
            ->groupBy('date', 'rate')
            ->orderBy('date')
            ->get();
        //dd($paymentsByDate->toArray());

        $liquidationByDate = Payments::select(
            DB::raw('GROUP_CONCAT(id SEPARATOR "; ") as ids'),
            DB::raw('DATE(JSON_UNQUOTE(JSON_EXTRACT(data, "$.liquidation_date"))) as liquidation_date'),
            DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.rate.internal")) AS DECIMAL(10,2)) as rate'),
            DB::raw('SUM(amount) as eur'),
            DB::raw('SUM(CASE 
                WHEN JSON_EXTRACT(data, "$.confirmation_date") IS NOT NULL THEN
                    amount * CASE 
                        WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.rate.internal")) AS DECIMAL) > 0 
                            THEN 1 - (CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.rate.internal")) AS DECIMAL)/100)
                        WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.rate.internal")) AS DECIMAL) < 0 
                            THEN 1 + (ABS(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.rate.internal")) AS DECIMAL))/100)
                        ELSE 1
                    END
                ELSE 0
            END) as usdt'),
        )
            ->whereNotNull(DB::raw("JSON_EXTRACT(data, '$.liquidation_date')"))
            ->groupBy('liquidation_date', 'rate')
            ->orderBy('liquidation_date')
            ->get();
        // dd($liquidationByDate->toArray());

        $capitalsByDate = Capitals::select(
            //DB::raw('DATE(JSON_UNQUOTE(JSON_EXTRACT(data, "$.confirmation_date"))) as date'),
            DB::raw('DATE(created_at) as date'),
            DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data, "$.profit.salary"))+JSON_UNQUOTE(JSON_EXTRACT(data, "$.profit.profit")) AS DECIMAL) as rate'),
            DB::raw('SUM(amount) as tosend'),
            DB::raw('SUM(comment) as received'),
        )
            //->whereNotNull(DB::raw("JSON_EXTRACT(data, '$.confirmation_date')"))
            ->groupBy('rate', 'date')
            ->orderBy('date')
            ->orderBy('rate')
            ->get();
        //dd($capitalsByDate->toArray());
        //dd($capitalsByDate->toArray(), $paymentsByDate->toArray());

        $array = array();
        $items = $paymentsByDate->toArray();
        foreach ($items as $item) {
            if (!isset($array[$item["date"]]))
                $array[$item["date"]] = array(
                    "payments" => array(),
                    "capitals" => array(),
                    "liquidation" => array(
                        "amount" => 0,
                        "payments" => array(),
                    ),
                );
            //unset($item["date"]);
            $array[$item["date"]]["payments"][] = $item;
        }
        //dd($array);
        $items = $liquidationByDate->toArray();
        foreach ($items as $item) {
            if (!isset($array[$item["liquidation_date"]]))
                $array[$item["liquidation_date"]] = array(
                    "payments" => array(),
                    "capitals" => array(),
                    "liquidation" => array(
                        "amount" => 0,
                        "payments" => array(),
                    ),
                );
            $array[$item["liquidation_date"]]["liquidation"]["amount"] += $item["usdt"];
            $array[$item["liquidation_date"]]["liquidation"]["payments"][] = $item["ids"];
        }
        //dd($array);
        $items = $capitalsByDate->toArray();
        foreach ($items as $item) {
            $index = $item["date"];
            if (!isset($array[$index]))
                $array[$index] = array(
                    "payments" => array(),
                    "capitals" => array(),
                    "liquidation" => array(
                        "amount" => 0,
                        "payments" => array(),
                    ),
                );
            unset($item["date"]);
            $array[$index]["capitals"][] = $item;
        }
        uksort($array, function ($a, $b) {
            return strtotime($a) - strtotime($b);
        });

        $activerate = false;
        foreach ($array as $key => $day) {
            if (
                isset($day["capitals"][0]) &&
                $activerate === false &&
                isset($day["capitals"])
            )
                $activerate = $day["capitals"][0]["rate"];


            foreach ($day["capitals"] as $capital) {
                if ($activerate > 0) {
                    if (
                        isset($capital["rate"]) &&
                        $capital["rate"] < $activerate
                    )
                        $activerate = $capital["rate"];
                } else if ($activerate < 0) {
                    if (
                        isset($capital["rate"]) &&
                        $capital["rate"] > $activerate
                    )
                        $activerate = $capital["rate"];
                }
            }

            $array[$key]["activerate"] = $activerate;
            foreach ($day["payments"] as $payment) {
                if (floatval($payment["rate"]) != (floatval($activerate))) {
                    $amount = $payment["eur"];
                    $negative = -1 * $amount;
                    $tosend = $bot->ProfitsController->getEURtoSendWithActiveRate($amount, $activerate);
                    $array[$key]["capitals"][] = array(
                        "rate" => $activerate,
                        "tosend" => -1 * $tosend,
                        "received" => $negative,
                    );
                    $tosend = $bot->ProfitsController->getEURtoSendWithActiveRate($amount, $payment["rate"]);
                    $array[$key]["capitals"][] = array(
                        "rate" => $payment["rate"],
                        "tosend" => $tosend,
                        "received" => $amount,
                    );
                }
            }
        }
        //dd($array);

        return $array;
    }

    public function getCashFlowSheet($cashflow, $sheet)
    {
        $sheet->setCellValue("A1", "Fecha");
        $sheet->setCellValue("B1", "Recibido");
        $sheet->setCellValue("C1", "Tasa");
        $sheet->setCellValue("D1", "A Enviar");
        $sheet->setCellValue("E1", "Enviado");
        $sheet->setCellValue("F1", "Tasa");
        $sheet->setCellValue("G1", "A Pagar");
        $sheet->setCellValue("H1", "Liquidado");
        $sheet->setCellValue("I1", "Balance");
        $sheet->setCellValue("J1", "ID");

        foreach ($cashflow as $date => $day) {
            // Obtener la última fila con datos en la columna A
            $row = $sheet->getHighestDataRow('A');
            $max = count($day["payments"]);
            if (count($day["payments"]) < count($day["capitals"]))
                $max = count($day["capitals"]);
            for ($i = 0; $i < $max; $i++) {
                $sheet->setCellValue("A" . ($row + 1 + $i), $date);
                if (isset($day["capitals"][$i])) {
                    $sheet->setCellValue("B" . ($row + 1 + $i), $day["capitals"][$i]["received"]);
                    $sheet->setCellValue("C" . ($row + 1 + $i), $day["capitals"][$i]["rate"]);
                }

                $formula = "=B" . ($row + 1 + $i) . "*(1+(C" . ($row + 1 + $i) . "/100))";
                $sheet->setCellValue("D" . ($row + 1 + $i), $formula);

                if (isset($day["payments"][$i])) {
                    $sheet->setCellValue("E" . ($row + 1 + $i), $day["payments"][$i]["eur"]);

                    $sheet->setCellValue("F" . ($row + 1 + $i), $day["payments"][$i]["rate"]);
                    //                                    (1 - ($day["payments"][$i]["rate"]/100))
                    $formula = "=E" . ($row + 1 + $i) . "*(1-(F" . ($row + 1 + $i) . "/100))";
                    if ($day["payments"][$i]["rate"] < 0)
                        //                                    (1 + (ABS($day["payments"][$i]["rate"])/100))
                        $formula = "=E" . ($row + 1 + $i) . "*(1+(ABS(F" . ($row + 1 + $i) . ")/100))";
                    $sheet->setCellValue("G" . ($row + 1 + $i), $formula);

                }

                if (
                    $day["liquidation"]["amount"] > 0 &&
                    $i == 0
                ) {
                    $sheet->setCellValue("H" . ($row + 1 + $i), $day["liquidation"]["amount"]);
                    $sheet->setCellValue("J" . ($row + 1 + $i), implode("; ", $day["liquidation"]["payments"]));
                    $sheet->getStyle('J' . ($row + 1 + $i))->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ]);
                }

                $formula = "=B" . ($row + 1 + $i) . "-H" . ($row + 1 + $i);
                if ($row > 1)
                    $formula = "=I" . ($row + $i) . "+B" . ($row + 1 + $i) . "-H" . ($row + 1 + $i);
                $sheet->setCellValue("I" . ($row + 1 + $i), $formula);

            }
        }
        // Obtener la última fila con datos en la columna A
        $lastRow = $sheet->getHighestDataRow('A');

        // Agregar la fórmula SUM en la siguiente fila
        $sheet->setCellValue('A' . ($lastRow + 1), "TOTAL:");
        $sheet->getStyle('A' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->setCellValue('A' . ($lastRow + 2), "Balance:");
        $sheet->getStyle('A' . ($lastRow + 2))->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->setCellValue('B' . ($lastRow + 1), '=SUM(B2:B' . $lastRow . ')');
        // Opcional: aplicar formato a la celda de total
        $sheet->getStyle('B' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        // Opcional: aplicar formato a la celda de total
        $sheet->getStyle('C' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        $sheet->setCellValue('D' . ($lastRow + 1), '=SUM(D2:D' . $lastRow . ')');
        // Opcional: aplicar formato a la celda de total
        $sheet->getStyle('D' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        $sheet->setCellValue('E' . ($lastRow + 1), '=SUM(E2:E' . $lastRow . ')');
        // Opcional: aplicar formato a la celda de total
        $sheet->getStyle('E' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        $sheet->getStyle('F' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        $sheet->setCellValue('G' . ($lastRow + 1), '=SUM(G2:G' . $lastRow . ')');
        $sheet->getStyle('G' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        $sheet->setCellValue('H' . ($lastRow + 1), '=SUM(H2:H' . $lastRow . ')');
        // Opcional: aplicar formato a la celda de total
        $sheet->getStyle('H' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        $sheet->getStyle('I' . ($lastRow + 1))->applyFromArray([
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        // Opcional: aplicar formato a la celda de total
        $sheet->getStyle('J' . ($lastRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE]]
        ]);

        $formula = "=B" . ($lastRow + 1) . "-H" . ($lastRow + 1);
        $sheet->setCellValue("B" . ($lastRow + 2), $formula);
        $sheet->getStyle('B' . ($lastRow + 2))->applyFromArray([
            'font' => ['bold' => true],
        ]);

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(7);
        $sheet->getColumnDimension('C')->setVisible(false);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('D')->setVisible(false);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(7);
        $sheet->getColumnDimension('F')->setVisible(false);
        $sheet->getColumnDimension('G')->setWidth(10);
        //$sheet->getColumnDimension('G')->setVisible(false);
        $sheet->getColumnDimension('H')->setWidth(10);
        //$sheet->getColumnDimension('H')->setVisible(false);
        $sheet->getColumnDimension('I')->setWidth(15);
        //$sheet->getColumnDimension('I')->setVisible(false);
        $sheet->getColumnDimension('J')->setWidth(60);
        $sheet->getColumnDimension('J')->setVisible(false);
        $sheet->freezePane('B2');
        $sheet->setTitle("Flujo");

        // Opcional: estilo para los encabezados
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFD9D9D9']]
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
        // Agregar filtros automáticos a los encabezados (desde A1 hasta F1)
        $sheet->setAutoFilter('A1:H1');
    }
}
