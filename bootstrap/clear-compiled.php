<?php

@unlink(__DIR__.'/cache/compiled.php');
@unlink(__DIR__.'/cache/services.json'); // Laravel 5.1
@unlink(__DIR__.'/cache/services.php'); // Laravel 5.2
