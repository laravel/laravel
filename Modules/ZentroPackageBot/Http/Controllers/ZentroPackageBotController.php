<?php

namespace Modules\ZentroPackageBot\Http\Controllers;

use App\Http\Controllers\JsonsController;
use Modules\TelegramBot\Traits\UsesTelegramBot;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\TelegramBot\Entities\TelegramBots;
use Illuminate\Support\Facades\Lang;

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

        $this->strategies["/p"] =
            $this->strategies["/pass"] =
            $this->strategies["/password"] =
            function () use ($array) {
                $key = strtolower($array["message"]);
                $demo = false;
                return array(
                    "text" =>
                        "🔐 *" . strtoupper($key) . " hash:*\n",
                );
            };

        return $this->getProcessedMessage();
    }

    public function mainMenu($actor)
    {
        $menu = array();

        $url = route('telegram-scanner', array(
            "botname" => $this->telegram["username"]
        ));

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

}
