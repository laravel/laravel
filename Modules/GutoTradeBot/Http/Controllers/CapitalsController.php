<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\FileController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\GutoTradeBot\Entities\Capitals;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CapitalsController extends MoneysController
{
    public function create($amount, $comment, $screenshot, $sender_id, $supervisor_id, $data = array())
    {
        $capital = parent::createByModel(Capitals::class, $amount, $comment, $screenshot, $sender_id, $supervisor_id, $data);
        Log::channel('storage')->info('capital ' . json_encode($capital->toArray()));

        return $capital;
    }

    public function getUnmatched($id)
    {
        return parent::getUnmatchedMoneys(Capitals::class, $id);

    }
    public function getUnmatchedByAmount($amount, $id)
    {
        return parent::getUnmatchedMoneysByAmount(Capitals::class, $amount, $id);
    }
    public function getUnconfirmedCapitals($bot, $user_id = false)
    {
        return parent::getUnconfirmedMoneys($bot, Capitals::class, $user_id);
    }
    public function getAllCapitals($bot, $user_id = false)
    {
        return parent::getAllMoneys($bot, Capitals::class, $user_id);
    }
    public function getSentByQuery($sender = false)
    {
        $query = Capitals::whereRaw("JSON_EXTRACT(data, '$.fullname') = ?", [$sender]);

        return $query;
    }

    public function getSentBySumQuery($field, $sender = false)
    {
        $query = Capitals::select(DB::raw('SUM(' . $field . ') as total_amount'))
            ->whereRaw("JSON_EXTRACT(data, '$.fullname') = ?", [$sender]);

        $results = $query->get();

        return $results[0]->toArray()["total_amount"];
    }

    public function import($bot)
    {
        $path = public_path() . "/import.xlsm";

        $spreadsheet = IOFactory::load($path);
        //$sheet = $spreadsheet->getActiveSheet();
        $sheet = $spreadsheet->getSheetByName('Recibos');

        // Leer el excel
        $highestRow = $sheet->getHighestRow();
        $data = [];
        // Define las columnas a analizar
        $columns = ['A', 'B', 'C'];
        for ($row = 1; $row <= $highestRow; $row++) {
            foreach ($columns as $column) {
                $cellCoordinate = $column . $row;

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
                    'confirmed' => true,
                    'date' => $date,
                ];
            }
        }
        // Quitando el encabezado
        unset($data[1]);
        // Guardar en la BD los registros
        foreach ($data as $value) {
            $date = $value["A"]["date"];
            $amount = $value["B"]["value"];
            if ($amount > 0) { // validando q sea mayor q 0... si el excel esta en desarrollo da error
                $confirmed = $value["C"]["confirmed"];
                $sender_id = 5482646491;

                $comment = $bot->ProfitsController->getEURtoSendWithActiveRate($amount);

                $json = array(
                    "message_id" => 1,
                );
                if ($confirmed) {
                    $json["confirmation_date"] = $date . " " . date("H:i:s");
                    $json["confirmation_message"] = 1;
                }
                $capital = Capitals::create([
                    'amount' => $comment,
                    'comment' => $amount,
                    'screenshot' => MoneysController::$NOSCREENSHOT_PATH,
                    'sender_id' => $sender_id,
                    'supervisor_id' => 816767995,
                    'data' => $json,
                ]);
                $capital->created_at = Carbon::createFromFormat("Y-m-d H:i:s", $date . " " . date("H:i:s"));
                $capital->save();
            }
        }
    }

    public function export($bot, $capitals, $actor)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue("A1", "Fecha");
        $sheet->setCellValue("B1", "Recibido");
        $sheet->setCellValue("C1", "A enviar");

        for ($i = 0; $i < count($capitals); $i++) {
            $sheet->setCellValue("A" . ($i + 2), Carbon::createFromFormat("Y-m-d H:i:s", $actor->getLocalDateTime($capitals[$i]->created_at, $bot->telegram["username"]))->toDateString());
            $sheet->setCellValue("B" . ($i + 2), $capitals[$i]->comment);
            $sheet->setCellValue("C" . ($i + 2), $capitals[$i]->amount);

            if (!$capitals[$i]->isConfirmed()) {
                $sheet->getStyle("B" . ($i + 2) . ":B" . ($i + 2))->getFont()->getColor()->setARGB(Color::COLOR_RED);
            }

        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->setTitle("Recibos");

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

    public function confirm($capitals, $user_id)
    {
        foreach ($capitals as $capital) {
            $array = $capital->data;

            $array = $capital->data;
            $array["confirmation_date"] = date("Y-m-d H:i:s");
            $array["confirmation_actor"] = $user_id;
            $capital->supervisor_id = $user_id;
            $capital->data = $array;

            $capital->data = $array;
            $capital->save();
        }
    }

    public function asignSupervisor($capitals, $supervisor_id)
    {
        foreach ($capitals as $capital) {
            $array = $capital->data;

            $capital->supervisor_id = $supervisor_id;

            $capital->data = $array;
            $capital->save();
        }
    }
    public function requestConfirmation($bot, $capitals)
    {
        foreach ($capitals as $capital) {
            if ($capital->supervisor_id && $capital->supervisor_id > 0) {
                // solicitar directamente al supervisor asignado
                $supervisorsmenu = $this->getOptionsMenuForThisOne($bot, $capital, 3);
                $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $capital->supervisor_id);
                $this->notifyStatusRequestToSupervisor($bot, $capital, $actor, $supervisorsmenu);
            } else {
                // si no hay supervisor, solicitar a todos los admins
                $supervisorsmenu = $this->getOptionsMenuForThisOne($bot, $capital, 1);
                $admins = $bot->ActorsController->getData(Actors::class, [
                    [
                        "contain" => true,
                        "name" => "admin_level",
                        "value" => [1, 4],
                    ],
                ], $bot->telegram["username"]);
                for ($i = 0; $i < count($admins); $i++) {
                    $this->notifyStatusRequestToSupervisor($bot, $capital, $admins[$i], $supervisorsmenu);
                }
            }
        }
    }

    public function getPrompt($bot, $method)
    {
        $bot->ActorsController->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", $method, $bot->telegram["username"]);

        $reply = array(
            "text" => "💰 *Reportar aporte de capital*\n\n_Para reportar un aporte de capital, ud debe enviar una captura y poner como descripción de la misma, el nombre y apellidos del remitente y el monto enviado._\n\nEjemplo:    *Juan Perez 1200*\n_Así estaríamos informando que Juan Perez ha enviado 1200 USDT_\n\n👇 Envíe la captura del aporte de capital realizado:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✋ Cancelar", "callback_data" => "menu"]],
                ],
            ]),
        );

        return $reply;
    }

    public function getMenu($actor)
    {
        $reply = array();

        $menu = array();

        if ($actor) {
            //array_push($menu, [["text" => "💰 Reportar aporte de capital realizado", "callback_data" => "sendercapitalmenu"]]);
            array_push($menu, [
                ["text" => "🤷🏻‍♂️ Sin confirmar", "callback_data" => "getadminunconfirmedcapitalsmenu"],
            ]);
            array_push($menu, [["text" => "📝 Exportar aportes de capital", "callback_data" => "getadminallcapitalsmenu"]]);
            array_push($menu, [["text" => "↖️ Volver al menú de administrador", "callback_data" => "adminmenu"]]);

            $reply = array(
                "text" => "💰 *Menú de aportes de capital*!\n\n_Aquí encontrará las opciones sobre los aportes de capital realizados_\n\n👇 Qué desea hacer ahora?",
                "markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            );

        }

        return $reply;
    }

    public function getUnconfirmed($bot, $user_id, $to_id = false)
    {
        if (!$to_id) {
            $to_id = $user_id;
        }

        $text = "👍 *No hay aportes de capital pendientes*\n_Ud no tiene aportes de capital pendientes por confirmar._";
        $menu = [
            [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
        ];
        if ($user_id != $to_id) {
            $text = "👍 *No hay aportes de capital pendientes*\n_El usuario no tiene aportes de capital pendientes por confirmar._";
            $menu = [
                [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "getadminunconfirmedcapitalsmenu"]],
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
        $capitals = $this->getUnconfirmedCapitals($bot, $user_id);

        if (count($capitals) > 0) {
            $amount = 0;
            $count = 0;
            foreach ($capitals as $capital) {

                $pendingmenu = array();
                if ($isadmin) {
                    $pendingmenu = $this->getOptionsMenuForThisOne($bot, $capital, 1);
                    if ($capital->supervisor_id && $capital->supervisor_id > 0) {
                        array_unshift($pendingmenu, [
                            ["text" => "⚠️ Solicitar confirmación", "callback_data" => "requestcapitalconfirmation-{$capital->id}"],
                        ]);
                    }
                } else {
                    array_unshift($pendingmenu, [
                        ["text" => "⚠️ Solicitar confirmación", "callback_data" => "requestcapitalconfirmation-{$capital->id}"],
                    ]);
                }

                $capital->sendAsTelegramMessage(
                    $bot,
                    $actor,
                    "Aporte de capital pendiente",
                    false,
                    $user_id == "all" ? $capital->sender_id : false,
                    $pendingmenu
                );

                $amount += $capital->amount;
                $count += 1;
            }

            $array = $this->export($bot, $capitals, $actor);
            $xlspath = request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"];

            $text = "👆 *Aportes de capital pendientes*\n_Estos son {$count} aportes de capital reportados por Ud y que aún no han sido confirmados._\n*Total: {$amount}* 💰\n\n📎 Se ha generado un excel con los datos aquí:\n{$xlspath}\n_Este archivo solo estará disponible por " . FileController::$TEMPFILE_DURATION_HOURS . " hrs._";
            if ($isadmin) {
                $text = "👆 *Aportes de capital pendientes*\n_Estos {$count} aportes de capital han sido reportados y aún no han sido confirmados._\n*Total: {$amount}* 💰\n\n📎 Se ha generado un excel con los datos aquí:\n{$xlspath}\n_Este archivo solo estará disponible por " . FileController::$TEMPFILE_DURATION_HOURS . " hrs._";
            }
            $menu = [
                [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
            ];

            if ($user_id != $to_id) {
                $text = "👆 *Aportes de capital pendientes*\n_Estos {$count} aportes de capital han sido reportados y aún no han sido confirmados._\n*Total: {$amount}* 💰\n\n📎 Se ha generado un excel con los datos aquí:\n{$xlspath}\n_Este archivo solo estará disponible por " . FileController::$TEMPFILE_DURATION_HOURS . " hrs._";
                $menu = [
                    [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "getadminunconfirmedcapitalsmenu"]],
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
                "value" => 4,
            ],
        ], $bot->telegram["username"]);
        $menu = array();
        array_push($menu, [
            ["text" => "👥 Todos", "callback_data" => "unconfirmedcapitals-all"],
        ]);
        foreach ($senders as $sender) {
            $suscriptor = $bot->AgentsController->getSuscriptor($bot, $sender->user_id, true);
            array_push($menu, [["text" => $suscriptor->getTelegramInfo($bot, "full_name"), "callback_data" => "unconfirmedcapitals-{$sender->user_id}"]]);
        }
        array_push($menu, [
            ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
        ]);

        $reply = array(
            "text" => "💰 *Aportes de capital sin confirmar por usuarios*\n\n_Aquí puede obtener el reporte de aportes de capital sin confirmar de uno o todos los usuarios_\n\n👇 De quién desea ver?",
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),

        );

        return $reply;
    }

    public function getAllMenuForUsers($bot)
    {
        $senders = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => 4,
            ],
        ], $bot->telegram["username"]);
        $menu = array();
        array_push($menu, [
            ["text" => "👥 Todos", "callback_data" => "allcapitals-all"],
        ]);
        foreach ($senders as $sender) {
            $suscriptor = $bot->AgentsController->getSuscriptor($bot, $sender->user_id, true);
            array_push($menu, [["text" => $suscriptor->getTelegramInfo($bot, "full_name"), "callback_data" => "allcapitals-{$sender->user_id}"]]);
        }
        array_push($menu, [
            ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
        ]);

        $reply = array(
            "text" => "💰 *Aportes de capital por usuarios*\n\n_Aquí puede obtener el reporte de aportes de capital de uno o todos los usuarios_\n\n👇 De quién desea ver?",
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

        $text = "👍 *No hay aportes*\n_Ud no tiene aportes de capital reportados._";
        $menu = [
            [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
        ];
        if ($user_id != $to_id) {
            $text = "👍 *No hay aportes*\n_El usuario no tiene aportes de capital reportados._";
            $menu = [
                [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "getadminallcapitalsmenu"]],
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
        $capitals = $this->getAllCapitals($bot, $user_id);

        if (count($capitals) > 0) {
            $amount = 0;
            $count = 0;
            foreach ($capitals as $capital) {
                $amount += $capital->amount;
                $count += 1;
            }

            $array = $this->export($bot, $capitals, $actor);
            $xlspath = request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"];

            $text = "👆 *Aportes de capital*\n_Estos son {$count} aportes reportados por Ud.";
            $menu = [
                [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
            ];
            if ($isadmin || $user_id != $to_id) {
                $text = "👆 *Aportes de capital*\n_Estos {$count} aportes han sido reportados.";
                $menu = [
                    [["text" => "↖️ Volver al menú de usuarios", "callback_data" => "getadminallcapitalsmenu"]],
                ];
            }

            $text .= "_\n*Total: {$amount}* 💶\n\n📎 Se ha generado un excel con los datos aquí:\n{$xlspath}\n_Este archivo solo estará disponible por " . FileController::$TEMPFILE_DURATION_HOURS . " hrs._";

            $reply = array(
                "text" => $text,
                "markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            );

        }

        return $reply;
    }

    public function notifyNew($bot, $capital, $actor, $supervisorsmenu)
    {
        $capital->sendAsTelegramMessage(
            $bot,
            $actor,
            "Nuevo aporte de capital",
            false,
            true,
            $supervisorsmenu
        );
    }

    public function notifyToGestors($bot, $capital)
    {
        $supervisorsmenu = $this->getOptionsMenuForThisOne($bot, $capital, 1);

        $admins = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => [1],
            ],
        ], $bot->telegram["username"]);
        for ($i = 0; $i < count($admins); $i++) {
            $this->notifyNew($bot, $capital, $admins[$i], $supervisorsmenu);
        }
    }
    public function notifyConfirmationToOwner($bot, $capital)
    {
        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $capital->sender_id);

        $capital->sendAsTelegramMessage(
            $bot,
            $actor,
            "Aporte de capital confirmado",
            "✅ _El siguiente aporte de capital reportado por Ud ha sido recibido en nuestras cuentas_",
            false,
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
            "text" => "✅ *Aporte de capital confirmado*\n_Ud ha confirmado satisfactoriamente el aporte de capital recibido_\n\nSe le ha enviado notificación a quien reportó este aporte de capital para que esté al tanto de esta confirmación.\n\n👇 Qué desea hacer ahora?",
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
        $suscriptor = $bot->AgentsController->getSuscriptor($bot, $user_id, true);
        $reply = array(
            "text" => "🆗 *Aporte de capital asignado*\n\n" . $suscriptor->getTelegramInfo($bot, "full_info") . "\n\n\n👇 Qué desea hacer ahora?",
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

    public function notifyStatusRequestToSupervisor($bot, $capital, $actor, $supervisorsmenu)
    {
        $capital->sendAsTelegramMessage(
            $bot,
            $actor,
            "Aporte de capital",
            "⚠️ _Se ha solicitado actualización de estado del siguiente aporte de capital:_",
            true,
            $supervisorsmenu
        );
    }

    public function notifyStatusNotYetToOwner($bot, $capital)
    {
        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $capital->sender_id);

        $capital->sendAsTelegramMessage(
            $bot,
            $actor,
            "Aporte de capital",
            "🤷🏻‍♂️ _Su aporte de capital aún no ha sido recibido:_",
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
            "text" => "👍 *Respuesta enviada*\n_Se le ha notificado al usuario que su aporte de capital aún no ha sido recibido._\n\n👇 Qué desea hacer ahora?",
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
            "text" => "👍 *Solicitud de confirmación*\n_Se ha enviado solicitud de confirmación del aporte de capital a las personas encargadas de procesarlo.\nPor favor, sea paciente, le notificaremos lo antes posible._\n\n👇 Qué desea hacer ahora?",
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

    public function notifyAfterReceived($bot, $capital, $user_id)
    {
        $reply = array();

        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $user_id);
        $array = $actor->data;
        if (isset($array[$bot->telegram["username"]]["last_bot_callback_data"])) {
            switch ($array[$bot->telegram["username"]]["last_bot_callback_data"]) {
                case "getsendercapitalscreenshot":
                    $reply = $this->getMessageTemplate(
                        $bot,
                        $capital,
                        $capital->sender_id,
                        "Aporte de capital recibido",
                        "✅ _Se ha solicitado a los GESTORES confimación de recepción de su aporte de capital en nuestras cuentas.\nSe le notificará automáticamente por esta vía._",
                        false,
                        [
                            [
                                ["text" => "💰 Reportar otro aporte de capital", "callback_data" => "sendercapitalmenu"],
                            ],
                            [
                                ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                            ],

                        ]
                    );
                    $reply = $reply["message"];
                    $reply["markup"] = $reply["reply_markup"];
                    break;
                case "getsupervisorcapitalscreenshot":
                    $reply = $this->getMessageTemplate(
                        $bot,
                        $capital,
                        $capital->sender_id,
                        "Recepción de aporte de capital completada",
                        false,
                        false,
                        [
                            [
                                ["text" => "👍 Reportar otra recepción de capital", "callback_data" => "supervisorcapitalmenu"],
                            ],
                            [
                                ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                            ],

                        ]
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

}
