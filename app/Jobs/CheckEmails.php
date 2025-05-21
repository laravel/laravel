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
                $array[] = array(
                    "date" => $carbonDate->format('Y-m-d'),
                    "id" => $spanTags[5]->textContent,
                    "name" => $spanTags[9]->textContent,
                    "amount" => $amount,
                    "to" => $spanTags[7]->textContent,
                    "rate" => $rate,
                    "usd" => $usd,
                );
            }
            // Marcar el mensaje como leído
            $message->setFlag('Seen');

        }
        dd($array);

        // Desconectarse del servidor IMAP
        $client->disconnect();
    }
}
