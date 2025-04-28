<?php

namespace Modules\GutoTradeBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\GutoTradeBot\Entities\Comments;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;

class CommentsController extends JsonsController
{
    public function create($comment, $screenshot, $sender_id, $payment_id, $data = array())
    {
        $money = Comments::create([
            'comment' => $comment,
            'screenshot' => $screenshot,
            'sender_id' => $sender_id,
            'payment_id' => $payment_id,
            'data' => $data,
        ]);
        return $money;
    }

    public function getByPaymentIdQuery($payment_id)
    {
        $query = Comments::where('payment_id', $payment_id);

        return $query;
    }
    public function getByPaymentId($payment_id)
    {
        return $this->getByPaymentIdQuery($payment_id)->get();
    }

    public function getMessageTemplate($bot, $comment, $to_id)
    {
        $actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $to_id);

        $fullname = "";
        $sender = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", $comment->sender_id);

        switch ($sender->data[$bot->telegram["username"]]["admin_level"]) {
            case 1:
            case "1":
            case 4:
            case "4":
                $fullname = "👮‍♂️ Admin";
                break;
            case 2:
            case "2":
                $response = json_decode($bot->TelegramController->getUserInfo($comment->sender_id, $bot->getToken($bot->telegram["username"])), true);
                $fullname = $response["result"]["full_name"];
                break;
            case 3:
            case "3":
                $fullname = "🤵 Supervisor";
                break;

            default:
                # code...
                break;
        }

        $created_at = $actor->getLocalDateTime($comment->created_at, $bot->telegram["username"]);
        $text = $fullname . " 💬\n📅 {$created_at}\n\n" . $comment->comment;

        return array(
            "message" => array(
                "text" => $text,
                "photo" => $comment->screenshot ? $comment->screenshot : false,
                "chat" => array(
                    "id" => $to_id,
                ),
            ),
        );
    }

    public function notifyAfterComment()
    {
        $reply = array(
            "text" => "💬 *Comentario enviado*\n_Se ha enviado su comentario satisfactoriamente._\n\n👇 Qué desea hacer ahora?",
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

}
