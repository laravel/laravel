<?php

namespace Modules\ZentroOwnerBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\TelegramBot\Traits\UsesTelegramBot;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\TelegramBot\Entities\TelegramBots;
use Illuminate\Support\Facades\Lang;

use Modules\ZentroOwnerBot\Entities\sfSecurity;

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
        //var_dump($array);
        //die;

        $this->strategies["/p"] =
            $this->strategies["/pass"] =
            $this->strategies["/password"] =
            function () use ($array) {
                $key = strtolower($array["message"]);
                $demo = false;
                //$demo = isset($request["demo"]);
                $hash = $this->generateHash($this->actor->user_id, $key, 20, $demo);
                return array(
                    "text" =>
                        "🔐 *" . strtoupper($key) . " hash:*\n" .
                        "`{$hash}`\n" .
                        "_" . Lang::get("zentroownerbot::bot.prompts.password.warning", ["time" => ZentroOwnerBotController::$AUTODESTROY_TIME_IN_MINS]) . "_",
                    "autodestroy" => ZentroOwnerBotController::$AUTODESTROY_TIME_IN_MINS,
                );
            };

        $this->strategies["/f"] =
            function () use ($array) {
                return array(
                    "text" => $this->obtenerIniciales($array["message"]),
                );
            };

        $this->strategies["/l"] =
            $this->strategies["/lic"] =
            $this->strategies["/licencia"] =
            $this->strategies["/license"] =
            function () use ($array) {
                try {
                    $license = $this->generateZentroLicence(array(
                        "pc" => $array["pieces"][1],
                        "end" => $array["pieces"][2],
                        "name" => "test",
                        "build" => "FU",
                    ));

                    $time = 2 * ZentroOwnerBotController::$AUTODESTROY_TIME_IN_MINS;
                    return array(
                        "text" =>
                            "💻 *" . $array["pieces"][1] . "*\n\n" .
                            "🔐 `" . $license["licence"] . "`\n" .
                            "📅 " . $license["installed"] . " ❌ " . $license["expire"] . " _" . $license["given"] . "_\n\n" .
                            "_" . Lang::get("zentroownerbot::bot.prompts.password.warning", ["time" => $time]) . "_"
                        ,
                        "autodestroy" => $time,
                    );
                } catch (\Exception $e) {
                    return array(
                        "text" => "❌ *ERROR:* " . $array["message"] . ":\n" . $e->getMessage(),
                    );
                }
            };

        return $this->getProcessedMessage();
    }

    public function mainMenu($actor)
    {
        return $this->getMainMenu(
            $actor
        );
    }

    public function configMenu($actor)
    {
        return $this->getConfigMenu(
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

    public function generateZentroLicence($request)
    {
        $key = $request["pc"];


        // guessing installation date
        $array = explode("-", $request["pc"]);
        $installed = date_create_from_format("Y-m-d", date("Y-m-d", $array[count($array) - 1]));
        unset($array[count($array) - 1]);


        $response = array(
            "installed" => $installed->format("d/m/Y")
        );

        $key = implode("-", $array);

        $end = $request["end"];
        if (strpos($end, "/") > -1) {
            $expire = date_create_from_format("d/m/Y", $end);
            if ($expire) {
                $span = $expire->diff($installed);
                $period = "";
                if ($span->format("%y") > 0)
                    $period = $period . $span->format("%y") . "Y";
                if ($span->format("%m") > 0)
                    $period = $period . $span->format("%m") . "M";
                if ($span->format("%d") > 0)
                    $period = $period . $span->format("%d") . "D";

                $end = "";
            }
        } else {
            $period = strtoupper($request["end"]);
            $expire = $installed->add(new \DateInterval("P" . $period));
        }
        $response["expire"] = $expire->format("d/m/Y");

        // adjusting app name to Zentro&reg;
        $text = str_replace("®", "", $request["name"]);
        $text = str_replace("�", "", $text);
        $text = str_replace("&reg;", "", $text);
        $text = str_replace("Zentro", "Zentro&reg;", $text);
        $request["name"] = $text;

        $response["given"] = $period;
        $response["licence"] = sfSecurity::generateRegistrationCode(strtoupper($request["name"] . $key), $period, $request["build"]);

        return $response;
    }

}
