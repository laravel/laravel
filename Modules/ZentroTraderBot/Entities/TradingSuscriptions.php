<?php

namespace Modules\ZentroTraderBot\Entities;

use Modules\TelegramBot\Entities\Actors;

class TradingSuscriptions extends Actors
{

    public function isReadyForExchange($exchange)
    {
        $array = $this->data[$this->telegram["username"]];
        unset($array["exchanges"]["active"]);

        $ready = count($array["exchanges"][$exchange]) > 0;
        foreach ($array["exchanges"][$exchange] as $key => $value) {
            if ($value == "") {
                $ready = false;
                break;
            }
        }

        return $ready;
    }

    public function hasActiveExchange()
    {
        $array = $this->data[$this->telegram["username"]];
        if (isset($array["exchanges"]["active"])) {
            foreach ($array["exchanges"]["active"] as $id) {
                if ($id != "") {
                    return true;
                }
            }
        }

        return false;
    }

    public static function getSuscriptorTemplate($array = array())
    {
        if (!isset($array["bingx"])) {
            $array["bingx"] = array();
            foreach (TradingSuscriptions::$EXCHANGES["bingx"]["fields"] as $field) {
                $array["bingx"][$field] = "";
            }

            $array["bingx"]["base_order_size"] = 10;
        }

        if (!isset($array["apexpromainnet"])) {
            $array["apexpromainnet"] = array();
            foreach (TradingSuscriptions::$EXCHANGES["apexpromainnet"]["fields"] as $field) {
                $array["apexpromainnet"][$field] = "";
            }

            $array["apexpromainnet"]["base_order_size"] = 1;
        }

        if (!isset($array["apexprotestnet"])) {
            $array["apexprotestnet"] = array();
            foreach (TradingSuscriptions::$EXCHANGES["apexprotestnet"]["fields"] as $field) {
                $array["apexprotestnet"][$field] = "";
            }

            $array["apexprotestnet"]["base_order_size"] = 1;
        }

        if (!isset($array["active"])) {
            $array["active"] = array();
        }

        if (!isset($array["admin_level"])) {
            $array["admin_level"] = 0;
        }

        if (!isset($array["suscription_level"])) {
            $array["suscription_level"] = 0;
        }

        return [
            "exchanges" => [
                "bingx" => $array["bingx"],
                "apexpromainnet" => $array["apexpromainnet"],
                "apexprotestnet" => $array["apexprotestnet"],
                "active" => $array["active"],
            ],
            "admin_level" => $array["admin_level"],
            "suscription_level" => $array["suscription_level"],
            "last_bot_callback_data" => "",
        ];
    }

    public static $EXCHANGES = array(
        "bingx" => array(
            "icon" => "🏦",
            "name" => "BingX",
            "base_order_size" => 10,
            "fields" => array("api_key", "secret_key"),
        ),
        "apexpromainnet" => array(
            "icon" => "🦍",
            "name" => "ApexPro Mainnet",
            "base_order_size" => 1,
            "fields" => array("account_id", "api_key", "api_key_passphrase", "api_key_secret", "stark_key_private", "stark_key_public", "stark_key_public_key_y_coordinate"),
        ),
        "apexprotestnet" => array(
            "icon" => "🙊",
            "name" => "ApexPro Testnet",
            "base_order_size" => 1,
            "fields" => array("account_id", "api_key", "api_key_passphrase", "api_key_secret", "stark_key_private", "stark_key_public", "stark_key_public_key_y_coordinate"),
        ),
    );

    public static $KNOWN_SUSCRIPTORS = array(
        "dvzambrano" => [
            "bingx" => [
                "api_key" => "F3pw0O44pv5QhGGuKzY9AyHZq60NYf3THq5QlfIaq98sS1KBIROzeLB2aUI7U7bUqUSnjMDfw3licUQDdhcbQ",
                "secret_key" => "DzLfyysYVDVQYVtEDe4z8AixcI0eDfNbBuYt4gJBm68jdR3D7PoCCxLG4PRnIokZEOyvl11PtuQBg06ORQ",
                "base_order_size" => 10,
            ],
            "apexpromainnet" => [
                "account_id" => "582636658558500973",
                "api_key" => "7db9ecef-ae49-919c-1846-d940915038f3",
                "api_key_passphrase" => "LE28zjRYnLxCg6gSwNt6",
                "api_key_secret" => "vaDMMnqqpaAw2zuM8PFLz7APToQ0u67HmIIaoaCg",
                "stark_key_private" => "0x075ad444fa7b1192d97a7d70d41e0e84aecb5ab24e94cfd66ff132c6fa080371",
                "stark_key_public" => "0x02ca7badf2d37abdc8e3b750f4f73a817e0a6baf309fb668d00b96e840cb58ed",
                "stark_key_public_key_y_coordinate" => "0x04b45a88a2c5f7692ce9f1bb025fb182300925d3111e903d68d72fea660fb668",
                "base_order_size" => 1,
            ],
            "apexprotestnet" => [
                "account_id" => "582635608690655601",
                "api_key" => "2e705f3e-1b37-3224-9977-9cfa5b17b1c2",
                "api_key_passphrase" => "MBQBXf9WXwKDB991TGGh",
                "api_key_secret" => "2AhyVMp8ZZ2SI6NeJlQq7tw65-hj8PxfkR2MNZ_P",
                "stark_key_private" => "0x07ead10517457b1a57f17bda2a7253962457e39ce22d4b469468f1f6dde1434b",
                "stark_key_public" => "0x0678a0b412a405b276ff2ce43c5bab96f49a86a7be1e9bde0941e5b3a37070f1",
                "stark_key_public_key_y_coordinate" => "0x05cf53cfd299bb9452bf0f750de4935e672500fa11474acfb042fce4d1f1f2d3",
                "base_order_size" => 5,
            ],
            "admin_level" => 2,
            "suscription_level" => 2,
            "active" => ["apexprotestnet", "apexpromainnet", "bingx"],
        ],
    );
}
