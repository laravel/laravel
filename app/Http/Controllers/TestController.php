<?php
namespace App\Http\Controllers;

use App\Http\Controllers\GraphsController;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Modules\GutoTradeBot\Entities\Capitals;
use Modules\GutoTradeBot\Entities\Moneys;
use Modules\GutoTradeBot\Entities\Profits;
use Modules\GutoTradeBot\Entities\Payments;
use Modules\GutoTradeBot\Entities\Rates;
use Modules\GutoTradeBot\Http\Controllers\CapitalsController;
use Modules\GutoTradeBot\Http\Controllers\GutoTradeBotController;
use Modules\GutoTradeBot\Http\Controllers\PaymentsController;
use Modules\GutoTradeBot\Http\Controllers\ProfitsController;
use Modules\TelegramBot\Http\Controllers\ActorsController;
use Modules\TelegramBot\Http\Controllers\TelegramController;
use Modules\GutoTradeBot\Http\Controllers\CoingeckoController;
use Webklex\IMAP\Facades\Client;
use Modules\TelegramBot\Entities\Actors;
use Modules\TelegramBot\Entities\TelegramBots;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Http\Controllers\FileController;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Modules\GutoTradeBot\Jobs\CheckEmails;
use Illuminate\Support\Facades\Storage;
use Modules\ZentroTraderBot\Http\Controllers\WalletController;
use Modules\ZentroTraderBot\Entities\TradingSuscriptions;
use Modules\ZentroTraderBot\Http\Controllers\ZentroTraderBotController;

