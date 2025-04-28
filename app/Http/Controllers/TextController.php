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
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
        ]);
        // Eliminar espacios adicionales
        $text = trim(preg_replace('/\s+/', ' ', $text));

        return $text;
    }

    public function calculateSimilarityPercentage($text1, $text2)
    {
        // Normalizar los textos
        $text1 = $this->standardize($text1);
        $text2 = $this->standardize($text2);

        // Calcular la distance Levenshtein entre los textos
        $distance = levenshtein($text1, $text2);

        // Encontrar la longitud máxima entre ambos textos
        $max_length = max(strlen($text1), strlen($text2));

        // Si la longitud máxima es 0 (ambos textos vacíos), son 100% similares
        if ($max_length == 0) {
            return 100;
        }

        // Calcular el porcentaje de similitud basado en la distance Levenshtein
        $percentage = (1 - $distance / $max_length) * 100;

        return $percentage;
    }
}
