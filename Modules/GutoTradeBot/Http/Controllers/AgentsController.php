<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\TelegramBot\Entities\Actors;

class AgentsController extends JsonsController
{
    public function getSuscriptor($bot, $user_id, $any_bot = false)
    {
        $suscriptor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $user_id);
        if (!$suscriptor) {
            $suscriptors = $bot->ActorsController->getData(Actors::class, [
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
        $suscriptors = $bot->ActorsController->getAllForBot($bot->telegram["username"]);

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

        // mostrar los meadatos definidos para este suscriptor
        if (isset($suscriptor->data[$bot->telegram["username"]]) && isset($suscriptor->data[$bot->telegram["username"]]["metadatas"]))
            foreach ($suscriptor->data[$bot->telegram["username"]]["metadatas"] as $key => $value) {
                $icon = "{$key} ";
                if ($key == "wallet")
                    $icon = "💰 ";
                $text .= "\n{$icon}`" . $value . "`";
            }

        // mostrar las cuentas asociadas a este suscriptor
        $accounts = $bot->AccountsController->getAccountsOfActor($suscriptor->user_id);
        if (count($accounts) > 0) {
            $text .= "\n";
        }
        foreach ($accounts as $account) {
            $message = $bot->AccountsController->getMessageTemplate($this, $account, $suscriptor->user_id, false);
            $text .= "\n" . $message["message"]["text"];
        }

        $array = [
            "message" => [
                "text" => $text,
                "photo" => $show_photo && isset($suscriptor->data["telegram"]) && $suscriptor->data["telegram"]["photo"] ? $suscriptor->data["telegram"]["photo"] : null,
                "chat" => [
                    "id" => $actor->user_id,
                ],
                "reply_markup" => json_encode([
                    "inline_keyboard" => $array["menu"],
                ]),
            ],
        ];
        //var_dump($array["message"]["photo"]);
        if (isset($array["message"]["photo"])) {
            $bot->TelegramController->sendPhoto($array, $this->getToken($bot->telegram["username"]));
        } else {
            $bot->TelegramController->sendMessage($array, $this->getToken($bot->telegram["username"]));
        }
    }


    public function getRoleMenu($user_id, $role_id)
    {
        $array = [
            "role" => "",
            "menu" => [],
        ];

        switch ($role_id) {
            case 0:
                $array["role"] = "😳 Sin rol";
                $array["menu"] = [
                    [
                        ["text" => "💶 REMESADOR", "callback_data" => "promote2-{$user_id}"],
                        ["text" => "👍 RECEPTOR", "callback_data" => "promote3-{$user_id}"],
                    ],
                    [
                        ["text" => "👮‍♂️ GESTOR", "callback_data" => "promote1-{$user_id}"],
                        ["text" => "👮‍♂️ CAPITAL", "callback_data" => "promote4-{$user_id}"],
                    ],
                ];
                break;
            case 1:
                $array["role"] = "👮‍♂️ GESTOR";
                $array["menu"] = [
                    [
                        ["text" => "💶 REMESADOR", "callback_data" => "promote2-{$user_id}"],
                        ["text" => "👍 RECEPTOR", "callback_data" => "promote3-{$user_id}"],
                    ],
                    [
                        ["text" => "👮‍♂️ CAPITAL", "callback_data" => "promote4-{$user_id}"],
                    ],
                ];
                break;
            case 2:
                $array["role"] = "💶 REMESADOR";
                $array["menu"] = [
                    [
                        ["text" => "👍 RECEPTOR", "callback_data" => "promote3-{$user_id}"],
                    ],
                    [
                        ["text" => "👮‍♂️ GESTOR", "callback_data" => "promote1-{$user_id}"],
                        ["text" => "👮‍♂️ CAPITAL", "callback_data" => "promote4-{$user_id}"],
                    ],
                ];
                break;
            case 3:
                $array["role"] = "👍 RECEPTOR";
                $array["menu"] = [
                    [
                        ["text" => "💶 REMESADOR", "callback_data" => "promote2-{$user_id}"],
                    ],
                    [
                        ["text" => "👮‍♂️ GESTOR", "callback_data" => "promote1-{$user_id}"],
                        ["text" => "👮‍♂️ CAPITAL", "callback_data" => "promote4-{$user_id}"],
                    ],
                ];
                break;
            case 4:
                $array["role"] = "👮‍♂️ CAPITAL";
                $array["menu"] = [
                    [
                        ["text" => "💶 REMESADOR", "callback_data" => "promote2-{$user_id}"],
                        ["text" => "👍 RECEPTOR", "callback_data" => "promote3-{$user_id}"],
                    ],
                    [
                        ["text" => "👮‍♂️ GESTOR", "callback_data" => "promote1-{$user_id}"],
                    ],
                ];
                break;
            default:
                break;
        }

        return $array;
    }

    public function notifyAfterModifyRole($bot, $user_id, $role_id = 2)
    {
        $array = $bot->AgentsController->getRoleMenu($user_id, $role_id);

        // obteniendo datos del usuario de telegram
        $response = json_decode($bot->TelegramController->getUserInfo($user_id, $this->getToken($bot->telegram["username"])), true);
        $reply = [
            "text" => "🆗 *Rol de usuario modificado*\n\n" . $response["result"]["full_info"] . "\n" . $array["role"] . "\n\n👇 Qué desea hacer ahora?",
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
        $bot->TelegramController->sendMessage($array, $this->getToken($bot->telegram["username"]));
    }
}
