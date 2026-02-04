<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../');
$it = new RecursiveIteratorIterator($dir);
$errors = [];
foreach ($it as $f) {
    if (!$f->isFile()) continue;
    if (strtolower($f->getExtension()) !== 'php') continue;
    $path = $f->getPathname();
    $cmd = 'php -l ' . escapeshellarg($path) . ' 2>&1';
    $output = [];
    $ret = 0;
    exec($cmd, $output, $ret);
    if ($ret !== 0) {
        $errors[$path] = implode("\n", $output);
    }
}
if (empty($errors)) {
    echo "No syntax errors found\n";
    exit(0);
}
foreach ($errors as $p => $msg) {
    echo "== FILE: $p ==\n";
    echo $msg . "\n\n";
}
exit(1);
