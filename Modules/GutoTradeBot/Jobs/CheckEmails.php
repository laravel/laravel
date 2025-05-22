<?php

namespace Modules\GutoTradeBot\Jobs;

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
use Illuminate\Support\Facades\Log;
use Modules\GutoTradeBot\Entities\Moneys;
use App\Http\Controllers\TextController;

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

    /*
    // Número máximo de intentos
    public function tries()
    {
        return 10;
    }
    // Tiempo entre intentos (en segundos)
    public function backoff()
    {
        return [30, 60, 120]; // Espera 30s, luego 60s, luego 120s
    }
    */

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Conectar al servidor IMAP
        $client = Client::account('default');
        $client->connect();

        try {

            $tc = new TextController();
            $bot = new GutoTradeBotController("GutoTradeTestBot");

            // Abrir la bandeja de entrada
            $inbox = $client->getFolder('INBOX');
            Carbon::setLocale('en');

            // Obtener correos no leídos
            //$messages = $inbox->query()->all()->get();
            $messages = $inbox->query()->unseen()->get();
            foreach ($messages as $message) {
                $html = $message->getHTMLBody();

                $dom = new DOMDocument();
                @$dom->loadHTML($html); // El uso de @ evita warnings en HTML mal formado

                // Buscar todos los elementos <b>
                $spanTags = $dom->getElementsByTagName('span');

                if (isset($spanTags[9])) {
                    // Parsear la fecha
                    $carbonDate = Carbon::parse($spanTags[3]->textContent);
                    $float = $tc->parseNumber(explode("\u{A0}", $spanTags[0]->textContent)[0]);
                    $amount = Moneys::format($float, 2, ".", "");
                    $rate = floatval(str_replace("@", "", explode(" ", $spanTags[17]->textContent)[0]));
                    $usd = Moneys::format($tc->parseNumber(str_replace("$", "", explode(" ", $spanTags[19]->textContent)[0])), 2, ".", "");
                    $name = $spanTags[9]->textContent;
                    $transaction = [
                        "date" => $carbonDate->format('Y-m-d') . " " . Carbon::now()->format("H:i"),
                        "id" => $spanTags[5]->textContent,
                        "name" => $name,
                        "amount" => $amount,
                        "to" => $spanTags[7]->textContent,
                        "rate" => $rate,
                        "usd" => $usd,
                    ];
                    $filename = GraphsController::generateComprobantGraph($transaction, true);
                    $url = "https://d.micalme.com" . FileController::$AUTODESTROY_DIR . "/{$filename}.jpg";
                    $text = $name . " " . $float;
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
        } catch (\Throwable $th) {
            Log::error("CheckEmails job ERROR CODE {$th->getCode()} line {$th->getLine()}: {$th->getMessage()}");
        }

        // Desconectarse del servidor IMAP
        $client->disconnect();
    }

    /*
    public function failed($exception)
    {
        // Lógica para manejar el fallo permanente
        Log::error('CheckEmails job failed permanently: ' . $exception->getMessage());

        // Opcionalmente notificar al administrador
        // Mail::to('admin@example.com')->send(new JobFailedNotification($this, $exception));
    }
        */
}
