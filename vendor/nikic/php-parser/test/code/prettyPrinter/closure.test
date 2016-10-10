Closures
-----
<?php

$closureWithArgs = function ($arg1, $arg2) {
    $comment = 'closure body';
};

$closureWithArgsAndVars = function ($arg1, $arg2) use($var1, $var2) {
    $comment = 'closure body';
};
-----
$closureWithArgs = function ($arg1, $arg2) {
    $comment = 'closure body';
};
$closureWithArgsAndVars = function ($arg1, $arg2) use($var1, $var2) {
    $comment = 'closure body';
};