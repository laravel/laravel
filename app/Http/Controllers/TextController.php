<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TextController extends Controller
{

    public function standardize($text)
    {
        // Convertir a minúsculas
        $text = mb_strtolower($text, 'UTF-8');
        // Eliminar tildes y acentos
        $text = strtr($text, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'ñ' => 'n',
            'Ñ' => 'N',
        ]);
        // Eliminar espacios adicionales
        $text = trim(preg_replace('/\s+/', ' ', $text));

        return $text;
    }

    public function calculateSimilarityPercentage($text1, $text2)
    {
        // Normalizar los texts
        $text1 = $this->standardize($text1);
        $text2 = $this->standardize($text2);

        similar_text($text1, $text2, $percentage);

        return $percentage;

        /*
        // Calcular la distance Levenshtein entre los texts
        $distance = levenshtein($text1, $text2);

        // Encontrar la length máxima entre ambos texts
        $max_length = max(strlen($text1), strlen($text2));

        // Si la length máxima es 0 (ambos texts vacíos), son 100% similares
        if ($max_length == 0) {
            return 100;
        }

        // Calcular el porcentaje de similitud basado en la distance Levenshtein
        $percentage = (1 - $distance / $max_length) * 100;

        return $percentage;
        */
    }
    public function numberAsEmoji($number)
    {
        // Mapeo de dígitos a emojis (versión de Telegram)
        $map = [
            "0" => "0️⃣",
            "1" => "1️⃣",
            "2" => "2️⃣",
            "3" => "3️⃣",
            "4" => "4️⃣",
            "5" => "5️⃣",
            "6" => "6️⃣",
            "7" => "7️⃣",
            "8" => "8️⃣",
            "9" => "9️⃣",
            "." => "🔹",
            "-" => "➖"
        ];

        $string = (string) $number;
        $text = "";

        for ($i = 0; $i < strlen($string); $i++) {
            $char = $string[$i];

            if (isset($map[$char])) {
                $text .= $map[$char];
            } else {
                $text .= $char; // Si no hay emoji, mantener el carácter original
            }
        }

        return $text;
    }

    public function hide_password($text, $visible = 0)
    {
        $length = mb_strlen($text, 'UTF-8');

        if ($visible <= 0 || $visible >= $length) {
            return str_repeat('*', $length);
        }

        $hidden = $length - $visible;
        return str_repeat('*', $hidden) . mb_substr($text, -$visible, null, 'UTF-8');
    }

    public function str_pad($input, $length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        $input_length = strlen($input);

        // Si el $length es menor que la length del input y es un padding normal (no negativo)
        if ($length < $input_length && $pad_type >= 0) {
            return substr($input, 0, max($length - 3, 0)) . '...'; // Aseguramos que no sea negativo
        }

        // Si el $pad_type es negativo, rellenamos en el medio
        if ($pad_type < 0) {
            $words = explode(" ", $input);
            if (count($words) < 2) {
                return str_pad($input, $length, $pad_string, $pad_type);
            }

            $current_length = 0;
            foreach ($words as $word) {
                $current_length += strlen($word);
            }

            $padding_needed = $length - $current_length;
            if ($padding_needed <= 0) {
                return $input; // No hay espacio para rellenar
            }

            switch ($pad_type) {
                case -12:
                    $firstword = $words[0];
                    unset($words[0]);
                    $words = implode(" ", $words);

                    return $firstword . str_repeat($pad_string, $padding_needed) . $words;

                default:
                    $lastword = $words[count($words) - 1];
                    unset($words[count($words) - 1]);
                    $words = implode(" ", $words);

                    return $words . str_repeat($pad_string, $padding_needed) . $lastword;
            }

        } else {
            // Comportamiento normal de str_pad()
            return str_pad($input, $length, $pad_string, $pad_type);
        }
    }

    function parseNumber($value)
    {
        $cleanValue = trim($value);

        // Patrón mejorado para números grandes
        $patron = '/^
            (\d{1,3}(?:[ \.,]?\d{3})*)  # Parte entera con separadores
            ([,.]\d{1,20})?              # Parte decimal opcional (hasta 20 dígitos)
        $/xu';

        if (preg_match($patron, $cleanValue, $matches)) {
            // Procesar parte entera
            $entero = str_replace([' ', '.', ','], '', $matches[1]);

            // Procesar parte decimal
            $decimal = '0';
            if (isset($matches[2])) {
                $decimal = str_replace(['.', ','], '', substr($matches[2], 1));
            }

            // Combinar y convertir a float (precaución con números muy grandes)
            $numeroString = "$entero.$decimal";

            // Para números muy grandes, mejor devolver como string
            if (strlen($entero) > 15) {
                return $numeroString; // Evitar pérdida de precisión en float
            }

            return (float) $numeroString;
        }

        return null;
    }

}
