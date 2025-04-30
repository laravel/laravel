<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\FileController;
use App\Http\Controllers\MathController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\GutoTradeBot\Entities\Agents;
use Modules\GutoTradeBot\Entities\Moneys;
use Modules\GutoTradeBot\Entities\Payments;
use Modules\GutoTradeBot\Entities\Profits;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PaymentsController extends MoneysController
{
    public function create($bot, $amount, $comment, $screenshot, $sender_id, $supervisor_id, $data = array())
    {
        $data["rate"] = array(
            "internal" => $bot->ProfitsController->getProfit(100),
            "oracle" => CoingeckoController::getRate(),
            "receiver" => 0
        );
        $data["capital"] = $bot->ProfitsController->getSpended($amount);
        /*
        Log::info("PaymentsController create args " . json_encode([
            'amount' => $amount,
            'comment' => $comment,
            'screenshot' => $screenshot,
            'sender_id' => $sender_id,
            'supervisor_id' => $supervisor_id,
            'data' => $data,
        ]) . "\n");
        */
        $payment = parent::createByModel(Payments::class, $amount, $comment, $screenshot, $sender_id, $supervisor_id, $data);
        Log::channel('storage')->info('payment ' . json_encode($payment->toArray()));

        return $payment;
    }

    public function getUnmatched($id)
    {
        return parent::getUnmatchedMoneys(Payments::class, $id);

    }
    public function getUnconfirmedPayments($bot, $user_id = false)
    {
        return parent::getUnconfirmedMoneys($bot, Payments::class, $user_id);
    }
    public function getUnliquidatedPayments($bot, $user_id = false)
    {
        return parent::getUnliquidatedMoneys($bot, Payments::class, $user_id);
    }
    public function getFloatingPayments()
    {
        return parent::getFloatingMoneys(Payments::class);
    }
    public function getAllPayments($bot, $user_id = false)
    {
        return parent::getAllMoneys($bot, Payments::class, $user_id);
    }

    public function import()
    {
        $path = public_path() . "/import.xlsm";

        $spreadsheet = IOFactory::load($path);
        //$sheet = $spreadsheet->getActiveSheet();
        $sheet = $spreadsheet->getSheetByName('Envios');

        // Leer el excel
        $highestRow = $sheet->getHighestRow();
        $data = [];
        // Define las columnas a analizar
        $columns = ['A', 'B', 'C', 'D', 'E'];
        for ($row = 1; $row <= $highestRow; $row++) {
            foreach ($columns as $column) {
                $cellCoordinate = $column . $row;
                $cellStyle = $sheet->getStyle($cellCoordinate);

                // Obtener color de fondo
                $fill = $cellStyle->getFill();
                $bgColor = $fill->getStartColor()->getRGB();

                // Obtener color de texto
                $font = $cellStyle->getFont();
                $textColor = $font->getColor()->getRGB();

                // Obtener valor de la celda
                $cellValue = $sheet->getCell($cellCoordinate)->getValue();
                $date = "";

                // Verificar si el valor es un número (posiblemente una fecha en formato de número de serie)
                if (is_numeric($cellValue)) {
                    // Convertir el número de serie a una fecha
                    $formattedDate = Date::excelToDateTimeObject($cellValue);

                    // Formatear la fecha según tus necesidades
                    $date = $formattedDate->format('Y-m-d');
                }

                // Analizar el color de fondo y el color del texto
                $data[$row][$column] = [
                    'value' => $cellValue,
                    'liquidated' => $bgColor === 'FFFF00' ? false : true,
                    'confirmed' => $textColor === 'FF0000' ? false : true,
                    'date' => $date,
                ];
            }
        }
        // Quitando el encabezado
        unset($data[1]);
        //dd($data[2]);
        // Guardar en la BD los registros
        foreach ($data as $value) {
            $date = $value["A"]["date"];
            $amount = $value["B"]["value"];
            if ($amount > 0) { // validando q sea mayor q 0... si el excel esta en desarrollo da error
                $liquidated = $value["B"]["liquidated"];
                $confirmed = $value["C"]["confirmed"];
                $sender_id = 816767995;
                if ($value["D"]["value"] != "" && $value["D"]["value"] != "Efectivo") {
                    $sender_id = Actors::$KNOWN_ACTORS[$value["D"]["value"]]["user_id"];
                }

                $comment = $value["E"]["value"];

                $supervisor_id = Actors::$KNOWN_ACTORS["Roger"]["user_id"];
                $way = $value["C"]["value"];
                if (stripos($way, "dayami") > -1) {
                    $supervisor_id = Actors::$KNOWN_ACTORS["Dayami"]["user_id"];
                }

                $json = array(
                    "message_id" => 1,
                );
                if ($confirmed) {
                    $json["confirmation_date"] = $date . " " . date("H:i:s");
                    $json["confirmation_message"] = 1;
                }
                if ($liquidated) {
                    $json["liquidation_date"] = $date . " " . date("H:i:s");
                    $json["liquidation_message"] = 1;
                }
                $payment = Payments::create([
                    'amount' => $amount,
                    'comment' => $comment,
                    'screenshot' => MoneysController::$NOSCREENSHOT_PATH,
                    'sender_id' => $sender_id,
                    'supervisor_id' => $supervisor_id,
                    'data' => $json,
                ]);
                $payment->created_at = Carbon::createFromFormat("Y-m-d H:i:s", $date . " " . date("H:i:s"));
                $payment->save();
            }
        }
    }

    public function export($bot, $payments, $actor)
    {
        $isadmin = $actor->isLevel(1, $bot->telegram["username"]) || $actor->isLevel(4, $bot->telegram["username"]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue("A1", "ID");
        $sheet->setCellValue("B1", "Fecha");
        $sheet->setCellValue("C1", "Cantidad");
        $sheet->setCellValue("D1", "Cliente");
        $sheet->setCellValue("E1", "Envia");
        if ($isadmin) {
            $sheet->setCellValue("F1", "Recibe");
        }

        $actors = array();

        for ($i = 0; $i < count($payments); $i++) {
            $sheet->setCellValue("A" . ($i + 2), $payments[$i]->id);
            $sheet->setCellValue("B" . ($i + 2), Carbon::createFromFormat("Y-m-d H:i:s", $actor->getLocalDateTime($payments[$i]->created_at, $bot->telegram["username"]))->toDateString());
            $sheet->setCellValue("C" . ($i + 2), $payments[$i]->amount);
            $sheet->setCellValue("D" . ($i + 2), $payments[$i]->comment);
            if (!isset($actors[$payments[$i]->sender_id])) {
                $actors[$payments[$i]->sender_id] = "";
                if ($payments[$i]->sender_id && $payments[$i]->sender_id > 0) {
                    $response = json_decode($bot->TelegramController->getUserInfo($payments[$i]->sender_id, $bot->getToken($bot->telegram["username"])), true);
                    $actors[$payments[$i]->sender_id] = $response["result"]["full_name"];
                }
            }
            $sheet->setCellValue("E" . ($i + 2), $actors[$payments[$i]->sender_id]);
            if ($isadmin) {
                if (!isset($actors[$payments[$i]->supervisor_id])) {
                    $actors[$payments[$i]->supervisor_id] = "";
                    if ($payments[$i]->supervisor_id && $payments[$i]->supervisor_id > 0) {
                        $response = json_decode($bot->TelegramController->getUserInfo($payments[$i]->supervisor_id, $bot->getToken($bot->telegram["username"])), true);
                        $actors[$payments[$i]->supervisor_id] = $response["result"]["full_name"];
                    }
                }

                $sheet->setCellValue("F" . ($i + 2), $actors[$payments[$i]->supervisor_id]);
            }

            if (!$payments[$i]->isLiquidated()) {
                $sheet->getStyle("C" . ($i + 2) . ":C" . ($i + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
            }
            if (!$payments[$i]->isConfirmed()) {
                $sheet->getStyle("D" . ($i + 2) . ":D" . ($i + 2))->getFont()->getColor()->setARGB(Color::COLOR_RED);
            }

        }

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(33);
        $sheet->getColumnDimension('E')->setWidth(17);
        if ($isadmin) {
            $sheet->getColumnDimension('F')->setWidth(17);
        }

        $sheet->setTitle("Pagos");

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

    public function confirm($payments, $user_id)
    {
        foreach ($payments as $payment) {
            $array = $payment->data;

            $array = $payment->data;
            $array["confirmation_date"] = date("Y-m-d H:i:s");
            $array["confirmation_actor"] = $user_id;
            $payment->supervisor_id = $user_id;

            $payment->data = $array;
            $payment->save();
        }
    }

    public function liquidate($payments, $user_id)
    {
        foreach ($payments as $payment) {
            $array = $payment->data;

            $array["liquidation_date"] = date("Y-m-d H:i:s");
            $array["liquidation_actor"] = $user_id;

            $payment->data = $array;
            $payment->save();
        }
    }

    public function asignSender($payments, $sender_id)
    {
        foreach ($payments as $payment) {
            $array = $payment->data;

            $payment->sender_id = $sender_id;

            $payment->data = $array;
            $payment->save();
        }
    }
    public function asignSupervisor($payments, $supervisor_id)
    {
        foreach ($payments as $payment) {
            $array = $payment->data;

            $payment->supervisor_id = $supervisor_id;

            $payment->data = $array;
            $payment->save();
        }
    }

    public function matchAny($bot, $id1, $id2)
    {
        $sender = $this->getFirst(Payments::class, "id", "=", $id1);
        $supervisor = $this->getFirst(Payments::class, "id", "=", $id2);

        if ($sender && $sender->id > 0 && $supervisor && $supervisor->id > 0) {
            if ($sender->sender_id == null && $supervisor->supervisor_id == null) {
                $aux = $sender;
                $sender = $supervisor;
                $supervisor = $aux;
            }
            $this->match($bot, array(
                array(
                    "sender" => $sender,
                    "supervisor" => $supervisor,
                ),
            ));
        }
        // se notifica la recepcion al remesador en la llamada a match
        // y aqui se notifica al admin q esta haciendo la operacion
        return $this->notifyConfirmationToAdmin($bot);
    }

    public function match($bot, $payments)
    {
        foreach ($payments as $payment) {
            if ($payment["supervisor"] && $payment["supervisor"]->id > 0) {
                $array = $payment["sender"]->data;
                if (!isset($array["previous_screenshot"])) {
                    $array["previous_screenshot"] = array();
                }
                // guardando la captura del remesador en a lista de capturas previas
                $array["previous_screenshot"][] = $payment["sender"]->screenshot;
                // haciendo q la captura principal sea la del supervisor q es lo recibido realmente
                $payment["sender"]->screenshot = $payment["supervisor"]->screenshot;
                // tomando la cantidad real recibida por el supervisor
                $payment["sender"]->amount = $payment["supervisor"]->amount;

                $array["confirmation_date"] = date("Y-m-d H:i:s");
                $array["match_payment_data"] = json_encode($payment["supervisor"]->toArray());

                $payment["sender"]->data = $array;
                $payment["sender"]->supervisor_id = $payment["supervisor"]->supervisor_id;
                $payment["sender"]->save();

                // notificar al remesador q se ha recibido el pago
                $this->notifyConfirmationToOwner($bot, $payment["sender"]);

                $payment["supervisor"]->delete();
            }
        }
    }
    public function requestConfirmation($bot, $payments)
    {

        foreach ($payments as $payment) {
            if ($payment->supervisor_id && $payment->supervisor_id > 0) {
                $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $payment->supervisor_id);
                // solicitar directamente al supervisor asignado
                $supervisorsmenu = $this->getOptionsMenuForThisOne($bot, $payment, 3);
                $this->notifyStatusRequestToSupervisor($bot, $payment, $actor, $supervisorsmenu);
            } else {
                // si no hay supervisor, solicitar a todos los admins
                $supervisorsmenu = $this->getOptionsMenuForThisOne($bot, $payment, 1);
                $admins = $bot->ActorsController->getData(Actors::class, [
                    [
                        "contain" => true,
                        "name" => "admin_level",
                        "value" => [1, 4],
                    ],
                ], $bot->telegram["username"]);
                for ($i = 0; $i < count($admins); $i++) {
                    $this->notifyStatusRequestToSupervisor($bot, $payment, $admins[$i], $supervisorsmenu);
                }
            }
        }
    }

    public function getPrompt($bot, $method)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", $method, $bot->telegram["username"]);

        $reply = array(
            "text" => "💶 *Reportar pago*\n\n_Para reportar un pago, ud debe enviar una captura y poner como descripción de la misma, el nombre y apellidos del remitente y el monto enviado._\n\nEjemplo:    `Juan Perez 20`\n_Así estaríamos informando que Juan Perez ha enviado 20 EUR_\n\n👇 Envíe la captura del pago realizado:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✋ Cancelar", "callback_data" => "menu"]],
                ],
            ]),
        );

        return $reply;
    }

    public function getMenu($bot, $actor)
    {
        $reply = array();

        if ($actor) {
            $menu = array();

            array_push($menu, [["text" => "🔎 Buscar", "callback_data" => "buscar"]]);
            array_push($menu, [
                ["text" => "🤷🏻‍♂️ Sin confirmar", "callback_data" => "/confirm"],
                ["text" => "🫰🏻 Sin liquidar", "callback_data" => "/liquidate"],
            ]);
            array_push($menu, [
                ["text" => "💸 Flotantes", "callback_data" => "floatingpayments-all"],
                ["text" => "🐌 Rezagados", "callback_data" => "promptpaymentdaysold"],
            ]);
            // admin_level = 1 Admnistrador, 4 Admin de capital
            switch ($actor->data[$bot->telegram["username"]]["admin_level"]) {
                case "1":
                case 1:
                    break;
                case "4":
                case 4:
                    break;
                default:
                    break;
            }
            array_push($menu, [["text" => "📝 Exportar pagos", "callback_data" => "getadminallpaymentsmenu"]]);
            array_push($menu, [["text" => "↖️ Volver al menú de administrador", "callback_data" => "adminmenu"]]);

            $reply = array(
                "text" => "💶 *Menú de pagos*!\n\n_Aquí encontrará las opciones sobre los pagos realizados_\n\n👇 Qué desea hacer ahora?",
                "markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            );

        }

        return $reply;
    }

    public function getUnconfirmed($bot, $user_id, $to_id = false)
    {
        $subject = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $user_id);
        $response = array(
            "result" => array(
                "full_name" => "TODOS",
            ),
        );
        if ($subject && $subject->id > 0) {
            $response = json_decode($bot->TelegramController->getUserInfo($subject->user_id, $bot->getToken($bot->telegram["username"])), true);
        }

        if (!$to_id) {
            $to_id = $user_id;
        }

        $text = "👍 *No hay pagos pendientes*\n_Ud no tiene pagos pendientes por confirmar._";
        $menu = [
            [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
        ];
        if ($user_id != $to_id) {
            $text = "👍 *No hay pagos pendientes*\n_{$response['result']['full_name']} no tiene pagos pendientes por confirmar._";
            $menu = [
                [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "/confirm"]],
            ];
        }
        $reply = array(
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        );

        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $to_id);
        $isadmin = $actor->isLevel(1, $bot->telegram["username"]);
        $payments = $this->getUnconfirmedPayments($bot, $user_id);

        if (count($payments) > 0) {
            $amount = 0;
            $count = 0;
            foreach ($payments as $payment) {

                $pendingmenu = $this->getOptionsMenuForThisOne($bot, $payment, $actor->data[$bot->telegram["username"]]["admin_level"]);
                $payment->sendAsTelegramMessage(
                    $bot,
                    $actor,
                    "Reporte de pago pendiente",
                    false,
                    $user_id == "all" ? $payment->sender_id : false,
                    $pendingmenu
                );

                $amount += $payment->amount;
                $count += 1;
            }

            $array = $this->export($bot, $payments, $actor);
            $xlspath = request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"];
            $amount = Moneys::format($amount);

            $fileinfo = "*Total: {$amount}* 💶\n\n📎 Se ha generado un excel con los datos aquí:\n{$xlspath}\n_Este archivo solo estará disponible por " . FileController::$TEMPFILE_DURATION_HOURS . " hrs._";
            $text = "👆 *Pagos pendientes*\n_Estos son {$count} pagos reportados por Ud y que aún no han sido confirmados._\n\n{$fileinfo}";
            if ($isadmin) {
                $text = "👆 *Pagos pendientes*\n_Estos {$count} pagos han sido reportados por {$response['result']['full_name']} y aún no han sido confirmados._\n\n{$fileinfo}";
            }
            $menu = [
                [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
            ];

            if ($user_id != $to_id) {
                $text = "👆 *Pagos pendientes*\n_Estos {$count} pagos han sido reportados por {$response['result']['full_name']} y aún no han sido confirmados._\n\n";
                $menu = [
                    [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "/confirm"]],
                ];
            }

            $reply = array(
                "text" => $text,
                "markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            );

        }

        return $reply;
    }

    public function getUnconfirmedMenuForUsers($bot)
    {
        $senders = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => [1, 2],
            ],

        ], $bot->telegram["username"]);

        $menu = array();
        $total = 0;
        foreach ($senders as $sender) {
            $sender = (new Agents())->newInstance($sender->getAttributes(), true);
            $amount = $sender->unconfirmedMoneys($bot, $this);
            if ($amount > 0) {
                $total += $amount;
                if (count($menu) == 0) {
                    array_push($menu, [
                        ["text" => "👥 Todos", "callback_data" => "unconfirmedpayments-all"],
                    ]);
                }
                $response = json_decode($bot->TelegramController->getUserInfo($sender->user_id, $bot->getToken($bot->telegram["username"])), true);
                array_push($menu, [["text" => $response["result"]["full_name"] . " " . Moneys::format($amount) . " 💶", "callback_data" => "unconfirmedpayments-{$sender->user_id}"]]);
            }
        }
        if (count($menu) > 0) {
            array_push($menu, [
                ["text" => "🔃 Volver a cargar", "callback_data" => "/confirm"],
            ]);
        }

        array_push($menu, [
            ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
        ]);

        $text = "💶 *Pagos sin confirmar a usuarios*\n" .
            "_Aquí puede obtener el reporte de pagos sin confirmar a uno o todos los usuarios_\n\n" .
            "*Total*: " . Moneys::format($total) . " 💶\n" .
            "*Equivalentes a*: " . Moneys::format($bot->ProfitsController->getUSDTtoSendWithActiveRate($total)) . " 💵";
        if (count($menu) == 1) {
            $text .= "\n\n😎 *NO HAY PAGOS SIN CONFIRMAR en este momento*";
        } else {
            $text .= "\n\n👇 De quién desea ver?";
        }
        $reply = array(
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),

        );

        return $reply;
    }

    public function getUnliquidated($bot, $user_id, $to_id = false)
    {
        $subject = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $user_id);
        $response = array(
            "result" => array(
                "full_name" => "TODOS",
            ),
        );
        if ($subject && $subject->id > 0) {
            $response = json_decode($bot->TelegramController->getUserInfo($subject->user_id, $bot->getToken($bot->telegram["username"])), true);
        }

        if (!$to_id) {
            $to_id = $user_id;
        }

        $text = "👍 *No hay pagos pendientes*\n_Ud no tiene pagos pendientes por liquidar._";
        $menu = [
            [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
        ];
        if ($user_id != $to_id) {
            $text = "👍 *No hay pagos pendientes*\n_" . $response["result"]["full_name"] . " NO TIENE pagos pendientes por liquidar._";
            $menu = [
                [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "/liquidate"]],
            ];
        }
        $reply = array(
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        );

        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $to_id);
        $isadmin = $actor->isLevel(1, $bot->telegram["username"]);
        $payments = $this->getUnliquidatedPayments($bot, $user_id);

        if (count($payments) > 0) {
            $amount = 0;
            $count = 0;
            $penalized = array();

            foreach ($payments as $payment) {

                $pendingmenu = $this->getOptionsMenuForThisOne($bot, $payment, $actor->data[$bot->telegram["username"]]["admin_level"]);
                $payment->sendAsTelegramMessage(
                    $bot,
                    $actor,
                    "Pago sin liquidar",
                    false,
                    $user_id == "all" ? $payment->sender_id : false,
                    $pendingmenu
                );

                $amount += $payment->amount;
                $count += 1;

                $penalty = $bot->PenaltiesController->getForAmount($payment->amount);
                if ($penalty && $penalty->id > 0) {
                    $penalized[] = array(
                        "payment" => $payment,
                        "amount" => $penalty->amount,
                    );
                }
            }

            // ajustando la cantidad a pagar en base al cambio vigente
            $liquidate_amount = Moneys::format(MathController::round($bot->ProfitsController->getUSDTtoSendWithActiveRate($amount), 2, true));
            $amount = Moneys::format($amount);

            $array = $this->export($bot, $payments, $actor);
            $xlspath = request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"];

            $text = "👆 *Pagos pendientes*\n_Estos son {$count} pagos confirmados de Ud y que aún no han sido liquidados._\n\n*Total*: {$amount} 💶\n*A liquidar: {$liquidate_amount}* 💵";
            if ($isadmin) {
                $text = "👆 *Pagos pendientes*\n_Estos {$count} pagos han sido confirmados a {$response['result']['full_name']} y aún no han sido liquidados._\n\n*Total: {$amount}* 💶\n*A liquidar: {$liquidate_amount}* 💵";
            }
            $menu = [
                [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
            ];

            if ($user_id != $to_id) {
                $text = "👆 *Pagos pendientes*\n_Estos {$count} pagos han sido confirmados a {$response['result']['full_name']} y aún no han sido liquidados._\n\n*Total: {$amount}* 💶\n*A liquidar: {$liquidate_amount}* 💵";
                $menu = [
                    [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "/liquidate"]],
                ];
            }

            // Si es un admin1 le muestro las penalizaciones
            if ($isadmin) {
                if (count($penalized) > 0) {
                    $text .= "\n\n😳 Deben ser revisados por penalización:";
                }
                foreach ($penalized as $penalty) {
                    $penalized_amount = Moneys::format(MathController::round($penalty["payment"]->amount - ($penalty["payment"]->amount * $penalty["amount"] / 100)));
                    $text .= "\n🆔 `" . $penalty["payment"]->id . "`:     ▪️ *{$penalized_amount}* 💶   /   ▫️ _" . Moneys::format($penalty["payment"]->amount) . "_ 💶";
                }

                if (isset($response['result']['formated_username'])) {
                    $text .= "\n\n👉 @{$response['result']['formated_username']}";
                }
                //Log::info("PaymentsController getUnliquidated subject: " . json_encode($subject->data[$bot->telegram["username"]]));
                if (
                    isset($subject->data[$bot->telegram["username"]]["metadatas"]) &&
                    isset($subject->data[$bot->telegram["username"]]["metadatas"]["wallet"]) &&
                    $subject->data[$bot->telegram["username"]]["metadatas"]["wallet"] != ""
                ) {
                    $text .= "\n💰 `" . $subject->data[$bot->telegram["username"]]["metadatas"]["wallet"] . "`";
                }

            }

            $text .= "\n\n📎 Se ha generado un excel con los datos aquí:\n{$xlspath}\n_Este archivo solo estará disponible por " . FileController::$TEMPFILE_DURATION_HOURS . " hrs._";

            $reply = array(
                "text" => $text,
                "markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            );

        }

        return $reply;
    }

    public function getUnliquidatedMenuForUsers($bot)
    {
        $senders = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => [1, 2],
            ],
        ], $bot->telegram["username"]);

        $menu = array();
        $total = 0;
        foreach ($senders as $sender) {
            $sender = (new Agents())->newInstance($sender->getAttributes(), true);
            $amount = $sender->liquidatedMoneys($bot, $this);
            if ($amount > 0) {
                $total += $amount;
                if (count($menu) == 0) {
                    array_push($menu, [
                        ["text" => "👥 Todos", "callback_data" => "unliquidatedpayments-all"],
                    ]);
                }

                array_push($menu, [["text" => $sender->getTelegramInfo($bot, "full_name") . "- " . Moneys::format($bot->ProfitsController->getUSDTtoSendWithActiveRate($amount)) . " 💵", "callback_data" => "unliquidatedpayments-{$sender->user_id}"]]);
            }
        }
        if (count($menu) > 0) {
            array_push($menu, [
                ["text" => "🔃 Volver a cargar", "callback_data" => "/liquidate"],
            ]);
        }
        array_push($menu, [
            ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
        ]);

        $text = "💶 *Pagos sin liquidar a usuarios*\n" .
            "_Aquí puede obtener el reporte de pagos sin liquidar a uno o todos los usuarios_\n\n" .
            "*Total*: " . Moneys::format($total) . " 💶\n" .
            "*Equivalentes a*: " . Moneys::format($bot->ProfitsController->getUSDTtoSendWithActiveRate($total)) . " 💵";
        if (count($menu) == 1) {
            $text .= "\n\n😎 *NO HAY PAGOS SIN LIQUIDAR en este momento*";
        } else {
            $text .= "\n\n👇 De quién desea ver?";
        }
        $reply = array(
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),

        );

        return $reply;
    }

    public function getFloating($bot, $user_id, $to_id = false)
    {
        if (!$to_id) {
            $to_id = $user_id;
        }

        $reply = array(
            "text" => "👍 *No hay pagos flotantes*\n_No existen pagos flotantes en sistema._",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "↖️ Volver al menú de pagos", "callback_data" => "/payments"]],
                ],
            ]),
        );

        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $to_id);
        $isadmin = $actor->isLevel(1, $bot->telegram["username"]);
        $payments = $this->getFloatingPayments();

        if (count($payments) > 0) {
            $amount = 0;
            $count = 0;
            foreach ($payments as $payment) {
                $pendingmenu = $this->getOptionsMenuForThisOne($bot, $payment, $actor->data[$bot->telegram["username"]]["admin_level"]);
                $payment->sendAsTelegramMessage(
                    $bot,
                    $actor,
                    "Reporte de pago flotante",
                    false,
                    true,
                    $pendingmenu
                );

                $amount += $payment->amount;
                $count += 1;
            }

            $array = $this->export($bot, $payments, $actor);
            $xlspath = request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"];

            $fileinfo = "*Total: {$amount}* 💶\n\n📎 Se ha generado un excel con los datos aquí:\n{$xlspath}\n_Este archivo solo estará disponible por " . FileController::$TEMPFILE_DURATION_HOURS . " hrs._";
            $text = "👆 *Pagos flotantes*\n_Estos son {$count} pagos flotantes._\n\n{$fileinfo}";
            if ($isadmin) {
                $text = "👆 *Pagos flotantes*\n_Estos {$count} pagos flotantes._\n\n{$fileinfo}";
            }
            $menu = [
                [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
            ];

            if ($user_id != $to_id) {
                $text = "👆 *Pagos flotantes*\n_Estos {$count} pagos flotantes._\n\n{$fileinfo}";
                $menu = [
                    [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
                ];
            }

            $reply = array(
                "text" => $text,
                "markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            );

        }

        return $reply;
    }

    public function getAllMenuForUsers($bot)
    {
        $senders = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => 2,
            ],
        ], $bot->telegram["username"]);
        $menu = array();
        if (count($senders) > 0) {
            array_push($menu, [
                ["text" => "👥 Todos", "callback_data" => "allpayments-all"],
            ]);
        }

        foreach ($senders as $sender) {
            $response = json_decode($bot->TelegramController->getUserInfo($sender->user_id, $bot->getToken($bot->telegram["username"])), true);
            array_push($menu, [["text" => $response["result"]["full_name"], "callback_data" => "allpayments-{$sender->user_id}"]]);
        }
        array_push($menu, [
            ["text" => "↖️ Volver al menú de pagos", "callback_data" => "/payments"],
        ]);

        $reply = array(
            "text" => "💶 *Pagos por usuarios*\n\n_Aquí puede obtener el reporte de pagos de uno o todos los usuarios_\n\n👇 De quién desea ver?",
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),

        );

        return $reply;
    }

    public function getAllList($bot, $user_id, $to_id = false)
    {
        if (!$to_id) {
            $to_id = $user_id;
        }

        $text = "👍 *No hay pagos*\n_Ud no tiene pagos reportados._";
        $menu = [
            [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
        ];
        if ($user_id != $to_id) {
            $text = "👍 *No hay pagos*\n_El usuario no tiene pagos reportados._";
            $menu = [
                [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "getadminallpaymentsmenu"]],
            ];
        }
        $reply = array(
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        );

        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $to_id);
        $isadmin = $actor->isLevel(1, $bot->telegram["username"]);
        $payments = $this->getAllPayments($bot, $user_id);

        if (count($payments) > 0) {
            $amount = 0;
            $count = 0;
            foreach ($payments as $payment) {
                $amount += $payment->amount;
                $count += 1;
            }

            $array = $this->export($bot, $payments, $actor);
            $xlspath = request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"];

            $text = "👆 *Pagos*\n_Estos son {$count} pagos reportados por Ud.";
            $menu = [
                [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
            ];
            if ($isadmin || $user_id != $to_id) {
                $text = "👆 *Pagos*\n_Estos {$count} pagos han sido reportados.";
                $menu = [
                    [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "getadminallpaymentsmenu"]],
                ];
            }

            $text .= "_\n\n*Total: {$amount}* 💶\n\n📎 Se ha generado un excel con los datos aquí:\n{$xlspath}\n_Este archivo solo estará disponible por " . FileController::$TEMPFILE_DURATION_HOURS . " hrs._";

            $reply = array(
                "text" => $text,
                "markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            );

        }

        return $reply;
    }

    public function notifyToCapitals($bot, $payment, $message = false, $title = "")
    {
        $supervisorsmenu = $this->getOptionsMenuForThisOne($bot, $payment, 1);

        $admins = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => 4,
            ],
        ], $bot->telegram["username"]);
        for ($i = 0; $i < count($admins); $i++) {
            $payment->sendAsTelegramMessage(
                $bot,
                $admins[$i],
                $title,
                $message,
                true,
                $supervisorsmenu
            );
        }
    }

    public function notifyToGestors($bot, $payment, $message = false, $title = "")
    {
        $supervisorsmenu = $this->getOptionsMenuForThisOne($bot, $payment, 1);

        $admins = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => 1,
            ],
        ], $bot->telegram["username"]);
        for ($i = 0; $i < count($admins); $i++) {
            $payment->sendAsTelegramMessage(
                $bot,
                $admins[$i],
                $title,
                $message,
                true,
                $supervisorsmenu
            );
        }
    }
    public function notifyConfirmationToOwner($bot, $payment)
    {
        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $payment->sender_id);
        $payment->sendAsTelegramMessage(
            $bot,
            $actor,
            "Pago confirmado",
            "✅ _El siguiente pago reportado por Ud ha sido recibido en nuestras cuentas_",
            true,
            [
                [
                    ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                ],

            ]
        );
    }

    public function notifyConfirmationToAdmin($bot)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", "", $bot->telegram["username"]);

        $reply = array(
            "text" => "✅ *Pago confirmado*\n_Ud ha confirmado satisfactoriamente el pago recibido_\n\nSe le ha enviado notificación a quien reportó este pago para que esté al tanto de esta confirmación.\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        );

        return $reply;
    }

    public function notifyAfterAsign($bot, $user_id)
    {
        // obteniendo datos del usuario de telegram
        $response = json_decode($bot->TelegramController->getUserInfo($user_id, $bot->getToken($bot->telegram["username"])), true);
        $reply = array(
            "text" => "🆗 *Reporte de pago asignado*\n\n" . $response["result"]["full_info"] . "\n\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        );

        return $reply;
    }

    public function notifyStatusRequestToSupervisor($bot, $payment, $actor, $supervisorsmenu)
    {
        $payment->sendAsTelegramMessage(
            $bot,
            $actor,
            "Reporte de pago",
            "⚠️ _Se ha solicitado actualización de estado del siguiente pago:_",
            true,
            $supervisorsmenu
        );
    }

    public function notifyStatusNotYetToOwner($bot, $payment)
    {
        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $payment->sender_id);

        $payment->sendAsTelegramMessage(
            $bot,
            $actor,
            "Reporte de pago",
            "🤷🏻‍♂️ _Su pago aún no ha sido recibido:_",
            false,
            [
                [
                    ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                ],

            ]
        );
    }

    public function notifyStatusNotYetToAdmin()
    {
        $reply = array(
            "text" => "👍 *Respuesta enviada*\n_Se le ha notificado al usuario que su pago aún no ha sido recibido._\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        );

        return $reply;
    }

    public function notifyAfterStatusRequest()
    {
        $reply = array(
            "text" => "👍 *Solicitud de confirmación*\n_Se ha enviado solicitud de confirmación del pago a las personas encargadas de procesarlo.\nPor favor, sea paciente, le notificaremos lo antes posible._\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        );

        return $reply;
    }
    public function notifyAfterLiquidate()
    {
        $reply = array(
            "text" => "✅ *Pago liquidado*\n_Ud ha liquidado satisfactoriamente el pago_\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        );

        return $reply;
    }

    public function notifyAfterReceived($bot, $payment, $user_id)
    {
        $reply = array();

        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $user_id);
        $array = $actor->data;
        if (isset($array[$bot->telegram["username"]]["last_bot_callback_data"])) {
            switch ($array[$bot->telegram["username"]]["last_bot_callback_data"]) {
                case "getsenderpaymentscreenshot":
                    $reply = $this->getMessageTemplate(
                        $bot,
                        $payment,
                        $payment->sender_id,
                        "Reporte de pago en revisión",
                        "✅ _Se ha solicitado a los ADMINISTRADORES confimación de recepción de su pago en nuestras cuentas.\nSe le notificará automáticamente por esta vía._",
                        false,
                        [
                            [
                                ["text" => "💶 Reportar otro pago", "callback_data" => "senderpaymentmenu"],
                            ],
                            [
                                ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                            ],

                        ]
                    );
                    $reply = $reply["message"];
                    $reply["markup"] = $reply["reply_markup"];
                    break;
                case "getsupervisorpaymentscreenshot":
                    $reply = $this->getMessageTemplate(
                        $bot,
                        $payment,
                        $payment->sender_id,
                        "Recepción de pago completada",
                        false,
                        false,
                        [
                            [
                                ["text" => "👍 Reportar otra recepción de pago", "callback_data" => "supervisorpaymentmenu"],
                            ],
                            [
                                ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                            ],

                        ]
                    );
                    $reply = $reply["message"];
                    $reply["markup"] = $reply["reply_markup"];
                    break;
                case "getforwardedpaymentscreenshot":
                    $reply = $this->getMessageTemplate(
                        $bot,
                        $payment,
                        $payment->sender_id,
                        "Reporte de pago en revisión",
                        false,
                        true,
                        []
                    );
                    $reply = $reply["message"];
                    $reply["markup"] = $reply["reply_markup"];
                    break;
                default:
                    break;
            }
        }
        $array[$bot->telegram["username"]]["last_bot_callback_data"] = "";
        $actor->data = $array;
        $actor->save();

        return $reply;
    }

    public function getComments($bot, $payment_id, $to_id)
    {
        $text = "👍 *No hay comentarios*\n_No existen comentarios sobre este pago._";
        $menu = [
            [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
        ];
        $reply = array(
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        );

        $comments = $bot->CommentsController->getByPaymentId($payment_id);

        if (count($comments) > 0) {
            $count = 0;
            $screenshot = false;
            foreach ($comments as $comment) {
                $array = $bot->CommentsController->getMessageTemplate(
                    $bot,
                    $comment,
                    $to_id,
                );

                if (isset($array["message"]["photo"]) && $array["message"]["photo"] && !$screenshot) {
                    $bot->TelegramController->sendPhoto($array, $bot->getToken($bot->telegram["username"]));
                    $screenshot = true;
                } else {
                    $bot->TelegramController->sendMessage($array, $bot->getToken($bot->telegram["username"]));
                }
                $count += 1;
            }

            $text = "👆 *Comentarios sobre el pago*\n_Estos son {$count} comentarios sobre el pago seleccionado._";
            $menu = [
                [["text" => "🔃 Mostrar nuevamente el Pago", "callback_data" => "/buscar {$payment_id}"]],
                [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
            ];

            $reply = array(
                "text" => $text,
                "markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            );


        }

        return $reply;
    }

    public function notifyAfterUnconfirm()
    {
        $reply = array(
            "text" => "👍 *Pago desconfirmado*\n_Se ha desconfirmado el pago satisfactoriamente._\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        );

        return $reply;
    }

    public function renderPaymentsByDate($bot, $days, $title, $extra_options = array())
    {
        $payments = $this->getUnconfirmedMoneysBeforeDate($bot, Payments::class, Carbon::now()->addDays($days)->toDateString());

        return $this->renderPaymentsAfterSearch($bot, $payments, $title, $extra_options);
    }

    public function renderPaymentsByAny($bot, $text, $title, $extra_options = array())
    {
        $payments = $this->searchMoneysByAny(Payments::class, $text);

        return $this->renderPaymentsAfterSearch($bot, $payments, $title, $extra_options);
    }

    public function renderPaymentsAfterSearch($bot, $payments, $title, $extra_options = array())
    {
        $reply = array();

        $options = array(
            [
                ["text" => "🔎 Buscar otro", "callback_data" => "buscar"],
            ]
        );
        foreach ($extra_options as $key => $option) {
            $options[] = $option;
        }

        $amount = 0;
        if (count($payments) <= 20) {
            foreach ($payments as $payment) {
                $owner = $this->getFirst(Actors::class, "user_id", "=", $payment->sender_id);

                if (
                        // si es un admin
                    ($bot->actor->isLevel(1, $bot->telegram["username"]) || $bot->actor->isLevel(4, $bot->telegram["username"])) ||
                        // si es el q lo subio
                    ($payment->sender_id == $bot->actor->user_id || $payment->supervisor_id == $bot->actor->user_id) ||
                        // si quien lo subio, es descendiente del actor
                    ($owner && $owner->id > 0 && $owner->isDescendantOf($bot->actor->user_id))
                ) {
                    // preparar el menu de opciones sobre este pago
                    $menu = $this->getOptionsMenuForThisOne($bot, $payment, $bot->actor->data[$bot->telegram["username"]]["admin_level"]);
                    $payment->sendAsTelegramMessage(
                        $bot,
                        $bot->actor,
                        $title,
                        false,
                        true,
                        $menu
                    );
                    $amount++;
                }
            }
        }

        if (count($payments) > 0) {
            if (count($payments) <= 20) {
                $reply = array(
                    "text" => "👆 *Registros encontrados*\n_Estos son los {$amount} pagos que cumplen con su criterio de búsqueda._\n\n👇 Qué desea hacer ahora?",
                    "markup" => json_encode([
                        "inline_keyboard" => $options,
                    ]),
                );
            } else {
                $reply = array(
                    "text" => "🤯 *" . count($payments) . " registros encontrados*\n_Se han encontrado " . count($payments) . " pagos que cumplen con su criterio de búsqueda._\n*Son muchos para enviarle los mensages correspondientes.*\n_Intente buscar por otro criterio para obtener menos resultados._\n\n👇 Qué desea hacer ahora?",
                    "markup" => json_encode([
                        "inline_keyboard" => $options,
                    ]),
                );
            }
        } else {
            $reply = array(
                "text" => "🤦‍♂️ *Registro no encontrado*\n_No se ha encontrado ningún registo que cumpla con su criterio de búsqueda._\n\n👇 Qué desea hacer ahora?",
                "markup" => json_encode([
                    "inline_keyboard" => $options,
                ]),
            );
        }

        return $reply;
    }

    public function getPaymentsStats($bot, $start_date = false, $end_date = false)
    {
        $date_rage = true;
        $from_date = Carbon::createFromFormat("Y-m-d H:i:s", Carbon::now()->format("Y-m-d") . " 00:00:00");
        $to_date = Carbon::createFromFormat("Y-m-d H:i:s", Carbon::now()->format("Y-m-d") . " 23:59:59");
        switch (gettype($end_date)) {
            case "boolean":
                $date_rage = false;
                // no han enviado fecha de fin por lo q se toma 1 mes hacia atras desde el dia de hoy
                $from_date = $to_date->clone()->subMonths(1);
                if ($start_date) {
                    $from_date = Carbon::createFromFormat("Y-m-d H:i:s", $start_date . " 00:00:00");
                }
                break;
            case "integer":
                $date_rage = false;
                // han enviado cantidad de dias
                if ($start_date) {
                    $to_date = Carbon::createFromFormat("Y-m-d H:i:s", $start_date . " 23:59:59");
                }
                $from_date = $to_date->clone()->subDays($end_date);
                break;
            case "string":
                try {
                    $to_date = Carbon::createFromFormat("Y-m-d H:i:s", $end_date . " 23:59:59");
                    if (!$start_date) {
                        $from_date = $to_date->clone()->subMonths(1);
                    } else {
                        $from_date = Carbon::createFromFormat("Y-m-d H:i:s", $start_date . " 00:00:00");
                    }

                } catch (\Throwable $th) {
                    $to_date = Carbon::createFromFormat("Y-m-d H:i:s", $start_date . " 23:59:59");
                    $from_date = $to_date->clone()->subDays($end_date);
                }

                break;
            default:
                break;
        }

        $array = array();
        if ($date_rage) {
            $array = $this->getInfo($bot, $from_date, $to_date);
        } else {
            $array = $this->getInfo($bot, false, $to_date);
        }

        //dd($array);
        return array(
            "items" => $array,
            "from_date" => $from_date,
            "to_date" => $to_date,
        );
    }

}
