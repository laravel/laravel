<?php

namespace App\Jobs;

use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TokenController;
use DOMDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Facades\Client;
use Carbon\Carbon;
use Modules\GutoTradeBot\Http\Controllers\GutoTradeBotController;
use App\Http\Controllers\GraphsController;
use App\Http\Controllers\FileController;

class CheckEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // php artisan schedule:run

        // Conectar al servidor IMAP
        $client = Client::account('default');

        // Intentar conectarse
        $client->connect();

        // Abrir la bandeja de entrada
        $inbox = $client->getFolder('INBOX');
        Carbon::setLocale('en');

        // Obtener correos no leídos
        //$messages = $inbox->query()->unseen()->get();
        $messages = $inbox->query()->all()->get();

        $array = array();

        $bot = new GutoTradeBotController("GutoTradeTestBot");

        foreach ($messages as $message) {
            $html = $message->getHTMLBody();

            $dom = new DOMDocument();
            @$dom->loadHTML($html); // El uso de @ evita warnings en HTML mal formado

            // Buscar todos los elementos <b>
            $spanTags = $dom->getElementsByTagName('span');

            if (isset($spanTags[9])) {

                // Parsear la fecha
                $carbonDate = Carbon::parse($spanTags[3]->textContent);
                $amount = floatval(explode("\u{A0}", $spanTags[0]->textContent)[0]);
                $rate = floatval(str_replace("@", "", explode(" ", $spanTags[17]->textContent)[0]));
                $usd = floatval(str_replace("$", "", explode(" ", $spanTags[19]->textContent)[0]));
                $name = $bot->TextController->str_pad($spanTags[9]->textContent, 21);
                $transaction = [
                    "date" => $carbonDate->format('Y-m-d') . " " . Carbon::now()->format("H:i"),
                    "id" => $bot->TextController->str_pad("Id: " . $spanTags[5]->textContent, 50, " ", -5),
                    "name" => $name,
                    "amount" => $bot->TextController->str_pad("Monto: €" . $amount, 41, " ", -5),
                    "to" => $bot->TextController->str_pad("IBAN: " . $spanTags[7]->textContent, 50, " ", -5),
                    "rate" => $bot->TextController->str_pad("Tasa: " . $rate, 70, " ", -5),
                    "usd" => $bot->TextController->str_pad("Acreditado: $" . $usd, 66, " ", -5),

                ];
                $filename = GraphsController::generateComprobantGraph($transaction);
                $url = "https://d.micalme.com" . FileController::$AUTODESTROY_DIR . "/{$filename}.jpg";
                $text = "Prueba " . $name . "\n" . $url;
                $array = array(
                    "message" => array(
                        "text" => $text,
                        "photo" => $url,
                        "chat" => array(
                            "id" => 816767995,
                        ),
                    ),
                );
                $bot->TelegramController->sendPhoto($array, $bot->getToken($bot->telegram["username"]));

            }
            // Marcar el mensaje como leído
            $message->setFlag('Seen');

        }

        // Desconectarse del servidor IMAP
        $client->disconnect();
    }
}
