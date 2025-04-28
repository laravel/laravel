<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\GutoTradeBot\Entities\Accounts;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;

class AccountsController extends JsonsController
{

    public function searchAccountsByField($field, $symbol, $value)
    {
        return Accounts::where($field, $symbol, $value)->get();
    }

    public function getAccountsOfActor($user_id)
    {
        return Accounts::where('data', 'LIKE', "%{$user_id}%")->get();
    }

    public function getAccountsGroupedByBank($field, $symbol, $value)
    {
        $accounts = Accounts::select('bank', 'id', 'name', 'number', 'detail', 'is_active', 'data')
            ->where($field, $symbol, $value)
            ->whereNotNull('bank')
            ->get()
            ->groupBy('bank');

        $array = [];

        foreach ($accounts as $bank => $bankAccounts) {
            $array[$bank] = $bankAccounts->toArray();
        }

        return $array;
    }
    public function getOperationsPrompt()
    {
        $reply = array(
            "text" => "🎲 *Ajustar operaciones restantes*\n\n👇 Escriba cuántas operaciones restan en esta cuenta:",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✋ Cancelar", "callback_data" => "menu"]],
                ],
            ]),
        );

        return $reply;
    }

    public function getMessageTemplate($account, $to_id, $show_whattodo = true)
    {
        $menu = array();

        $text = "🏦 *{$account['bank']}*:\n";
        $text .= "-------------------------------------------------------------------\n";
        $text .= "🪪 `{$account['name']}`\n";
        if (isset($account["data"]) && isset($account["data"]["number"])) {
            foreach ($account["data"]["number"] as $number => $value) {
                $text .= "👉 `{$number}`\n";
            }
        } else {
            $text .= "👉 `{$account['number']}`\n";
        }
        if ($account['detail'] != null) {
            $text .= "📌 `{$account['detail']}`\n";
        }
        if ($account["is_active"]) {
            if (isset($account["data"])) {
                $data = $account["data"];
                if (isset($data["remain_operations"])) {
                    array_push($menu, [["text" => "🎲 {$data['remain_operations']} Operaciones", "callback_data" => "promptaccountoperations-{$account['id']}"]]);
                }
            }
            array_push($menu, [
                ["text" => "🔴 Desactivar", "callback_data" => "accountactivation-{$account['id']}-false"],
            ]);
        } else {
            array_push($menu, [
                ["text" => "🟢 Activar", "callback_data" => "accountactivation-{$account['id']}-true"],
            ]);
        }

        if ($show_whattodo && count($menu) > 0) {
            $text .= "👇 Qué desea hacer?";
        }

        return array(
            "message" => array(
                "text" => $text,
                "chat" => array(
                    "id" => $to_id,
                ),
                "reply_markup" => json_encode([
                    "inline_keyboard" => $menu,
                ]),
            ),
        );
    }

    public function getActiveAccounts($bot)
    {
        $reply = [];

        $text = "";

        $active_accounts = $this->getAccountsGroupedByBank("is_active", "=", true);
        switch ($bot->actor->data[$bot->telegram["username"]]["admin_level"]) {
            case '1':
            case 1:
            case '4':
            case 4:
                // buscando cuentas inactivas para agregarselas a los admins y las puedan ver
                $inactive_accounts = $this->getAccountsGroupedByBank("is_active", "=", false);
                foreach ($inactive_accounts as $bank => $accounts) {
                    if (!isset($active_accounts[$bank])) {
                        $active_accounts[$bank] = [];
                    }
                    foreach ($accounts as $account) {
                        $active_accounts[$bank][] = $account;
                    }
                }

                // Para los admins mando cada cuenta por separado con opciones de gestion
                $amount = 0;
                foreach ($active_accounts as $bank => $accounts) {
                    foreach ($accounts as $account) {
                        $array = $this->getMessageTemplate($account, $bot->actor->user_id);
                        $bot->TelegramController->sendMessage($array, $this->getToken($bot->telegram["username"]));
                        $amount++;
                    }
                }
                $text = "👆 *Cuentas configuradas*\n_Estas son {$amount} cuentas configuradas para recibir pagos._\n\n";
                break;
            default:
                // para cualquier otro mando un solo mensaje con el texto de todas las cuentas
                foreach ($active_accounts as $bank => $accounts) {
                    $account_content = "";
                    foreach ($accounts as $account) {
                        $auth = false;
                        $account_number = "👉 `{$account['number']}`\n";
                        if (isset($account["data"]) && isset($account["data"]["number"])) {
                            foreach ($account["data"]["number"] as $number => $value) {
                                if (array_search($bot->actor->user_id, $value["owners"]) > -1) {
                                    $account_number = "👉 `{$number}`\n";
                                    $auth = true;
                                    break;
                                }
                            }
                        } else {
                            $account_number = "👉 `{$account['number']}`\n";
                            $auth = true;
                        }
                        if ($auth) {
                            $account_content .= "-------------------------------------------------------------------\n";
                            $account_content .= "🪪 `{$account['name']}`\n";

                            $account_content .= $account_number;

                            if (isset($account["data"]) && isset($account["data"]["remain_operations"])) {
                                $account_content .= "🎲 {$account['data']['remain_operations']} operaciones restantes\n🧏 _Es un estimado y no se actualiza en tiempo real_\n";
                            }
                            if ($account['detail'] != null) {
                                $account_content .= "📌 `{$account['detail']}`\n";
                            }
                            if (isset($account["data"]) && isset($account["data"]["notes"])) {
                                foreach ($account["data"]["notes"] as $note) {
                                    $account_content .= "ℹ️ {$note}\n";
                                }
                            }
                        }
                    }
                    if ($account_content != "") {
                        $text .= "🏦 *Si tu cliente es {$bank}*:\n";
                        $text .= $account_content;
                        $text .= "===================================\n\n";
                    }
                }
                break;
        }
        $menu = [];
        array_push($menu, [["text" => "↖️ Volver al menú principal", "callback_data" => "menu"]]);

        if (count($active_accounts) == 0) {
            $text .= "❌ *No existen cuentas activas en este momento*\n";
        }

        $text .= "👇 Qué desea hacer ahora?";

        $reply = [
            "text" => $text,
            "markup" => json_encode([
                "inline_keyboard" => $menu,
            ]),
        ];

        return $reply;
    }

}
