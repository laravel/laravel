<?php

namespace Modules\TelegramBot\Entities;

use App\Traits\UsesModuleConnection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Actors extends Model
{
    use UsesModuleConnection;

    protected $table = "actors";

    protected $fillable = ['user_id', 'data'];

    protected $casts = [
        'data' => 'json',
    ];

    public $timestamps = false;

    public function getDescendants($bot, $user_id = false)
    {
        if (!$user_id) {
            $user_id = $this->user_id;
        }

        // Array para almacenar los descendientes
        $descendants = [];

        // Obtener los hijos directos del actor
        $children = self::whereRaw("JSON_EXTRACT(data, '$." . $bot->telegram["username"] . ".parent_id') = ?", [$user_id])->get();

        foreach ($children as $child) {
            // Agregar el ID del hijo al array
            $descendants[] = $child->user_id;

            // Llamar recursivamente para obtener los descendientes del hijo
            $descendants = array_merge($descendants, $this->getDescendants($bot, $child->user_id));
        }

        return $descendants;
    }

    public function isDescendantOf($bot)
    {
        $descendants = $this->getDescendants($bot, $bot->actor->user_id);
        if (in_array($this->user_id, $descendants)) {
            return true;
        }

        return false;
    }

    public function getTelegramInfo($bot, $key = false)
    {
        $telegram = false;
        if (isset($this->data["telegram"]))
            $telegram = $this->data["telegram"];
        else {
            $response = json_decode($bot->TelegramController->getUserInfo($this->user_id, $bot->getToken($bot->telegram["username"])), true);
            $telegram = $response["result"];
        }

        $telegram["formated_username"] = "";
        if (isset($telegram["username"]))
            $telegram["formated_username"] = $bot->TelegramController->escapeText4Url($telegram["username"]);

        if (
            $telegram &&
            $key &&
            isset($telegram[$key])
        )
            return $telegram[$key];

        return $telegram;
    }

    public function isLevel($level, $bot)
    {
        $result = false;

        if (isset($this->data[$bot])) {
            $result = $this->data[$bot]["admin_level"] == $level;
        }

        return $result;
    }

    public static function getTemplate($admin_level = 0, $parent_id = false)
    {
        $data = array(
            // admin_level = 1 Admnistrador, 2 Remesador, 3 Receptor
            "admin_level" => $admin_level,
            "last_bot_callback_data" => "",
        );
        if ($parent_id && is_numeric($parent_id)) {
            $data['parent_id'] = $parent_id;
        }

        return $data;
    }

    public function getLocalDateTime($date, $botname)
    {
        if (isset($this->data[$botname]["time_zone"])) {
            // Log::info("Actors getLocalDateTime time_zone='" . $this->data[$botname]["time_zone"] . "'");
            $date = Carbon::createFromFormat("Y-m-d H:i:s", $date)->addHours(intval($this->data[$botname]["time_zone"]))->format("Y-m-d H:i:s");
        }
        return $date;
    }

    public static $KNOWN_ACTORS = array(
        "dvzambrano" => array(
            "user_id" => 816767995,
            "role" => 1,
        ),
        /*
    "Roger" => array(
    "user_id" => 5482646491,
    "role" => 4,
    ),
    "Dayami" => array(
    "user_id" => 6277250767,
    "role" => 3,
    ),
    "Arquimides" => array(
    "user_id" => 347888105,
    "role" => 2,
    ),
    "DrLimonta" => array(
    "user_id" => 1419502564,
    "role" => 2,
    ),
    "AZOR79" => array(
    "user_id" => 1269084609,
    "role" => 2,
    ),
    "GermanDavid" => array(
    "user_id" => 873754229,
    "role" => 2,
    ),
    "Yander.ron" => array(
    "user_id" => 613173575,
    "role" => 2,
    ),
    "Anibal" => array(
    "user_id" => 1358852792,
    "role" => 2,
    ),
    "TheSon_ofGod" => array(
    "user_id" => 1256079990,
    "role" => 2,
    ),
    "Locol2023" => array(
    "user_id" => 6211414111,
    "role" => 2,
    ),
    "Lixandro" => array(
    "user_id" => 5919527201,
    "role" => 2,
    ),
    "criptodev1981" => array(
    "user_id" => 1741391257,
    "role" => 2,
    ),
    "GerardGames" => array(
    "user_id" => 895670352,
    "role" => 2,
    ),
    "Deivys2000" => array(
    "user_id" => 5508220560,
    "role" => 2,
    ),
    "EL_Lobo_DPEPDE" => array(
    "user_id" => 5328142807,
    "role" => 2,
    ),
    "KarimB99" => array(
    "user_id" => 5219069448,
    "role" => 2,
    ),
    "Alej1961" => array(
    "user_id" => 6549567189,
    "role" => 2,
    ),
    "EdutroLL" => array(
    "user_id" => 1562139660,
    "role" => 2,
    ),
    "chichifuentes" => array(
    "user_id" => 1705333263,
    "role" => 2,
    ),
    "Jalvaro98" => array(
    "user_id" => 1314081227,
    "role" => 2,
    ),
     */
    );
}
