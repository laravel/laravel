<?php

namespace App\Http\Controllers;

use App\Traits\UsesModuleConnection;
use Modules\TelegramBot\Entities\TelegramBots;

class JsonsController extends Controller
{
    use UsesModuleConnection;

    public function getToken($name)
    {
        $bot = $this->getFirst(TelegramBots::class, "name", "=", "@{$name}");
        return $bot->token;
    }

    public function getFirst($model, $field, $symbol, $value)
    {
        return $model::where($field, $symbol, $value)->first();
    }

    public function get($model, $field, $symbol, $value)
    {
        return $model::where($field, $symbol, $value)->get();
    }

    public function getLatest($model)
    {
        return $model::latest()->first();
    }

    public function getData($model, array $fields, $index = "")
    {
        $query = $model::query();

        foreach ($fields as $field) {
            $values = is_array($field['value']) ? $field['value'] : [$field['value']];

            $query->where(function ($subQuery) use ($field, $values, $index) {
                foreach ($values as $value) {
                    $method = $field['contain'] ? 'orWhereJsonContains' : 'orWhereJsonDoesntContain';
                    if ($index == "") {
                        $subQuery->{$method}('data->' . $field['name'], $value);
                    } else {
                        $subQuery->{$method}('data->' . $index . '->' . $field['name'], $value);
                    }
                }
            });
        }

        return $query->get();

        /*
    if ($contain)
    return $model::whereJsonContain('data->' . $field, $value)->get();

    return $model::whereJsonDoesntContain('data->' . $field, $value)->get();
     */
    }

    public function destroy($model, $field, $value)
    {
        $obj = $this->getFirst($model, $field, '=', $value);
        if ($obj && $obj->id > 0) {
            $model::destroy($obj->id);
        }

    }

    public function updateData($model, $field, $field_id, $key, $value, $index = "")
    {
        $obj = $this->getFirst($model, $field, '=', $field_id);
        if ($obj && $obj->id > 0) {
            $array = $obj->data;

            if ($index == "") {
                if ($value == "") {
                    unset($array[$key]);
                } else {
                    $array[$key] = $value;
                }
            } else {
                if ($value == "") {
                    unset($array[$index][$key]);
                } else {
                    $array[$index][$key] = $value;
                }
            }

            $obj->data = $array;

            $obj->save();
        }
    }
    public function notifyAfterDelete()
    {
        $reply = array(
            "text" => "👍 *Registro eliminado*\n_Se ha eliminado el registro de la base de datos satisfactoriamente._\n\n👇 Qué desea hacer ahora?",
            "markup" => json_encode([
                "inline_keyboard" => [
                    [
                        ["text" => "↖️ Volver al menú principal", "callback_data" => "menu"],
                    ],

                ],
            ]),
        );

        return $reply;
    }
}
