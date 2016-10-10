<?php
/**
 *    @package JAMA
 *
 *    Pythagorean Theorem:
 *
 *    a = 3
 *    b = 4
 *    r = sqrt(square(a) + square(b))
 *    r = 5
 *
 *    r = sqrt(a^2 + b^2) without under/overflow.
 */
function hypo($a, $b)
{
    if (abs($a) > abs($b)) {
        $r = $b / $a;
        $r = abs($a) * sqrt(1 + $r * $r);
    } elseif ($b != 0) {
        $r = $a / $b;
        $r = abs($b) * sqrt(1 + $r * $r);
    } else {
        $r = 0.0;
    }
    return $r;
}    //    function hypo()


/**
 *    Mike Bommarito's version.
 *    Compute n-dimensional hyotheneuse.
 *
function hypot() {
    $s = 0;
    foreach (func_get_args() as $d) {
        if (is_numeric($d)) {
            $s += pow($d, 2);
        } else {
            throw new PHPExcel_Calculation_Exception(JAMAError(ARGUMENT_TYPE_EXCEPTION));
        }
    }
    return sqrt($s);
}
*/
