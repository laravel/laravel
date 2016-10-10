<?php

define('ERR_SELECT_FAILED', 1);
define('ERR_TIMEOUT', 2);
define('ERR_READ_FAILED', 3);
define('ERR_WRITE_FAILED', 4);

$read = array(STDIN);
$write = array(STDOUT, STDERR);

stream_set_blocking(STDIN, 0);
stream_set_blocking(STDOUT, 0);
stream_set_blocking(STDERR, 0);

$out = $err = '';
while ($read || $write) {
    $r = $read;
    $w = $write;
    $e = null;
    $n = stream_select($r, $w, $e, 5);

    if (false === $n) {
        die(ERR_SELECT_FAILED);
    } elseif ($n < 1) {
        die(ERR_TIMEOUT);
    }

    if (in_array(STDOUT, $w) && strlen($out) > 0) {
        $written = fwrite(STDOUT, (binary) $out, 32768);
        if (false === $written) {
            die(ERR_WRITE_FAILED);
        }
        $out = (binary) substr($out, $written);
    }
    if (null === $read && strlen($out) < 1) {
        $write = array_diff($write, array(STDOUT));
    }

    if (in_array(STDERR, $w) && strlen($err) > 0) {
        $written = fwrite(STDERR, (binary) $err, 32768);
        if (false === $written) {
            die(ERR_WRITE_FAILED);
        }
        $err = (binary) substr($err, $written);
    }
    if (null === $read && strlen($err) < 1) {
        $write = array_diff($write, array(STDERR));
    }

    if ($r) {
        $str = fread(STDIN, 32768);
        if (false !== $str) {
            $out .= $str;
            $err .= $str;
        }
        if (false === $str || feof(STDIN)) {
            $read = null;
            if (!feof(STDIN)) {
                die(ERR_READ_FAILED);
            }
        }
    }
}
