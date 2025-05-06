<?php

namespace Modules\GutoTradeBot\Entities;

use Modules\GutoTradeBot\Entities\Jsons;
use Modules\TelegramBot\Http\Controllers\TelegramBotController;
use Modules\TelegramBot\Http\Controllers\TelegramController;

class Moneys extends Jsons
{
    protected $fillable = ['amount', 'comment', 'screenshot', 'sender_id', 'supervisor_id', 'data'];

    public $timestamps = true;

    public function isLiquidated()
    {
        return isset($this->data["liquidation_date"]);
    }
    public function isConfirmed()
    {
        return isset($this->data["confirmation_date"]);
    }
    public function hasComments()
    {
        $items = Comments::selectRaw('COUNT(*) as count')
            ->where('payment_id', '=', $this->id)
            ->get();
        return $items[0]["count"] > 0;
    }
    public function sendAsTelegramMessage($bot, $actor, $title, $message = false, $show_owner_id = true, $menu = false, $demo = false)
    {
        $text = "💰 *{$title}*\n🆔 `{$this->id}`  ";
        if ($this->isConfirmed()) {
            $text .= "🟩";
            if ($this->isLiquidated()) {
                $text .= "🟩";
            } else {
                $text .= "🟨";
            }
        } else {
            $text .= "🟨⬜️";
        }
        if (
            isset($this->data["rate"]) &&
            isset($this->data["rate"]["internal"])
        ) {
            $text .= " {$this->data["rate"]["internal"]}➗";
        }

        $text .= "\n\n";

        if ($message) {
            $text .= "{$message}\n\n";
        }

        $clase = get_class($this);
        if (stripos($clase, "payment") > -1) {
            $text .= "*🪪 A nombre de:\n👤 {$this->comment}: {$this->amount} 💶*\n";
        }
        if (stripos($clase, "capital") > -1) {
            $text .= "*🖍 Movimiento:\n🛬 Se reciben: {$this->comment} 💰\n🛫 Se enviarán: {$this->amount} 💶*\n";
        }

        if ($actor && $actor->id > 0) {
            // Personalizando fecha y hora en dependencia de la zona horaria del actor
            $created_at = $actor->getLocalDateTime($this->created_at, $bot->telegram["username"]);
            $updated_at = $actor->getLocalDateTime($this->updated_at, $bot->telegram["username"]);
            $text .= "📅 *Fecha*: {$created_at}\n\n";

            if ($show_owner_id) {
                if ($this->sender_id && $this->sender_id > 0) {
                    $suscriptor = $bot->AgentsController->getSuscriptor($bot, $this->sender_id, true);
                    $text .= "👨🏻‍💻 Reportado por:\n" . $suscriptor->getTelegramInfo($bot, "full_info") . "\n\n";
                }

                if (
                    $this->supervisor_id && $this->supervisor_id > 0 &&
                    (
                        $actor->isLevel(1, $bot->telegram["username"]) ||
                        $actor->isLevel(3, $bot->telegram["username"]) ||
                        $actor->isLevel(4, $bot->telegram["username"])
                    )
                ) {
                    $suscriptor = $bot->AgentsController->getSuscriptor($bot, $this->supervisor_id, true);
                    if ($suscriptor && $suscriptor->id > 0)
                        $text .= "🕵️‍♂️ Asignado a:\n" . $suscriptor->getTelegramInfo($bot, "full_info") . "\n\n";
                }
            }
            $text .= "🗓 *Actualizado*: {$updated_at}\n\n";

            if ($menu && count($menu) > 0) {
                $text .= "👇 Qué desea hacer?";
            }

            if (isset($this->data["previous_screenshot"])) {
                $extra_screenshots = array();
                if (is_array($this->data["previous_screenshot"])) {
                    // si las capturas previas estan en array las tomo
                    $extra_screenshots = $this->data["previous_screenshot"];
                } else if ($this->data["previous_screenshot"] != "") {
                    // si es solo una y esta en formato texto
                    $extra_screenshots[] = $this->data["previous_screenshot"];
                }
                array_unshift($extra_screenshots, $this->screenshot);

                $array = array();
                foreach ($extra_screenshots as $screenshot) {
                    if ($screenshot != "") {
                        // si llegamos a 10 se para por restriccion cantidad de imagenes de la funcion sendMediaGroup
                        if (count($array) > 10) {
                            break;
                        }

                        $array[] = array(
                            "type" => "photo",
                            "media" => $screenshot,
                            "caption" => "🆔 {$this->id} 👤 *{$this->comment}*: {$this->amount} 💶\n📅 Fecha: {$created_at}",
                            "parse_mode" => "Markdown",
                        );
                    }
                }

                $array = array(
                    "demo" => $demo ? true : null,
                    "message" => array(
                        "media" => json_encode($array),
                        "chat" => array(
                            "id" => $actor->user_id,
                        ),
                    ),
                );
                $response = json_decode($bot->TelegramController->sendMediaGroup($array, $bot->getToken($bot->telegram["username"])), true);
                $array = array(
                    "demo" => $demo ? true : null,
                    "message" => array(
                        "text" => $text,
                        "chat" => array(
                            "id" => $actor->user_id,
                        ),
                        "reply_to_message_id" => isset($response["result"][0]) ? $response["result"][0]["message_id"] : null,
                        "reply_markup" => json_encode([
                            "inline_keyboard" => $menu ? $menu : array(),
                        ]),
                    ),
                );

                $bot->TelegramController->sendMessage($array, $bot->getToken($bot->telegram["username"]));

            } else {
                $array = array(
                    "demo" => $demo ? true : null,
                    "message" => array(
                        "text" => $text,
                        "photo" => $this->screenshot,
                        "chat" => array(
                            "id" => $actor->user_id,
                        ),
                        "reply_markup" => json_encode([
                            "inline_keyboard" => $menu ? $menu : array(),
                        ]),
                    ),
                );
                $bot->TelegramController->sendPhoto($array, $bot->getToken($bot->telegram["username"]));
            }
        }
    }

    public static function format($amount, $decimals_places = 2, $decimals_separator = ".", $thousands_separator = " ")
    {
        return number_format($amount, $decimals_places, $decimals_separator, $thousands_separator);
    }

}
