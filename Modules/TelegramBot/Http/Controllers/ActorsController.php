<?php
namespace Modules\TelegramBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Carbon\Carbon;
use Modules\TelegramBot\Entities\Actors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActorsController extends JsonsController
{
    public function create($botname, $user_id, $parent_id = false)
    {
        $data = [
            $botname => Actors::getTemplate(0, $parent_id),
        ];

        return Actors::create([
            'user_id' => $user_id,
            'data' => $data,
        ]);

    }
    public function suscribe($bot, $botname, $user_id, $parent_id)
    {
        // Valorando suscripcion del actor q nos esta escribiendo
        $actor = $this->getFirst(Actors::class, "user_id", "=", $user_id);
        if (is_numeric($user_id)) {
            // si no esta suscrito lo agregamos a la BD
            if ($actor == null) {
                $actor = $this->create($botname, $user_id, $parent_id);
            }
            // Chequeando si se ha suscrito a otro bot pero no este y añadiendolo
            if (!isset($actor->data[$botname])) {
                $array = $actor->data;
                // Se envia $textinfo["message"] porq alli viene el parent_id en caso de ser un referido en la forma /start 816767995
                $array[$botname] = Actors::getTemplate(0, $parent_id);
                $actor->data = $array;
                $actor->save();
            }
            // Chequeando si se han obtenido los datos desde Telegram
            if (
                !isset($actor->data["telegram"]) ||
                !isset($actor->data["telegram"]["username"]) ||
                trim($actor->data["telegram"]["username"]) == ""
            ) {
                $array = $actor->data;

                $response = json_decode($bot->TelegramController->getUserInfo($actor->user_id, $bot->token), true);
                if (isset($response["result"])) {
                    $array["telegram"] = $response["result"];
                    $array["telegram"]["pinned_message"] = false;
                    $array["telegram"]["photo"] = false;

                    $photos = $bot->TelegramController->getUserPhotos($actor->user_id, $bot->token);
                    if (count($photos) > 0) {
                        $array["telegram"]["photo"] = $photos[0][count($photos[0]) - 1]["file_id"];
                    }

                    $actor->data = $array;
                    $actor->save();
                }
            }
        }

        return $actor;
    }

    public function getAll()
    {
        return parent::get(Actors::class, "id", ">", 0);
    }

    public function getAllForBot($botname)
    {
        return Actors::whereNotNull(DB::raw("JSON_EXTRACT(data, '$." . $botname . "')"))->get();
    }

    public function getSuscriptor($bot, $user_id, $any_bot = false)
    {
        $suscriptor = $this->getFirst(Actors::class, "user_id", "=", $user_id);
        if (!$suscriptor) {
            $suscriptors = $this->getData(Actors::class, [
                [
                    "contain" => true,
                    "name" => "username",
                    "value" => $user_id,
                ],
            ], "telegram");
            if (count($suscriptors) > 0) {
                $suscriptor = $suscriptors[0];
            }
        }

        if ($suscriptor) {
            if (
                $any_bot ||
                isset($suscriptor->data[$bot->telegram["username"]])
            ) {
                return $suscriptor;
            }

            return false;
        }
    }

    public function findSuscriptors($bot, $actor)
    {
        $suscriptors = $this->getAllForBot($bot->telegram["username"]);

        $count = 0;
        foreach ($suscriptors as $suscriptor) {
            if (isset($suscriptor->data[$bot->telegram["username"]])) {
                $this->notifySuscriptor($bot, $actor, $suscriptor);
                $count++;
            }
        }

        $reply = array(
            "text" => "🫂 *Usuarios suscritos*\n_Estos son los {$count} usuarios que se han suscrito al bot._",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "↖️ Volver al menú de administrador", "callback_data" => "adminmenu"]],
                ],
            ]),
        );

        return $reply;
    }

    public function findSuscriptor($bot, $user_id)
    {
        $reply = [
            "text" => "",
        ];

        $suscriptor = $this->getSuscriptor($bot, $user_id);
        if ($suscriptor) {
            if (isset($suscriptor->data[$bot->telegram["username"]])) {
                $this->notifySuscriptor($bot, $bot->actor, $suscriptor, true);
            }
        } else {
            $reply = [
                "text" => "🤷🏻‍♂️ *Usuario no encontrado*\n\nEl usuario `{$user_id}` no se encuenta suscrito a este bot.",
                "markup" => json_encode([
                    "inline_keyboard" => [
                        [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]],
                    ],
                ]),
            ];
        }
        return $reply;
    }

    public function notifySuscriptor($bot, $actor, $suscriptor, $show_photo = false)
    {
        $array = [
            "role" => "",
            "menu" => [],
        ];

        if (isset($suscriptor->data[$bot->telegram["username"]]))
            $array = $this->getRoleMenu($suscriptor->user_id, $suscriptor->data[$bot->telegram["username"]]["admin_level"]);

        array_push($array["menu"], [["text" => "🏷 Añadir metadato", "callback_data" => "/usermetadata {$suscriptor->user_id}"]]);
        array_push($array["menu"], [["text" => "❌ Eliminar", "callback_data" => "confirmation|deleteuser-{$suscriptor->user_id}|adminmenu"]]);

        $text = $suscriptor->getTelegramInfo($bot, "full_info") . "\n" . $array["role"];

        return array(
            "message" => array(
                "text" => $text,
                "photo" => $show_photo && isset($suscriptor->data["telegram"]) && $suscriptor->data["telegram"]["photo"] ? $suscriptor->data["telegram"]["photo"] : null,
                "chat" => array(
                    "id" => $actor->user_id,
                ),
                "reply_markup" => json_encode([
                    "inline_keyboard" => $array["menu"],
                ]),
            ),
        );
    }


    public function getRoleMenu($user_id, $role_id)
    {
        $array = array(
            "role" => "",
            "menu" => array(),
        );

        return $array;
    }


    public function notifyAfterModifyRole($bot, $user_id, $role_id = 2)
    {
        $array = $this->getRoleMenu($user_id, $role_id);

        // obteniendo datos del usuario de telegram
        $suscriptor = $this->getSuscriptor($bot, $user_id, true);
        $reply = [
            "text" => "🆗 *Rol de usuario modificado*\n\n" . $suscriptor->getTelegramInfo($bot, "full_info") . "\n" . $array["role"] . "\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => $array["menu"],
            ]),
        ];

        return $reply;
    }

    public function notifyRoleChange($bot, $user_id)
    {
        // notificar al usuario modificado de nu nuevo rol
        $array = [
            "message" => [
                "text" => "ℹ️ *Su rol ha sido modificado*\n\n_Le recomendamos volver al /menu para verificar sus nuevas opciones_\n\n👇 Qué desea hacer ahora?",
                "chat" => [
                    "id" => $user_id,
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
        $bot->TelegramController->sendMessage($array, $bot->token);
    }

    public function getUTCPrompt($bot)
    {
        $this->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", "/utc", $bot->telegram["username"]);

        $reply = [
            "text" => "⏰ *Ajustar zona horaria*\n\n_Definir su zona horaria hará que el bot le personalice las fechas y horas.\nPara establecer su zona horaria de la forma UTC-4 escriba solo -4._\n\n👇 Escriba en qué zona horaria esta ud:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✋ Cancelar", "callback_data" => "menu"]],
                ],
            ]),
        ];

        return $reply;
    }

    public function notifyAfterUTCChange($timezone)
    {
        $now = Carbon::now()->addHours(intval($timezone));
        $date = $now->format("Y-m-d H:i:s");
        $reply = [
            "text" => "⏰ *Zona horaria actualizada*\n_Se ha actualizado su zona horaria satisfactoriamente._\n\nAhora son las {$date}.\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "↖️ Volver al menú configuraciones", "callback_data" => "configmenu"],
                    ],

                ],
            ]),
        ];

        return $reply;
    }

    public function notifyBadUTCValue($text)
    {
        $reply = [
            "text" => "⏰ *Zona con error*\n_No se puede establecer la zona horaria “{$text}”_\nRevise q haya enviado un número válido con el que se pueda ajustar la hora.\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "⏰ Intentar nuevamente", "callback_data" => "/utc"],
                    ],
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        ];

        return $reply;
    }

    public function getApplyMetadataPrompt($bot, $method, $backoption)
    {
        $this->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", $method, $bot->telegram["username"]);

        $reply = [
            "text" => "🏷 *Definir metadato al suscriptor*\n\nEj: `wallet:0xFAcD960564531bd336ed94fBBd0911408288FCF2`\n\n👇 Escriba a continuacion:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [$backoption],
                ],
            ]),
        ];

        return $reply;
    }

    public function notifyAfterMetadataChange($user_id)
    {
        $reply = array(
            "text" => "🏷 *Metadato actualizado*\n_Se ha actualizado el metadato del suscriptor satisfactoriamente._\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "🔃 Volver a mostrar el suscriptor", "callback_data" => "/user {$user_id}"]
                    ],
                    [
                        ["text" => "↖️ Volver al menú de administrador", "callback_data" => "adminmenu"],
                    ],

                ],
            ]),
        );

        return $reply;
    }
}
