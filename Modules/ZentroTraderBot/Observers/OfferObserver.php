<?php

namespace Modules\ZentroTraderBot\Observers;

use Modules\ZentroTraderBot\Entities\Offers;
use Modules\ZentroTraderBot\Entities\OffersAlerts; // Asumiendo que OffersAlerts también está en el módulo
use Modules\TelegramBot\Http\Controllers\TelegramController;

class OfferObserver
{
    /**
     * Manejar el evento "created" de la Oferta.
     */
    public function created(Offers $offer): void
    {
        // Buscamos las alertas que coincidan
        $matchingAlerts = OffersAlerts::where('type', $offer->type)
            ->where('payment_method', $offer->payment_method)
            ->where('is_active', true)
            ->get();

        // mandarlo al canal y al grupo

        // mandarlo a los usurios q estan buscando algo asi
        foreach ($matchingAlerts as $alert) {
            // Validamos que el usuario tenga un telegram_id antes de intentar enviar
            if ($alert->user && $alert->user->telegram_id) {
                /*
                Telegram::sendMessage([
                    'chat_id' => $alert->user->telegram_id,
                    'text' => "🚀 Nueva oferta detectada: {$offer->amount} USD vía {$offer->payment_method}"
                ]);
                */

                dispatch(function () use ($message_id, $chat_id, $bot_token) {
                    $array = array(
                        "message" => array(
                            "id" => $message_id,
                            "chat" => array(
                                "id" => config('metadata.system.app.zentrotraderbot.telegram.community.group'),
                            ),
                        ),
                    );
                    $controller = new TelegramController();
                    $controller->sendMessage($array, $bot_token);

                    $this->notifyOnTelegram($info, $payload, );
                    //app_zentrotraderbot_telegram_community_group
                })->delay(now()->addMinutes($autodestroy));


            }
        }
    }
}