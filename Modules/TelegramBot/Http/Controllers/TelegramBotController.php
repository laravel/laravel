<?php

namespace Modules\TelegramBot\Http\Controllers;

use App\Http\Controllers\FileController;
use App\Http\Controllers\JsonsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\TelegramBot\Entities\Actors;
use App\Http\Controllers\Controller;

class TelegramBotController extends Controller
{
    public function handle($botname, $instance = false)
    {
        $controller = $this->getController($botname, $instance);
        if ($controller) {
            return $controller->receiveMessage(
                //    $botname,
                //   $instance
            );
        }

        abort(404, 'Bot handle controller not found');
    }

    public function initScanner($botname, $instance = false)
    {
        $controller = $this->getController($botname, $instance);
        if ($controller) {
            return $controller->initScanner(
                $botname,
                $instance
            );
        }


        abort(404, 'Bot scan controller not found');
    }

    public function getController($botname, $instance = false)
    {
        // Creando una instancia dinamica de una clase hija encargada de manipular el bot correspondiente
        $controller = "Modules\\{$botname}\\Http\\Controllers\\{$botname}Controller";
        if (class_exists($controller)) {
            /*
            if (!$instance)
                $instance = $botname;
            $host = request()->getHost(); // gutotradebot.micalme.com
            $parts = explode(".", $host);
            if (count($parts) > 2) {
                unset($parts[count($parts) - 1]);
                unset($parts[count($parts) - 1]);
                $instance = implode(".", $parts);
            }
            */
            //return app()->make($controller)->receiveMessage($botname, $instance);
            return app()->make($controller, [
                'botname' => $botname,
                'instance' => $instance
            ]);
        }

        return false;
    }
    public function storeScan()
    {
        Log::info("storeScan " . request("code"));
        //request("code")
/*
        // 1. Extraer el chat_id del usuario desde initData (para saber a quién responder)
        // Telegram envía initData como un query string, hay que parsearlo
        parse_str(request("data"), $tgData);
        $user = json_decode($tgData['user'] ?? '{}');

        $this->afterScan($user->id, request("code"));
        */

        return response()->json(['success' => true]);
    }
}
