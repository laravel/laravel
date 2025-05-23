<?php

namespace Modules\TelegramBot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function analizeUrl($url)
    {
        // Primero, utilizamos parse_url para obtener la parte del "path"
        $array = parse_url($url);
        $array["url"] = $url;
        // Ahora obtenemos el "path" completo
        // Luego, usamos explode para dividir la cadena y obtener solo el último segmento
        $array["path_parts"] = explode('/', $array['path']);
        // Si hay una query string, la extraemos
        $query = [];
        if (isset($array['query'])) {
            parse_str($array['query'], $query);
        }
        $array["query_parts"] = $query;

        return $array;
    }

    public function cleanText4Url($text)
    {
        // Lista de caracteres problemáticos a reemplazar
        $chars = [
            '_' => ' ',
            '+' => '',
            '%' => '',
            '&' => '',
            '#' => '',
            '=' => '',
            '?' => '',
            '/' => '',
            '\\' => '',
            //' ' => '',
        ];
        return strtr($text, $chars);
    }
    public function escapeText4Url($text)
    {
        // Lista de caracteres problemáticos a reemplazar
        $chars = [
            '_' => '\_', // Escapar el guion bajo
            '+' => '\+', // Escapar el símbolo más
            '%' => '\%', // Escapar el porcentaje
            '&' => '\&', // Escapar el ampersand
            '#' => '\#', // Escapar el símbolo de número
            '=' => '\=', // Escapar el signo igual
            '?' => '\?', // Escapar el signo de interrogación
            '/' => '\/', // Escapar la barra
            '\\' => '\\\\', // Escapar la barra invertida
        ];
        return strtr($text, $chars);
    }

    // ["result":["message_id":ID]] ID = 0 ERROR; ID = -1 DEMO
    public function send($request, $url, $attempt = 1, $data = false)
    {
        try {
            // si es DEMO escribimos en la consola y retornamos message_id -1
            if (isset($request["demo"])) {
                echo "message = ";
                var_dump(
                    array(
                        "url" => $url,
                        "message" => $request["message"],
                    )
                );

                return json_encode(
                    array(
                        "result" => array(
                            "message_id" => -1,
                        ),
                    )
                );
            }

            $url .= "&parse_mode=Markdown";
            if (isset($request["message"]["reply_to_message_id"]) && $request["message"]["reply_to_message_id"] != "") {
                $url .= "&reply_to_message_id={$request["message"]["reply_to_message_id"]}";
            }
            if (isset($request["message"]["reply_markup"]) && $request["message"]["reply_markup"] != "") {
                $reply_markup = urlencode($request["message"]["reply_markup"]);
                $url .= "&reply_markup={$reply_markup}";
            }

            $response = file_get_contents($url, false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/x-www-form-urlencoded",
                    'content' => $data ? http_build_query($data) : false,
                ],
            ]));

            return $response;

        } catch (\Throwable $th) {
            $array = $this->analizeUrl($url);
            $method = $array["path_parts"][count($array["path_parts"]) - 1];
            Log::error("TelegramController {$method} attempt {$attempt}, CODE: {$th->getCode()}, line {$th->getLine()}, URL: {$url}, Message: {$th->getMessage()}");
            //Log::error("TelegramController TraceAsString: " . $th->getTraceAsString());

            // si hay algun error retornamos message_id 0
            return json_encode(
                array(
                    "result" => array(
                        "message_id" => 0,
                        "text" => $th->getMessage(),
                    ),
                )
            );
        }

    }

    private function detroyMessage($bot_token, $request, $secounds = 5)
    {
        $controller = $this;
        dispatch(function () use ($controller, $request, $bot_token) {
            $array = array(
                "message" => array(
                    "id" => $request["result"]["message_id"],
                    "chat" => array(
                        "id" => $request["result"]["chat"]["id"],
                    ),
                ),
            );
            $controller->deleteMessage($array, $bot_token);
        })->delay(now()->addSeconds($secounds));
    }

    public function sendMessage($request, $bot_token, $minutes = 0)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/sendMessage?chat_id={$request["message"]["chat"]["id"]}" .
            "&text=" . urlencode($request["message"]["text"]);

        $response = $this->send($request, $url);

        if ($minutes > 0) {
            $array = json_decode($response, true);
            $request["result"] = $array["result"];
            if (isset($array["result"]) && isset($array["result"]["message_id"]) && $array["result"]["message_id"] > 0) {
                $this->detroyMessage($bot_token, $request, $minutes * 60);
            }
        }

        return $response;
    }

    public function sendPhoto($request, $bot_token, $minutes = 0)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/sendPhoto?chat_id={$request["message"]["chat"]["id"]}" .
            "&photo={$request["message"]["photo"]}" .
            "&caption=" . urlencode($request["message"]["text"]);

        $response = $this->send($request, $url);
        $array = json_decode($response, true);

        if (isset($array["result"]) && isset($array["result"]["message_id"]) && $array["result"]["message_id"] == 0) {
            return $this->sendMessage($request, $bot_token, $minutes);
        }

        if ($minutes > 0) {
            $array = json_decode($response, true);
            $request["result"] = $array["result"];
            if (isset($array["result"]) && isset($array["result"]["message_id"]) && $array["result"]["message_id"] > 0) {
                $this->detroyMessage($bot_token, $request, $minutes * 60);
            }
        }

        return $response;
    }

    public function sendMediaGroup($request, $bot_token)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/sendMediaGroup?chat_id={$request["message"]["chat"]["id"]}" .
            "&media={$request["message"]["media"]}";

        return $this->send($request, $url);
    }

    public function sendDocument($request, $bot_token)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/sendDocument?chat_id={$request["message"]["chat"]["id"]}" .
            "&document={$request["message"]["document"]}"
            //."&caption=" . urlencode($request["message"]["text"])
        ;

        return $this->send($request, $url);

    }
    public function pinMessage($request, $bot_token)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/pinChatMessage?chat_id={$request["message"]["chat"]["id"]}" .
            "&message_id={$request["message"]["message_id"]}";

        return $this->send($request, $url);
    }

    public function deleteMessage($request, $bot_token)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/deleteMessage?chat_id={$request["message"]["chat"]["id"]}" .
            "&message_id={$request["message"]["id"]}";

        return $this->send($request, $url);
    }

    public function forwardMessage($request, $bot_token)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/forwardMessage";

        return $this->send($request, $url, 1, [
            'chat_id' => $request["message"]["chat"]["id"],
            'from_chat_id' => $request["message"]["from"]["id"],
            'message_id' => $request["message"]["message_id"],
        ]);
    }

    public function getBotInfo($bot_token)
    {
        $response = false;
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/getMe";

        try {
            $response = file_get_contents($url);

        } catch (\Throwable $th) {
            //Log::error("TelegramController getBotInfo: " . $th->getTraceAsString());
        }

        return $response;
    }

    public function getUserInfo($userId, $bot_token)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/getChat?chat_id={$userId}";

        $json = array(
            "result" => array(
                "full_name" => "👤 {$userId}",
                "full_info" => "👤 {$userId}",
            ),
        );

        try {
            $response = file_get_contents($url);

            $json = json_decode($response, true);

            // Formando un text personalizado con los datos del usuario
            $text = "👤 ";
            if (isset($json["result"]["first_name"])) {
                $text .= $this->cleanText4Url($json["result"]["first_name"]);
            }
            if (isset($json["result"]["last_name"])) {
                $text .= " " . $this->cleanText4Url($json["result"]["last_name"]);
            }
            $json["result"]["full_name"] = $text;
            if (isset($json["result"]["username"])) {
                $json["result"]["formated_username"] = $this->escapeText4Url($json["result"]["username"]);
                $text .= " \n✉️ @" . $json["result"]["formated_username"];

            }
            $text .= " \n🆔 `" . $userId . "`";
            $json["result"]["full_info"] = $text;

        } catch (\Throwable $th) {
            //Log::error("TelegramController getUserInfo: " . $th->getTraceAsString());
        }

        return json_encode($json);
    }

    private function getUserProfilePhotos($userId, $bot_token)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/getUserProfilePhotos?user_id={$userId}";
        try {
            $response = file_get_contents($url);
            return $response;

        } catch (\Throwable $th) {
            //Log::error("TelegramController getFileUrl: " . $th->getTraceAsString());
        }
    }

    public function getUserPhotos($userId, $bot_token)
    {
        $array = array();
        $response = json_decode($this->getUserProfilePhotos($userId, $bot_token), true);
        if (isset($response["result"]) && isset($response["result"]["photos"]) && count($response["result"]["photos"]) > 0) {
            $array = $response["result"]["photos"];
        }
        return $array;
    }

    public function getFileUrl($fileId, $bot_token)
    {
        $url = "https://api.telegram.org/bot" .
            $bot_token .
            "/getFile?file_id={$fileId}";
        try {
            $response = file_get_contents($url);
            return $response;

        } catch (\Throwable $th) {
            //Log::error("TelegramController getFileUrl: " . $th->getTraceAsString());
        }
    }

    public function getFile($filePath, $bot_token)
    {
        $url = "https://api.telegram.org/file/bot" .
            $bot_token .
            "/{$filePath}";

        $contents = file_get_contents($url);

        return $contents;
    }
}
