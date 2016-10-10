<?php
    $transform = $argv[1];
    $source_path = $argv[2];
    $destination_path = $argv[3];
    $dir = opendir($source_path);
    while (($file = readdir($dir)) !== false) {
        if (! preg_match('/\.xml$/', $file)) {
            continue;
        }
        $source = $source_path . $file;
        $destination = $destination_path .
                preg_replace('/\.xml$/', '.php', basename($source));
        $command = "xsltproc $transform $source > $destination\n";
        `$command`;
    }
    closedir($dir);
?>