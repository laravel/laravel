<?php

namespace Modules\ZentroTraderBot\Http\Controllers;

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
                $text .= "\n{$icon}`" . $value . "`";
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
                        ["text" => "👤 Usuario", "callback_data" => "promote2-{$user_id}"],
                        ["text" => "👮‍♂️ Admin", "callback_data" => "promote1-{$user_id}"],
                    ],
                ];
                break;
            case 1:
                $array["role"] = "👮‍♂️ Admin";
                $array["menu"] = [
                    [
                        ["text" => "👤 Usuario", "callback_data" => "promote2-{$user_id}"],
                    ]
                ];
                break;
            default:
                break;
        }

        return $array;
    }
}
