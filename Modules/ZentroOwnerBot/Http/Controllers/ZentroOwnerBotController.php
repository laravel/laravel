<?php

namespace Modules\ZentroOwnerBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\TelegramBot\Traits\UsesTelegramBot;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\TelegramBot\Entities\TelegramBots;

class ZentroOwnerBotController extends JsonsController
{
    use UsesTelegramBot;

    private static $AUTODESTROY_TIME_IN_MINS = 1;

    public function __construct($botname, $instance = false)
    {
        $this->ActorsController = new ActorsController();
        $this->TelegramController = new TelegramController();

        if ($instance === false)
            $instance = $botname;
        $response = false;
        try {
            $bot = $this->getFirst(TelegramBots::class, "name", "=", "@{$instance}");
            $this->token = $bot->token;
            $this->data = $bot->data;

            $response = json_decode($this->TelegramController->getBotInfo($this->token), true);
        } catch (\Throwable $th) {
        }
        if (!$response)
            $response = array(
                "result" => array(
                    "username" => $instance
                )
            );

        $this->telegram = $response["result"];
    }

    public function processMessage()
    {
        $reply = array(
            "text" => "🚷 Ud no esta aurorizado a usar este bot.",
        );

        $array = $this->getCommand($this->message["text"]);
        //var_dump($array);
        //die;
        //echo strtolower($array["command"]);
        switch (strtolower($array["command"])) {
            case "/start":
                //https://t.me/bot?start=816767995
                // /start 816767995
                $reply = array(
                    "text" => "👋 Hola, bienvenido",
                );
                break;
            case "/p":
            case "/pass":
            case "/password":
                $key = strtolower($array["message"]);
                $demo = false;
                //$demo = isset($request["demo"]);
                $hash = $this->generateHash($this->actor->user_id, $key, 20, $demo);
                $reply = array(
                    "text" => "🔐 *" . strtoupper($key) . " hash:*\n`{$hash}`\n_Por seguridad este mensaje se elimina en " . ZentroOwnerBotController::$AUTODESTROY_TIME_IN_MINS . " min_",
                    "autodestroy" => ZentroOwnerBotController::$AUTODESTROY_TIME_IN_MINS,
                );
                break;
            case "/f":
                $reply = array(
                    "text" => $this->obtenerIniciales($array["message"]),
                );
                break;
            default:
                $reply = array(
                    "text" => "🤷🏻‍♂️ No se que responderle a “{$this->message['text']}”.",
                );
                break;

        }

        return $reply;
    }

    function obtenerIniciales($texto)
    {
        $palabras = preg_split('/\s+/', trim($texto));
        $iniciales = '';

        foreach ($palabras as $palabra) {
            if (!empty($palabra)) {
                $iniciales .= strtoupper(substr($palabra, 0, 1));
            }
        }

        return $iniciales;
    }

    private function generateHash($text, $key, $length = false, $debug = false)
    {
        $key = strtolower($key);
        if ($debug) {
            echo $text . "\n" . $key . "\n";
        }
        $hash = hash_hmac('sha256', $text, $key);
        if ($length) {
            if ($length > 64) {
                $length = 64;
            }
            $hash = substr($hash, 0, $length);
        }
        // Convertir la primera letra del hash a mayúscula (si existe)
        $hash = preg_replace_callback(
            '/[a-z]/', // Busca la primera letra minúscula
            function ($matches) {
                return strtoupper($matches[0]); // Convierte a mayúscula
            },
            $hash,
            1// Solo la primera ocurrencia
        );
        if ($debug) {
            die($hash);
        }

        return $hash;
    }

}
