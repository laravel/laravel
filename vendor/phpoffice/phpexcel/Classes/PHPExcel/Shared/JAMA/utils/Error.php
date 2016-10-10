<?php
/**
 *    @package JAMA
 *
 *    Error handling
 *    @author Michael Bommarito
 *    @version 01292005
 */

//Language constant
define('JAMALANG', 'EN');


//All errors may be defined by the following format:
//define('ExceptionName', N);
//$error['lang'][ExceptionName] = 'Error message';
$error = array();

/*
I've used Babelfish and a little poor knowledge of Romance/Germanic languages for the translations here.
Feel free to correct anything that looks amiss to you.
*/

define('POLYMORPHIC_ARGUMENT_EXCEPTION', -1);
$error['EN'][POLYMORPHIC_ARGUMENT_EXCEPTION] = "Invalid argument pattern for polymorphic function.";
$error['FR'][POLYMORPHIC_ARGUMENT_EXCEPTION] = "Modèle inadmissible d'argument pour la fonction polymorphe.".
$error['DE'][POLYMORPHIC_ARGUMENT_EXCEPTION] = "Unzulässiges Argumentmuster für polymorphe Funktion.";

define('ARGUMENT_TYPE_EXCEPTION', -2);
$error['EN'][ARGUMENT_TYPE_EXCEPTION] = "Invalid argument type.";
$error['FR'][ARGUMENT_TYPE_EXCEPTION] = "Type inadmissible d'argument.";
$error['DE'][ARGUMENT_TYPE_EXCEPTION] = "Unzulässige Argumentart.";

define('ARGUMENT_BOUNDS_EXCEPTION', -3);
$error['EN'][ARGUMENT_BOUNDS_EXCEPTION] = "Invalid argument range.";
$error['FR'][ARGUMENT_BOUNDS_EXCEPTION] = "Gamme inadmissible d'argument.";
$error['DE'][ARGUMENT_BOUNDS_EXCEPTION] = "Unzulässige Argumentstrecke.";

define('MATRIX_DIMENSION_EXCEPTION', -4);
$error['EN'][MATRIX_DIMENSION_EXCEPTION] = "Matrix dimensions are not equal.";
$error['FR'][MATRIX_DIMENSION_EXCEPTION] = "Les dimensions de Matrix ne sont pas égales.";
$error['DE'][MATRIX_DIMENSION_EXCEPTION] = "Matrixmaße sind nicht gleich.";

define('PRECISION_LOSS_EXCEPTION', -5);
$error['EN'][PRECISION_LOSS_EXCEPTION] = "Significant precision loss detected.";
$error['FR'][PRECISION_LOSS_EXCEPTION] = "Perte significative de précision détectée.";
$error['DE'][PRECISION_LOSS_EXCEPTION] = "Bedeutender Präzision Verlust ermittelte.";

define('MATRIX_SPD_EXCEPTION', -6);
$error['EN'][MATRIX_SPD_EXCEPTION] = "Can only perform operation on symmetric positive definite matrix.";
$error['FR'][MATRIX_SPD_EXCEPTION] = "Perte significative de précision détectée.";
$error['DE'][MATRIX_SPD_EXCEPTION] = "Bedeutender Präzision Verlust ermittelte.";

define('MATRIX_SINGULAR_EXCEPTION', -7);
$error['EN'][MATRIX_SINGULAR_EXCEPTION] = "Can only perform operation on singular matrix.";

define('MATRIX_RANK_EXCEPTION', -8);
$error['EN'][MATRIX_RANK_EXCEPTION] = "Can only perform operation on full-rank matrix.";

define('ARRAY_LENGTH_EXCEPTION', -9);
$error['EN'][ARRAY_LENGTH_EXCEPTION] = "Array length must be a multiple of m.";

define('ROW_LENGTH_EXCEPTION', -10);
$error['EN'][ROW_LENGTH_EXCEPTION] = "All rows must have the same length.";

/**
 *    Custom error handler
 *    @param int $num Error number
 */
function JAMAError($errorNumber = null)
{
    global $error;

    if (isset($errorNumber)) {
        if (isset($error[JAMALANG][$errorNumber])) {
            return $error[JAMALANG][$errorNumber];
        } else {
            return $error['EN'][$errorNumber];
        }
    } else {
        return ("Invalid argument to JAMAError()");
    }
}
