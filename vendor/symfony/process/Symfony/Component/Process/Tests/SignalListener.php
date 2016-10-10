<?php

// required for signal handling
declare (ticks = 1);

pcntl_signal(SIGUSR1, function () {echo "Caught SIGUSR1"; exit;});

$n = 0;

// ticks require activity to work - sleep(4); does not work
while ($n < 400) {
    usleep(10000);
    $n++;
}

return;
