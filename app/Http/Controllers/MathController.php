<?php

namespace App\Http\Controllers;

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

}
