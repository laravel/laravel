<?php

namespace Faker\Provider\pt_BR;

/**
 * Calculates one MOD 11 check digit based on customary Brazilian algorithms.
 *
 * @see http://en.wikipedia.org/wiki/Check_digit
 * @see http://pt.wikipedia.org/wiki/CNPJ#Algoritmo_de_Valida.C3.A7.C3.A3o
 * @see http://en.wikipedia.org/wiki/Cadastro_de_Pessoas_F%C3%ADsicas#Validation
 *
 * @param int|string $numbers Numbers on which generate the check digit
 *
 * @return int
 */
function check_digit($numbers)
{
    $numbers = (string) $numbers;
    $length = strlen($numbers);
    $second_algorithm = $length >= 12;
    $verifier = 0;

    for ($i = 1; $i <= $length; ++$i) {
        if (!$second_algorithm) {
            $multiplier = $i + 1;
        } else {
            $multiplier = ($i >= 9) ? $i - 7 : $i + 1;
        }
        $verifier += $numbers[$length - $i] * $multiplier;
    }

    $verifier = 11 - ($verifier % 11);

    if ($verifier >= 10) {
        $verifier = 0;
    }

    return $verifier;
}
