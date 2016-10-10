#!/usr/bin/env php
<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// -------------------------------------------------------------------------- //
// In order to be able to execute this script to create a Phar archive of Predis,
// the Phar module must be loaded and the "phar.readonly" directive php.ini must
// be set to "off". You can change the values in the $options array to customize
// the creation of the Phar archive to better suit your needs.
// -------------------------------------------------------------------------- //

$options = array(
    'name'           => 'predis',
    'project_path'   => __DIR__ . '/../lib/',
    'compression'    => Phar::NONE,
    'append_version' => true,
);

function getPharFilename($options)
{
    $filename = $options['name'];

    // NOTE: do not consider "append_version" with Phar compression do to a bug in
    // Phar::compress() when renaming phar archives containing dots in their name.
    if ($options['append_version'] && $options['compression'] === Phar::NONE) {
        $versionFile = @fopen(__DIR__ . '/../VERSION', 'r');

        if ($versionFile === false) {
            throw new Exception("Could not locate the VERSION file.");
        }

        $version = trim(fgets($versionFile));
        fclose($versionFile);
        $filename .= "_$version";
    }

    return "$filename.phar";
}

function getPharStub($options)
{
    return <<<EOSTUB
<?php
Phar::mapPhar('predis.phar');
spl_autoload_register(function (\$class) {
    if (strpos(\$class, 'Predis\\\\') === 0) {
        \$file = 'phar://predis.phar/'.strtr(\$class, '\\\', '/').'.php';
        if (file_exists(\$file)) {
            require \$file;
            return true;
        }
    }
});
__HALT_COMPILER();
EOSTUB;
}

// -------------------------------------------------------------------------- //

$phar = new Phar(getPharFilename($options));
$phar->compress($options['compression']);
$phar->setStub(getPharStub($options));
$phar->buildFromDirectory($options['project_path']);