class TestController extends Controller
{
    public function test(Request $request)
    {

        $bot = new ZentroTraderBotController("ZentroTraderBot");
        $admins = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => [1],
            ],
        ], $bot->telegram["username"]);
        dd($admins->toArray());
        die("\n");


        $suscriptor = TradingSuscriptions::where('user_id', "816767995")->first();
        dd($suscriptor->data);
        die;

        $wc = new WalletController();
        dd($wc->getBalance("816767995"));
        die;

        $actor = Actors::where('user_id', "816767995")->first();
        dd($actor->data);
        die;

        $bot = new GutoTradeBotController("IrelandPaymentsBot");
        $bot->actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", "816767995");
        // ----------------------------------------------------------------


        $client = new \GuzzleHttp\Client();

        $imagePath = public_path() . FileController::$AUTODESTROY_DIR . "/1756565405.jpg";

        $response = $client->request('POST', 'https://api.ocr.space/parse/image', [
            'headers' => ['apiKey' => 'K85608386588957'], // API key gratuita
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($imagePath, 'r')
                ],
                [
                    'name' => 'OCREngine',
                    'contents' => '2'
                ]
            ]
        ]);

        dd(json_decode($response->getBody(), true)['ParsedResults'][0]['ParsedText']);




        $response = $bot->TelegramController->exportFileLocally("AgACAgEAAxkBAAIEtWgU3p93-ImyhVgfK2DpzEE3tJKkAALarjEbnW6oRFe8vIv8tEB3AQADAgADeQADNgQ", $bot->token);
        dd($response);


        die(trans("Web3::messages.menu.title"));



        //4143
        $id = 1134;
        $fc = new FileController();
        $payments = $fc->searchInLog('payment', $id, 'storage', true);
        foreach ($payments as $array)
            if ($array["id"] == $id) {
                $payment = new Payments($array);
                $payment->id = $array["id"];
                $payment->created_at = $array["created_at"];
                $payment->updated_at = $array["updated_at"];
                $payment->save();
                break;
            }
        dd($payments);
        die(date("Y-m-d H:i:s") . ": DONE!");



        // Ejecutar el Job manualmente
        $job = new CheckEmails();
        $job->handle(); // Llama directamente al método handle()
        die(date("Y-m-d H:i:s") . ": DONE!");



        $array = $bot->PaymentsController->getCapitalizationReport($bot);
        echo "<a href='" . request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"] . "'>Pagos: " . $array["filename"] . "</a><br/><br/>";
        die;



        dd(isset($bot->data["promotions"]));
        $array = $bot->data;
        $array["promotions"] = array(
            array(
                "from" => 1,
                "to" => 1000,
                "price" => 1.01,
            ),
            array(
                "from" => 1000,
                "to" => 3000,
                "price" => 1.02,
            ),
            array(
                "from" => 3000,
                "to" => 1000000,
                "price" => 1.03,
            ),
        );
        //dd($bot->data);
        dd(json_encode($array));

        dd($bot->getSystemInfo());

        $codigo = 'function resta($x, $y) { return $x - $y; }';
        echo json_encode($codigo);
        eval ($codigo);
        dd(resta(10, 4));

        Carbon::setLocale('en');
        $tc = new TextController();

        try {

            // Conectar al servidor IMAP
            $client = Client::account('default');
            $botname = explode("@", $client->username)[0];
            $bot = new GutoTradeBotController($botname);

            $client->connect();
            // Abrir la bandeja de entrada
            $inbox = $client->getFolder('INBOX');

            // Obtener correos no leídos
            //$messages = $inbox->query()->all()->get();
            $messages = $inbox->query()->unseen()->get();
            foreach ($messages as $message) {
                /*
                // Metadatos básicos
                $messageId = $message->getUid(); // UID del mensaje
                $subject = $message->getSubject(); // Asunto

                $from = $message->getFrom(); // Remitente(s)
                foreach ($from as $sender) {
                    $name = $sender->name;    // Nombre del remitente
                    $mail = $sender->mail;    // Dirección de correo
                    $full = $sender->full;    // Cadena completa "Nombre <email>"
                }

                $to = $message->getTo(); // Destinatario(s)
                $cc = $message->getCc(); // Copias
                $bcc = $message->getBcc(); // Copias ocultas

                $date = $message->getDate(); // Fecha
                // $date es un objeto Carbon que puedes formatear:
                $formattedDate = $date->format('Y-m-d H:i:s');

                $flags = $message->getFlags(); // Banderas (visto, respondido, etc.)

                // Obteniendo información sobre adjuntos (sin descargarlos)
                $attachments = $message->getAttachments();
                foreach ($attachments as $attachment) {
                    $filename = $attachment->getName();
                    $size = $attachment->getSize();
                    $contentType = $attachment->getContentType();
                }
                */

                $html = $message->getHTMLBody();

                $dom = new DOMDocument();
                @$dom->loadHTML($html); // El uso de @ evita warnings en HTML mal formado

                // Buscar todos los elementos <b>
                $spanTags = $dom->getElementsByTagName('span');

                if (isset($spanTags[9])) {
                    // Parsear la fecha
                    $carbonDate = Carbon::parse($spanTags[3]->textContent);
                    /*
                    "110,00\u{A0}€ EUR"
                    "$451.62 USD" 

                    "110,00 EUR"
                    "451.62 USD" 
                     */
                    $float = $spanTags[0]->textContent;
                    $float = str_replace("\u{A0}€", "", $float);
                    $float = str_replace("$", "", $float);
                    $pieces = explode(" ", $float);
                    $float = $tc->parseNumber($pieces[0]);
                    if (!is_numeric($float))
                        $float = 0;
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
                    $url = "https://{$botname}.micalme.com" . FileController::$AUTODESTROY_DIR . "/{$filename}";
                    $text = $name . " " . $float;
                    $array = array(
                        "message" => array(
                            "text" => $text,
                            "photo" => $url,
                            "chat" => array(
                                "id" => env("TELEGRAM_GROUP_GUTO_TRADE_BOT"),
                            ),
                        ),
                    );
                    $response = $bot->TelegramController->sendPhoto($array, $bot->token);
                    Log::info("CheckEmails sendtogroup message = " . json_encode($array["message"]) . " response = " . json_encode($response) . "\n");


                } else {
                    // Marcar el mensaje como leído porq no cumple con el formato de mensaje Meru

                }
                $message->setFlag('Seen');
            }

            // Desconectarse del servidor IMAP
            $client->disconnect();
        } catch (\Throwable $th) {
            Log::error("CheckEmails job ERROR CODE {$th->getCode()} line {$th->getLine()}: {$th->getMessage()}");
        }














        $similarity = $bot->TextController->calculateSimilarityPercentage(
            "ALBERTO BENZAZON RIVERA",
            "ALBERTO"
        );
        dd($similarity);

        dd($bot->data["notifications"]);
        //$bot->updateData(Actors::class, "user_id", $bot->actor->user_id, "last_bot_callback_data", "/utc", $bot->telegram["username"]);
        $array = array(
            "notifications" => array(
                "payments" => array(
                    "new" => array(
                        "fromremesador" => array(
                            "togestors" => 1,
                            "tocapitals" => 1,
                        ),
                        "fromcapital" => array(
                            "togestors" => 1,
                        ),
                        "frombot" => array(
                            "togestors" => 1,
                            "tocapitals" => 1,
                        ),
                    ),
                    "double" => array(
                        "togestors" => 1,
                        "tocapitals" => 1,
                    ),
                ),
                "capitals" => array(
                    "new" => array(
                        "togestors" => 1,
                    ),
                    "noenough" => array(
                        "tocapitals" => 1,
                    ),
                ),
                "comments" => array(
                    "new" => array(
                        "tosupervisors" => 1,
                        "togestors" => 1,
                    ),
                ),
            )
        );
        $obj = $bot->getFirst(TelegramBots::class, "name", "=", "@GutoTradeTestBot");
        $obj->data = $array;
        $obj->save();
        dd($obj);

        $now = Carbon::now();
        $future = Carbon::now()->addYears(2)->addMonths(3)->addMinutes(5)->addSeconds(6);

        dd(MathController::getTimeDifference($now->getTimestamp(), $future->getTimestamp()));

        dd(env("TELEGRAM_GROUP_GUTO_TRADE_BOT"));

        dd($bot->telegram["id"]);


        $ac = new ActorsController();
        $name = "gutotradebOt";
        $bot = $ac->getFirst(TelegramBots::class, "name", "=", "@{$name}");
        dd($bot);

        $tc = new TelegramController();
        $response = json_decode($tc->getBotInfo("7543090584:AAEisZYB1NL24Wwwv2xQ2rVChOugyXYLdBU"), true);
        $response = json_decode($tc->getBotInfo("7543090584:AAEisZYB1NL24Wwwv2xQ2rVChOugyXYLdBU"), true);
        dd($response);


        $bot = new GutoTradeBotController("GutoTradeBot");
        $admins = $bot->ActorsController->getData(Actors::class, [
            [
                "contain" => true,
                "name" => "admin_level",
                "value" => [1, 4],
            ],
        ], "gutotradebot");
        dd($admins->toArray());



        $array = [
            "message" => [
                "text" => "Prueba: " . date("H:i:s"),
                "chat" => [
                    "id" => 816767995,
                ],
                "autodestroy" => 1
            ],
        ];
        $tc = new TelegramController();
        $response = json_decode($tc->sendMessage($array, "7543090584:AAEisZYB1NL24Wwwv2xQ2rVChOugyXYLdBU"), 1);
        dd($response);


        // Ejecutar el Job manualmente
        $job = new CheckEmails();
        $job->handle(); // Llama directamente al método handle()
        die(date("Y-m-d H:i:s") . ": DONE!");



        echo env('APP_INSTANCE');
        dd(config());

        // Datos de ejemplo (puedes reemplazarlos con tus datos reales)
        $transactions = [
            "date" => Carbon::now()->format("Y-m-d H:i"),
            "id" => "9cd4bbbf-021f-4a4e-8902-f6a96c8059ca",
            "name" => "ARIANNE GARRIDO RONDON",
            "amount" => Moneys::format("20", 2, ".", ""),
            "to" => "IE11MODR99035506793800",
            "rate" => "0.8912656",
            "usd" => "161.71",
        ];

        $filename = GraphsController::generateComprobantGraph($transactions, true);
        die("<img src='" . request()->root() . FileController::$AUTODESTROY_DIR . "/" . $filename . ".jpg'/>");






        $caption = $bot->PaymentsController->processCaption("Prueba Con Nombre Larguisimo de muchos apellidos");
        dd($caption);





        //dd($bot->TextController->numberAsEmoji(123));
        $bot->actor = $bot->ActorsController->getFirst(Actors::class, "user_id", "=", "816767995");

        $reply = $bot->getSystemInfo();
        dd($reply);

        $cashflow2 = $bot->PaymentsController->getCashFlow($bot);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $bot->PaymentsController->getCashFlowSheet($cashflow2, $sheet);

        $writer = new Xlsx($spreadsheet);
        $filename = time() . "2.xlsx";

        $path = public_path() . FileController::$AUTODESTROY_DIR;
        // Si la carpeta no existe, crearla
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        // Guardar el archivo en el sistema
        $writer->save($path . "/" . $filename);

        $array = explode(".", $filename);
        $reportnew = request()->root() . "/report/" . $array[1] . "/" . $array[0];
        dd($reportnew);


        $items = Payments::where('id', '>', 0)->get();
        $array = $bot->PaymentsController->export($bot, $items, $bot->actor);
        echo "<a href='" . request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"] . "'>Pagos: " . $array["filename"] . "</a><br/><br/>";
        die;

        $results = $bot->getSystemInfo();
        dd($results);
        die;



        $items = Payments::where('id', '>', 0)->get();
        $array = $bot->PaymentsController->export($bot, $items, $actor);
        echo "<a href='" . request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"] . "'>Pagos: " . $array["filename"] . "</a><br/><br/>";
        die;

        $items = Capitals::where('id', '>', 0)->get();
        $array = $bot->CapitalsController->export($bot, $items, $actor);
        echo "<a href='" . request()->root() . "/report/" . $array["extension"] . "/" . $array["filename"] . "'>Capitales: " . $array["filename"] . "</a><br/><br/>";
        die;



        dd($bot->ProfitsController->getSpended(100, 5, 0));

        die;

        $reply = $bot->PaymentsController->getAllList($bot, "all", "816767995");
        dd($reply);
        die;

        $count = 0;
        $payments = Capitals::where('id', '>', 0)->get();
        foreach ($payments as $key => $payment) {
            $date = Carbon::createFromFormat("Y-m-d H:i:s", $payment->created_at)->format("Y-m-d");
            $rate = Rates::where('date', '=', $date)->first();

            if ($rate && $rate->id > 0) {
                $array = $payment->data;
                $array["rate"]["oracle"] = array(
                    "direct" => $rate->rate,
                    "inverse" => 1 / $rate->rate,
                );
                $payment->data = $array;
                $payment->save();
                $count++;
            }
        }
        echo $count;
        dd($payments->toArray());
        die;


        $uniqueDates = Payments::select(DB::raw('DATE(created_at) as date'))
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('rates')
                    ->whereRaw('DATE(payments.created_at) = rates.date');
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->limit(10)
            ->orderBy('date', 'desc')
            ->get()
            ->pluck('date');
        $dates = $uniqueDates->toArray();
        //dd($dates);
        //die;

        $last = "";
        $amount = 0;
        foreach ($dates as $date) {
            $array = CoingeckoController::getHistory("eur", "tether", $date);
            $value = $array["direct"];
            if ($value == 0)
                break;
            else {
                Rates::create([
                    'date' => $date,
                    'base' => "tether",
                    'coin' => "eur",
                    'rate' => $value,
                ]);
                $last = $date;
                $amount++;
            }
        }
        echo "{$amount} / last = {$last}";
        dd($dates);
        die;








        //die;
        $value = 1;
        //while ($value > 0) {
        foreach ($dates as $date) {
            $array = CoingeckoController::getHistory("eur", "tether", $date);
            $value = $array["direct"];
            if ($value == 0)
                break;
            else {
                Rates::create([
                    'date' => $date,
                    'base' => "tether",
                    'coin' => "eur",
                    'rate' => $value,
                ]);
                sleep(2);
            }
        }
        //}
        dd($uniqueDates->toArray());
        die;


        $date = "29-04-2025";
        $array = CoingeckoController::getHistory("eur", "tether", $date);
        dd($array);
        die;



        $paymentsWithMissingRates = Payments::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('rates')
                ->whereRaw('DATE(payments.created_at) = rates.created_at');
        })->get();
        dd($paymentsWithMissingRates->toArray());
        die;


        $dates = array();

        $payments = Payments::whereRaw("JSON_EXTRACT(data, '$.rate.oracle.direct') = ?", [0])
            ->orderBy('id', "desc")
            ->limit(10)
            ->get();

        $paymentcount = 0;
        foreach ($payments as $payment) {
            $array = $payment->data;

            $date = Carbon::createFromFormat("Y-m-d H:i:s", $payment->created_at);

            if (!isset($dates[$date->format("d-m-Y")]))
                $dates[$date->format("d-m-Y")] = CoingeckoController::getHistory("eur", "tether", $date->format("Y-m-d"));

            if ($dates[$date->format("d-m-Y")]["direct"] > 0) {
                $array["rate"]["oracle"] = $dates[$date->format("d-m-Y")];
                $payment->data = $array;
                $payment->save();
                $paymentcount++;
            }
        }


        $payments = Capitals::whereRaw("JSON_EXTRACT(data, '$.rate.oracle.direct') = ?", [0])
            ->orderBy('id', "desc")
            ->limit(10)
            ->get();
        $capitalcount = 0;
        foreach ($payments as $payment) {
            $array = $payment->data;

            $date = Carbon::createFromFormat("Y-m-d H:i:s", $payment->created_at);

            if (!isset($dates[$date->format("d-m-Y")]))
                $dates[$date->format("d-m-Y")] = CoingeckoController::getHistory("eur", "tether", $date->format("Y-m-d"));

            if ($dates[$date->format("d-m-Y")]["direct"] > 0) {
                $array["rate"]["oracle"] = $dates[$date->format("d-m-Y")];
                $payment->data = $array;
                $payment->save();
                $capitalcount++;
            }
        }


        echo "Payments: {$paymentcount}, Capitals: {$capitalcount}<hr/>";
        dd($dates);
        die;






        $actor = $bot->ActorsController->getFirst(Actors::class, 'user_id', '=', "816767995");
        $reply = $bot->notifyStats($actor);
        dd($reply);


        $bot = new GutoTradeBotController("GutoTradeBot");
        $ac = new ActorsController();
        $actor = $ac->getFirst(Actors::class, 'user_id', '=', config('metadata.system.app.telegram.bot.owner'));
        $reply = $bot->notifyStats($actor);
        dd($reply);
        die;


        $ac = new ActorsController();
        $suscriptors = $ac->getAllForBot("IrelandPaymentsBot");
        dd($suscriptors->toArray());
        die;

        $amount = 100;
        //$flow = $bot->ProfitsController->calculateFlow($amount, 1, 1, 6);
        //$flow = $bot->ProfitsController->calculateFlow($amount, 1, 0, 0); // al 1x1
        $flow = $bot->ProfitsController->calculateFlow($amount, 1, 0, -1); // al 1.01 a favor del euro
        dd($flow);

        die(date("Y-m-d H:i:s") . ": DONE!");

        $rate = $bot->ProfitsController->getUSDTtoSendWithActiveRate($amount);
        dd($rate);
        $rate = $bot->ProfitsController->getEURtoSendWithActiveRate($amount);
        dd($rate);
        $liquidate_amount = Moneys::format(MathController::round($rate, 2, true));

        die(date("Y-m-d H:i:s") . ": DONE!");


        $host = explode(".", request()->getHost());
        dd($host);
        die(date("Y-m-d H:i:s") . ": DONE!");

        $fc = new FileController();
        $response = $fc->searchInLog('payment', "Santiago", 'storage', false);
        dd($response);
        die(date("Y-m-d H:i:s") . ": DONE!");

        $ac = new ActorsController();

        $botname = "GutoTradeBot";

        $suscriptors = $ac->getAll();
        foreach ($suscriptors as $actor) {
            //dd($actor->data[$botname]);
            if (isset($actor->data[$botname]) && isset($actor->data[$botname]["wallet"])) {
                $suscriptordata = $actor->data;
                $suscriptordata[$botname]["metadatas"] = array(
                    "wallet" => $actor->data[$botname]["wallet"]
                );
                unset($suscriptordata[$botname]["wallet"]);
                $actor->data = $suscriptordata;
                $actor->save();
            }
        }
        die(date("Y-m-d H:i:s") . ": DONE!");

        // ----------------------------------------------------------------------------

        $pc = new PaymentsController();
        $capitals = $pc->getCapitalsByDateRange(Capitals::class, null, Carbon::parse("2025-03-22 19:00:41"));
        //dd($capitals->toArray());
        foreach ($capitals as $capital) {
            $array = $capital->data;
            $array["profit"] = [
                "salary" => "1",
                "profit" => "6",
            ];
            $capital->data = $array;
            $capital->save();
        }
        $capitals = $pc->getCapitalsByDateRange(Capitals::class, Carbon::parse("2025-03-22 19:00:41"), Carbon::parse("2025-04-07 17:35:00"));
        //dd($capitals->toArray());
        foreach ($capitals as $capital) {
            $array = $capital->data;
            $array["profit"] = [
                "salary" => "2",
                "profit" => "5",
            ];
            $capital->data = $array;
            $capital->save();
        }
        $capitals = $pc->getCapitalsByDateRange(Capitals::class, Carbon::parse("2025-04-07 17:35:00"));
        foreach ($capitals as $capital) {
            $array = $capital->data;
            $array["profit"] = [
                "salary" => Profits::getSalary(),
                "profit" => Profits::getProfit(),
            ];
            $capital->data = $array;
            $capital->save();
        }
        //dd($capitals->toArray());
        die(date("Y-m-d H:i:s") . ": DONE!");

        // ----------------------------------------------------------------------------

        $pc = new ProfitsController();
        dd($pc->calculateFlow(100, 1.1099));
        die;

        // ----------------------------------------------------------------------------

        $groupedCapitals = Capitals::query()
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as amount'),
                DB::raw('COUNT(id) as count'),
                DB::raw('JSON_ARRAYAGG(JSON_OBJECT("id", id, "amount", amount, "comment", comment, "screenshot", screenshot, "sender_id", sender_id, "supervisor_id", supervisor_id, "data", data)) as items'),
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc(DB::raw('DATE(created_at)'))
            ->limit(10)
            ->get();
        dd($groupedCapitals->toArray()[2]);
        die;

        // ----------------------------------------------------------------------------

        $pc = new PaymentsController();
        $array = $pc->getPaymentsStats();
        $array = $array["items"];
        dd($array);

        $cc = new CapitalsController();
        $capitals = $cc->getSentBySumQuery("comment", "donel");
        dd($capitals);
        die;

        // ----------------------------------------------------------------------------

        $ac = new ActorsController();

        $botname = "GutoTradeBot";

        $suscriptors = $ac->getAll();
        foreach ($suscriptors as $actor) {
            if (isset($actor->data[$botname])) {
                if (!isset($actor->data["telegram"]) && !isset($actor->data["telegram"]["id"])) {
                    $array = $actor->data;

                    $tc = new TelegramController();
                    $response = json_decode($tc->getUserInfo($actor->user_id, $bot->getToken($botname)), true);
                    if (isset($response["result"])) {
                        $array["telegram"] = $response["result"];
                        $array["telegram"]["pinned_message"] = false;
                        $array["telegram"]["photo"] = false;

                        $photos = $tc->getUserPhotos($actor->user_id, $bot->getToken($botname));
                        if (count($photos) > 0) {
                            $array["telegram"]["photo"] = $photos[0][count($photos[0]) - 1]["file_id"];
                        }

                        if (isset($array["telegram"]["username"])) {
                            echo $array["telegram"]["username"] . "<br/>";
                        }

                        $actor->data = $array;
                        $actor->save();
                    }
                }
            }
        }
        die(date("Y-m-d H:i:s") . ": DONE!");

        // ----------------------------------------------------------------------------

        $to_date = Carbon::createFromFormat("Y-m-d H:i:s", Carbon::now()->format("Y-m-d") . " 23:59:59");
        $from_date = $to_date->clone()->subMonths(1);

        $pc = new PaymentsController();
        $records = $pc->getRecords($from_date, $to_date);
        //dd($records);
        $sendamount = 0;
        $records["sentprom"] = [];
        $receivedamount = 0;
        $records["receivedprom"] = [];
        for ($i = 0; $i < count($records["confirmeds"]); $i++) {
            $records["sents"][$i] -= $records["confirmeds"][$i];

            $sendamount += $records["confirmeds"][$i];
            $records["sentprom"][$i] = $sendamount / ($i + 1);

            $receivedamount += $records["receiveds"][$i];
            $records["receivedprom"][$i] = $receivedamount / ($i + 1);
        }
        if (count($records["dates"]) == 0) {
            array_push($records["dates"], Carbon::now()->subDays(1)->toDateString());
            array_push($records["dates"], Carbon::now()->toDateString());
            array_push($records["receiveds"], 0);
            array_push($records["receiveds"], 0);
            array_push($records["confirmeds"], 0);
            array_push($records["confirmeds"], 0);
            array_push($records["sents"], 0);
            array_push($records["sents"], 0);
            array_push($records["balances"], 0);
            array_push($records["balances"], 0);
            array_push($records["confirmed_balances"], 0);
            array_push($records["confirmed_balances"], 0);
        }

        $array = [
            [
                "values" => [[$records["sents"], $records["confirmeds"]], $records["receiveds"]],
                "color" => [["#fafa8f", "#fbdfaa"], "#aeffae"],
                "label" => [[null, "Enviado"], "Recibido"],
            ],
            [
                "values" => [$records["sentprom"], $records["receivedprom"]],
                "weight" => 3,
                "color" => ["#eb6f01", "#12b512"],
                "label" => "Balance",
                "trend" => [
                    "style" => "solid",
                    "weight" => 2,
                ],
            ],
        ];

        $filename = GraphsController::generateGroupBarsGraph($records["dates"], $array);
        //dd($filename);
        die("<img src='http://localhost/micalme/autodestroy/" . $filename . ".jpg'/>");

        // ----------------------------------------------------------------------------

        $amount = 100;
        //$rate = CoingeckoController::getRate();
        $rate = 1.06;
        echo "Exchange: {$rate}<br/>";

        $arrival = $amount * $rate;
        echo "Llegaron: {$arrival}<br/>";

        $pc = new ProfitsController();
        $salary = $pc->getFirst(Profits::class, "name", "=", "salary");
        $salary_percent = $salary->value;
        $salary = Profits::getSalary($arrival);
        echo "Salario: {$salary}<br/>";

        $towork = $arrival - $salary;
        echo "A trabajar: {$towork}<br/>";

        $profit = $pc->getFirst(Profits::class, "name", "=", "profit");
        $profit_percent = $profit->value;
        $tosend_percent = $profit_percent + $salary_percent;
        $profit = $towork * $tosend_percent / 100;
        $tosend = $towork + $profit;
        echo "A enviar: {$tosend}<br/>";

        $text = "▫️                     _Exchange " . Moneys::format($rate) . "_                    ▫️\n" .
            "▫️ *" . Moneys::format($amount) . "* 💶 -----------------------> *" . Moneys::format($arrival) . "* 💵 ▫️\n" .
            "▫️                                          _-" . $salary_percent . "%_        *" . Moneys::format($salary) . "* 💵 ▫️\n" .
            "▫️                                                    ----------------- ▫️\n" .
            "▫️ *" . Moneys::format($tosend) . "* 💶 <-----------------------   *" . Moneys::format($towork) . "* 💵 ▫️\n" .
            "▫️                       _VIP " . $tosend_percent . "%_   " . Moneys::format($profit) . "                   ▫️\n";

        $tc = new TelegramController();
        $array = [
            "message" => [
                "text" => $text,
                "chat" => [
                    "id" => config('metadata.system.app.telegram.bot.owner'),
                ],
            ],
        ];
        $response = json_decode($tc->sendMessage($array, "7543090584:AAEisZYB1NL24Wwwv2xQ2rVChOugyXYLdBU"), true);
        dd($response);
        die(date("Y-m-d H:i:s") . ": DONE!");

        dd($profit);
        die;

        // ----------------------------------------------------------------------------

        $tc = new TelegramController();
        $array = [
            "message" => [
                "text" => "*Esta es una* ~prueba~",
                "chat" => [
                    "id" => config('metadata.system.app.telegram.bot.owner'),
                ],
            ],
        ];
        $response = json_decode($tc->getUserInfo(5328142807, "7543090584:AAEisZYB1NL24Wwwv2xQ2rVChOugyXYLdBU"), true);
        dd($response);
        die(date("Y-m-d H:i:s") . ": DONE!");

        $array = json_decode($tc->sendMessage($array, "7543090584:AAEisZYB1NL24Wwwv2xQ2rVChOugyXYLdBU"), true);

        die(date("Y-m-d H:i:s") . ": DONE!");

        // ----------------------------------------------------------------------------

        $cc = new CapitalsController();
        $capital = $cc->create(Capitals::getEURtoSendWithActiveRate(100), 100, "path", 81, 100, []);
        die(date("Y-m-d H:i:s") . ": DONE!");

        // ----------------------------------------------------------------------------

        /*
        $bot = new GutoTradeBotController();
        $ac = new ActorsController();
        $actor = $ac->getFirst(Actors::class, 'user_id', '=', config('metadata.system.app.telegram.bot.owner'));

        $reply = $bot->notifyStats($request, $actor, "2024-07-17", "2024-11-20");
        dd($reply);
        die;
         */

        /*
        $cc = new CapitalsController();
        $payments = Payments::where('data', 'LIKE', "%:7%")->get();
        foreach ($payments as $payment) {
        $data = $payment->data;
        $data["rate"] = Profits::getProfit(100);
        $data["capital"] = Profits::getSpended($payment->amount);
        $payment->data = $data;
        $payment->save();
        }

        dd($payments->toArray());

        $results = $cc->get(Capitals::class, "created_at", ">=", "2025-01-01 00:00:00");
        foreach ($results as $capital) {
        $newamount = MathController::round(Capitals::getEURtoSendWithActiveRate($capital->comment), 2, false);
        echo "Recibido: {$capital->comment}, Antes: {$capital->amount}, Ahora: {$newamount}<br/>";
        //$capital->amount = $newamount;
        //$capital->save();
        }
        //dd($results->toArray());
         */

        $amount = 100;
        echo "Recibido: " . $amount . " USDT<br/>";
        echo "Salario: " . Profits::getSalary($amount) . " USDT<br/>";
        echo "Ganancia: " . Profits::getProfit($amount) . " EUR<br/>";
        $send = Profits::toSend($amount);
        echo "A Enviar: " . $send . " EUR<hr/>";
        echo "Enviado: " . $send . " EUR<br/>";
        echo "Usado de lo recibido: " . Profits::getSpended($send) . " USDT<br/>";
        echo "Mi salario: " . Profits::getEarned($send) . " USDT<br/>";
        echo "Debi recibir: " . Profits::getUSDTreceived($send) . " USDT<br/>";

        /*
        $response = Http::withOptions([
        'verify' => false, // Desactiva la verificación SSL
        ])->post("https://api.mainnet-beta.solana.com", [
        "jsonrpc" => "2.0",
        "id" => 1,
        "method" => "getTransaction",
        "params" => [
        "HJYFeECnjuXPB5gZGrk5oz3mo94KVeEsuKLgYcvV3vkgLnL3yBGDPaoHYKh2dzSQqYWBpijVzCNkdR8YdeTe3k2", // Firma de la transacción
        [
        "encoding" => "jsonParsed",
        "maxSupportedTransactionVersion" => 0, // Parámetro adicional
        ],
        ],
        ]);

        if ($response->ok()) {
        $data = $response->json();
        if (isset($data['result'])) {
        $transaction = $data['result'];
        //dd($transaction);

        $originWallets = $transaction['transaction']['signatures'] ?? [];
        $destinationWallets = [];

        foreach ($transaction['transaction']['message']['accountKeys'] ?? [] as $account) {
        if ($account['pubkey']) {
        dd($account['pubkey']);
        }
        }
        }
        }
         */

        // obtener el mensaje --------------------------------------------------------------------------------
        // Conectar al servidor IMAP
        $client = Client::account('default');

        // Intentar conectarse
        $client->connect();

        // Abrir la bandeja de entrada
        $inbox = $client->getFolder('INBOX');
        // Obtener el contenido de cada campo deseado
        $data = [];

        $messages = $inbox->query()->all()->get();
        foreach ($messages as $message) {
            $item = [
                "links" => [],
            ];
            // Obtener asunto y cuerpo del mensaje
            $item["subject"] = str_ireplace("[alert] ", "", $message->getSubject());

            $html = $message->getHTMLBody();

            $dom = new DOMDocument();
            @$dom->loadHTML($html); // El uso de @ evita warnings en HTML mal formado

            // Buscar todos los elementos <b>
            $bTags = $dom->getElementsByTagName('b');
            foreach ($bTags as $bTag) {
                if (stripos($bTag->textContent, "timestamp") !== false) {
                    $item["timestamp"] = trim($bTag->nextSibling->textContent);
                }
                if (stripos($bTag->textContent, "changes") !== false) {
                    $text = $bTag->nextSibling->textContent;
                    $text = str_replace("ð´", "🔴", $text);
                    $text = str_replace("ð¢", "🟢", $text);
                    $item["changes"] = trim($text);
                }
            }
            // Buscar todos los elementos <a>
            $aTags = $dom->getElementsByTagName('a');
            switch (count($aTags)) {
                case 1:
                    $item["links"]["tx"] = $aTags[0]->getAttribute('href');
                    break;
                case 2:
                    $item["links"]["tx"] = $aTags[1]->getAttribute('href');
                    $item["links"]["token"] = $aTags[0]->getAttribute('href');

                    $array = explode("/token/", $item["links"]["token"]);
                    if (count($array) > 1) {
                        $item["links"]["token"] = "https://www.birdeye.so/token/{$array[1]}?chain=solana";
                    }
                    break;

                default:
                    # code...
                    break;
            }

            if (isset($item["links"]["token"])) {
                $text = "*" . $item["subject"] . "*\n" .
                    "📅 " . $item["timestamp"] . "\n\n" .
                    $item["changes"] . "\n\n";
                //$text .= "📈 [Chart](" . $item["links"]["token"] . ") | ";

                // $text .= "🔗 [TX](" . $item["links"]["tx"] . ")\n\n";

                $tc = new TelegramController();
                $array = [
                    "message" => [
                        "text" => "{$text}",
                        "chat" => [
                            "id" => config('metadata.system.app.telegram.bot.owner'),
                        ],
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [
                                    ['text' => '📈 Chart on Birdeye', 'url' => $item["links"]["token"]],
                                    ['text' => '🔗 TX on Solscan', 'url' => $item["links"]["token"]],
                                ],
                            ],
                        ]),
                    ],
                ];
                $array = json_decode($tc->sendMessage($array), true);

                $data[] = $item;
            }

        }
        dd($data);
        die;

        // ----------------------------------------------------------------------------

        // analizar el token---------------------------------------------------------------------------
        $tokenaddress = "H1hcBegR2A6b2mCGqhH5zA4sTZvMxrqT4v3fHY1kpump";

        $tc = new TokenController();
        $token = $tc->analize($tokenaddress);
        $html = $tc->formatInfo($token);
        $text = $tc->formatInfo($token, "telegram");
        echo $html;

        $tc = new TelegramController();
        $array = [
            "message" => [
                "text" => $text,
                "chat" => [
                    "id" => config('metadata.system.app.telegram.bot.owner'),
                ],
            ],
        ];
        $tc->sendMessage($array);

        dd($token);
        die;

        // ----------------------------------------------------------------------------

        $ac = new ApexProController();
        $bc = new BingXController();
        $tc = new TelegramController();
        $tsc = new TradingSuscriptionsController();
        $tv = new TradingViewController();

        $suscriptor = $tsc->getFirst('user_id', '=', config('metadata.system.app.telegram.bot.owner'));

        switch ($request["name"]) {
            case "telegramuser":
                echo config('metadata.system.app.telegram.bot.owner') . "<hr/>";
                $response = $tc->getUserInfo(
                    1159726742
                );
                var_dump($response);
                break;
            case "perpetual":
                $response = $bc->perpetualTradeOrderTest(
                    $suscriptor->data["exchanges"]["bingx"]["api_key"],
                    $suscriptor->data["exchanges"]["bingx"]["secret_key"],
                    [
                        "symbol" => "BTC-USDT",
                        "side" => "BUY",
                        "positionSide" => "LONG",
                        "type" => "MARKET",
                        "quantity" => 5,
                    ],
                );
                var_dump($response);
                break;
            case "vst":
                $contract = $bc->perpetualAccountQuoteContractsFormated(
                    $suscriptor->data["exchanges"]["bingx"]["api_key"],
                    $suscriptor->data["exchanges"]["bingx"]["secret_key"],
                    [],
                    "sol"
                );
                // $contract["tradeMinQuantity"] $contract["tradeMinUSDT"]
                //var_dump($response);
                break;
            case "leverage":
                $response = json_decode($bc->futuresQueryLeverage(
                    $suscriptor->data["exchanges"]["bingx"]["api_key"],
                    $suscriptor->data["exchanges"]["bingx"]["secret_key"],
                    [
                        "symbol" => "SOL-USDT",
                    ]
                ), true);
                var_dump($response);
                echo "<br/><br/><hr/>";

                $response = json_decode($bc->futuresSwitchLeverage(
                    $suscriptor->data["exchanges"]["bingx"]["api_key"],
                    $suscriptor->data["exchanges"]["bingx"]["secret_key"],
                    [
                        "symbol" => "SOL-USDT",
                        "leverage" => "8",
                        "side" => "SHORT",
                    ]
                ), true);
                var_dump($response);
                echo "<br/><br/><hr/>";

                $response = json_decode($bc->futuresQueryLeverage(
                    $suscriptor->data["exchanges"]["bingx"]["api_key"],
                    $suscriptor->data["exchanges"]["bingx"]["secret_key"],
                    [
                        "symbol" => "SOL-USDT",
                    ]
                ), true);
                var_dump($response);
                die;

                break;

            case "commissionrate":
                $response = json_decode($bc->spotQueryTradingCommissionRate(config('metadata.system.app.telegram.bot.owner'), [
                    "symbol" => "SOL-USDT",
                ]), true);
                var_dump($response);
                die;

                break;

            case "trade":
                /*
                $bc->spotTradeCreateOrder(1, [
                "symbol" => message.additionalData.basecurrency+"-"+message.additionalData.currency,
                "side" => message.additionalData.action.toUpperCase(),
                "type"=> "MARKET",
                "quantity"=> message.additionalData.contracts
                ]);
                "payload": {
                "symbol": "NEMS-USDT",
                "side": "SELL",
                "type": "LIMIT",
                "quantity": "115",
                "price": "0.1557",
                "timestamp": "1702720966321"
                },
                 */
                $payload = [
                    "symbol" => "SOL-USDT",
                    "side" => "BUY",
                    "type" => "MARKET",
                    "quantity" => "0.01",
                ];
                $response = json_decode($bc->spotTradeCreateOrder(config('metadata.system.app.telegram.bot.owner'), $payload), true);
                /*
                string(164) "{"code":100400,"msg":" check market entrust volume fail, entrust volume to low, userID: 1073074124988538881, minVolume:0.0272, entrustVolume: 0.0100","debugMsg":""}"
                string(292) "{"code":0,"msg":"","debugMsg":"","data":{"symbol":"SOL-USDT","orderId":1770113650711855104,"transactTime":1710862956108,"price":"183.03","stopPrice":"0","origQty":"0.03","executedQty":"0.03","cummulativeQuoteQty":"5.490834","status":"FILLED","type":"MARKET","side":"SELL","clientOrderID":""}}"
                 */
                //$response = json_decode("{\"code\":0,\"msg\":\"\",\"debugMsg\":\"\",\"data\":{\"symbol\":\"SOL-USDT\",\"orderId\":1770113650711855104,\"transactTime\":1710862956108,\"price\":\"183.03\",\"stopPrice\":\"0\",\"origQty\":\"0.03\",\"executedQty\":\"0.03\",\"cummulativeQuoteQty\":\"5.490834\",\"status\":\"FILLED\",\"type\":\"MARKET\",\"side\":\"SELL\",\"clientOrderID\":\"\"}}", true);
                //var_dump($response);
                if ($response["code"] > 0) { // error al ejecutar la orden
                    $text = "🔴 *ERROR creating trade order on BingX*:\n\n🎟 *Order*: " . json_encode($payload) . "\n\n🐞 *Code*: {$response["code"]}\n🙇🏻 *Response*:{$response["msg"]}";
                } else {
                    $orderId = (string) number_format($response["data"]["orderId"], 0, '', '');
                    $text = "🟢 *Created order {$orderId} on BingX*:\n\n🎟 *Order*: " . json_encode($payload) . "\n\n💵 *Price*: {$response["data"]["price"]}\n✔️ *origQty*: {$response["data"]["origQty"]}\n👉 *executedQty*: {$response["data"]["executedQty"]}\n📦 *cummulativeQuoteQty*: {$response["data"]["cummulativeQuoteQty"]}\n✅ *status*: {$response["data"]["status"]}";
                }
                $request["message"] = [
                    "text" => $text,
                    "chat" => [
                        "id" => config('metadata.system.app.telegram.bot.owner'),
                    ],
                ];
                $response = json_decode($tc->sendMessage($request), true);

                break;
            case "tradingview":
                $request["message"] = [
                    "text" => "✅ *buy DVZUSDT*\n💰 *buy*: 0.099 DVZ\n💵 *Price*: 100.494 USDT\n🏦 *Position*:0.099",
                    "chat" => [
                        "id" => config('metadata.system.app.telegram.bot.owner'),
                    ],
                    "additionalData" => [
                        "basecurrency" => "DVZ",
                        "currency" => "USDT",
                        "time" => "2024-02-23T17:30:00Z",
                        "ticker" => "DVZUSDT",
                        "volume" => "0.0298",
                        "close" => "100.494",
                        "open" => "100.494",
                        "high" => "100.494",
                        "low" => "100.494",
                        "position_size" => "0.099",
                        "action" => "sell",
                        "contracts" => "0.099",
                        "price" => "100.494",
                        "id" => "D93-BO",
                        "comment" => "D93-BO",
                        "alert_message" => "el mensaje",
                        "market_position" => "long",
                        "market_position_size" => "0.099",
                        "prev_market_position" => "flat",
                        "prev_market_position_size" => "0",
                    ],
                ];
                $response = json_decode($tv->webhook($request), true);
                var_dump($response);
                echo "</hr>";
                break;
            case "telegram":
                $request["message"] = [
                    "text" => "hola",
                    "chat" => [
                        "id" => config('metadata.system.app.telegram.bot.owner'),
                    ],
                ];
                $response = json_decode($tc->sendMessage($request), true);
                var_dump($response);
                echo "</hr>";

                $request["message"] = [
                    "id" => $response["result"]["message_id"],
                    "chat" => [
                        "id" => config('metadata.system.app.telegram.bot.owner'),
                    ],
                ];
                $response = $tc->deleteMessage($request);
                var_dump($response);
                break;
            case "async":
                $promise = Http::async()->get('https://www.laravelia.com');

                // Obtén el resultado de la solicitud asincrónica
                $promise->then(function ($response) {
                    return $response->body();
                });
                break;
            default:
                die($request["name"]);
            //break;
        }
    }
}
