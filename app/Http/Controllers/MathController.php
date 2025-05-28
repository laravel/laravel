<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class MathController extends Controller
{

    public static function round($number, $decimals = 2, $up = null)
    {
        // Multiplica el número por 10 elevado a la cantidad de decimals deseados
        $factor = pow(10, $decimals);

        // Redondeo por defecto según la convención internacional
        if ($up === null) {
            return round($number * $factor) / $factor; // Redondea según la convención
        }

        // Redondear hacia arriba o hacia abajo según el parámetro
        if ($up) {
            return ceil($number * $factor) / $factor; // Redondea hacia arriba
        } else {
            return floor($number * $factor) / $factor; // Redondea hacia abajo
        }
    }


    /**
     * Suma un periodo de tiempo a una fecha.
     *
     * @param int $amount Cantidad de unidades de tiempo a agregar.
     * @param string $period Unidad de tiempo ("SECONDS", "MINUTES", "HOURS", "DAYS", "MONTHS", "YEARS").
     * @param Carbon $now Fecha a la q sumarle el periodo: si me omite se toma la actual.
     * @return int UnixTime.
     */
    public static function sumPeriodsToDate($amount, $period = "SECONDS", $now = false)
    {
        $period = strtoupper($period); // Normalizar a mayúsculas

        if ($now === false)
            $now = Carbon::now();

        switch ($period) {
            case "SECONDS":
                $futureTime = $now->addSeconds($amount)->timestamp;
                break;
            case "MINUTES":
                $futureTime = $now->addMinutes($amount)->timestamp;
                break;
            case "HOURS":
                $futureTime = $now->addHours($amount)->timestamp;
                break;
            case "DAYS":
                $futureTime = $now->addDays($amount)->timestamp;
                break;
            case "MONTHS":
                $futureTime = $now->addMonths($amount)->timestamp;
                break;
            case "YEARS":
                $futureTime = $now->addYears($amount)->timestamp;
                break;
            default:
                $futureTime = $now->timestamp;
                break;
        }

        //$date = date("Y-m-d H:i:s", $futureTime);
        //dd($date);

        return $futureTime;
    }

    /**
     * Calcula la diferencia entre dos timestamps.
     * 
     * @param int $startTime Timestamp inicial (ej: time()).
     * @param int $endTime Timestamp futuro.
     * @return array [
     *     'years'   => int,
     *     'months'  => int,
     *     'days'    => int,
     *     'hours'   => int,
     *     'minutes' => int,
     *     'seconds' => int,
     *     'legible' => string (ej: "1 año, 3 meses, 2 días, 4 hrs, 1 min, 2 seg")
     * ]
     */
    public static function getTimeDifference($startTime, $endTime)
    {
        if ($endTime < $startTime) {
            $aux = $endTime;
            $endTime = $startTime;
            $startTime = $aux;
        }

        $start = Carbon::createFromTimestamp($startTime);
        $end = Carbon::createFromTimestamp($endTime);
        $diff = $start->diff($end);

        // Valores numéricos
        $data = [
            'years' => $diff->y,
            'months' => $diff->m,
            'days' => $diff->d,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
        ];

        // Texto legible
        $parts = [];
        $units = [
            'year' => ['singular' => 'año', 'plural' => 'años'],
            'month' => ['singular' => 'mes', 'plural' => 'meses'],
            'day' => ['singular' => 'día', 'plural' => 'días'],
            'hour' => ['singular' => 'hr', 'plural' => 'hrs'],
            'minute' => ['singular' => 'min', 'plural' => 'mins'],
            'second' => ['singular' => 'seg', 'plural' => 'segs'],
        ];

        foreach ($data as $unit => $value) {
            if ($value > 0) {
                $unitName = $units[rtrim($unit, 's')]; // Elimina la 's' final (ej: 'years' -> 'year')
                $text = $value . ' ' . ($value == 1 ? $unitName['singular'] : $unitName['plural']);
                $parts[] = $text;
            }
        }

        $data['legible'] = implode(', ', $parts);

        return $data;
    }


}
