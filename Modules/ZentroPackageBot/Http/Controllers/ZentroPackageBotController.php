<?php

namespace Modules\ZentroPackageBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\TelegramBot\Traits\UsesTelegramBot;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\TelegramBot\Entities\TelegramBots;
use Illuminate\Support\Facades\Lang;
use Modules\ZentroPackageBot\Entities\Packages;
use Illuminate\Support\Facades\Log;

class ZentroPackageBotController extends JsonsController
{
    use UsesTelegramBot;


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
        $this->strategies["/password"] =
            function () use ($array) {
                $key = strtolower($array["message"]);
                $demo = false;
                return array(
                    "text" =>
                        "🔐 *" . strtoupper($key) . " hash:*\n",
                );
            };

        /*
        "web_app_data": {
            "button_text": "📷 Abrir Escáner",
            "data": "996-13838856"
        }
         */
        if (isset($this->message['web_app_data'])) {
            $array = $this->message['web_app_data'];
            $this->strategies["/webappdata"] =
                function () use ($array) {
                    $text = "❌";
                    // Buscamos en la base de datos (donde el Seeder ya insertó este AWB)
                    $package = Packages::where('awb', $array["data"]["code"])
                        ->orWhere('tracking_number', $array["data"]["code"])
                        ->first();
                    if ($package)
                        $text = "👤 " . $array["data"]["user_id"] . "\n" .
                            "✅ *Carga Internacional Detectada*\n\n" .
                            "📦 *Item:* {$package->description}\n" .
                            "✈️ *AWB:* {$package->awb}\n" .
                            "📍 *Destino:* {$package->province} (SCU)\n" .
                            "⚖️ *Peso:* {$package->weight_kg} kg";

                    return array(
                        "text" => $text,
                    );
                };
        }

        //$this->message['web_app_data']

        return $this->getProcessedMessage();
    }

    public function mainMenu($actor)
    {
        $menu = array();

        $url = route('telegram-scanner-init', array(
            "botname" => $this->telegram["username"]
        )) . '?v=' . time();

        array_push($menu, [
            [
                "text" => "📷 Abrir Escáner",
                'web_app' => ['url' => $url]
            ],
        ]);

        return $this->getMainMenu(
            $actor,
            $menu
        );
    }

    public function afterScan($user_id, $code)
    {
        Log::info("afterScan {$user_id} {$code}");
        $array = array(
            "message" => array(
                "text" => $code,
                "chat" => array(
                    "id" => $user_id,
                ),
            ),
        );

        $this->TelegramController->sendMessage($array, $this->token);
    }

}
