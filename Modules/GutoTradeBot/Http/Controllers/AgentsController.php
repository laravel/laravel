<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use Modules\TelegramBot\Http\Controllers\ActorsController;

class AgentsController extends ActorsController
{

    public function notifySuscriptor($bot, $actor, $suscriptor, $show_photo = false)
    {

        $array = parent::notifySuscriptor($bot, $actor, $suscriptor, $show_photo);
        $text = $array["message"]["text"];

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

        $array["message"]["text"] = $text;
        //var_dump($array["message"]["photo"]);
        if (isset($array["message"]["photo"])) {
            $bot->TelegramController->sendPhoto($array, $bot->token);
        } else {
            $bot->TelegramController->sendMessage($array, $bot->token);
        }
    }

    public function getRoleMenu($user_id, $role_id)
    {
        $array = parent::getRoleMenu($user_id, $role_id);

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
}
