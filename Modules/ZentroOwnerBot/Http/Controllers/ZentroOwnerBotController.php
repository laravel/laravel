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
        $array = $this->getCommand($this->message["text"]);

        $this->strategies["/p"] =
            $this->strategies["/pass"] =
            $this->strategies["/password"] =
            function () use ($array) {
                $key = strtolower($array["message"]);
                $demo = false;
                //$demo = isset($request["demo"]);
                $hash = $this->generateHash($this->actor->user_id, $key, 20, $demo);
                return array(
                    "text" => "🔐 *" . strtoupper($key) . " hash:*\n`{$hash}`\n_Por seguridad este mensaje se elimina en " . ZentroOwnerBotController::$AUTODESTROY_TIME_IN_MINS . " min_",
                    "autodestroy" => ZentroOwnerBotController::$AUTODESTROY_TIME_IN_MINS,
                );
            };

        $this->strategies["/f"] =
            function () use ($array) {
                return array(
                    "text" => $this->obtenerIniciales($array["message"]),
                );
            };

        return $this->getProcessedMessage();
    }

    public function mainMenu($actor)
    {
        return $this->getMainMenu(
            $actor
        );
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
