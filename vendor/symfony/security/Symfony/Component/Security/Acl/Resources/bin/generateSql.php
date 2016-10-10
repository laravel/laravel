<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../../../../ClassLoader/ClassLoader.php';

use Symfony\Component\ClassLoader\ClassLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Security\Acl\Dbal\Schema;

$loader = new ClassLoader();
$loader->addPrefixes(array(
    'Symfony'                    => __DIR__.'/../../../../../..',
    'Doctrine\\Common'           => __DIR__.'/../../../../../../../vendor/doctrine-common/lib',
    'Doctrine\\DBAL\\Migrations' => __DIR__.'/../../../../../../../vendor/doctrine-migrations/lib',
    'Doctrine\\DBAL'             => __DIR__.'/../../../../../../../vendor/doctrine-dbal/lib',
    'Doctrine'                   => __DIR__.'/../../../../../../../vendor/doctrine/lib',
));
$loader->register();

$schema = new Schema(array(
    'class_table_name'         => 'acl_classes',
    'entry_table_name'         => 'acl_entries',
    'oid_table_name'           => 'acl_object_identities',
    'oid_ancestors_table_name' => 'acl_object_identity_ancestors',
    'sid_table_name'           => 'acl_security_identities',
));

$reflection = new ReflectionClass('Doctrine\\DBAL\\Platforms\\AbstractPlatform');
$finder = new Finder();
$finder->name('*Platform.php')->in(dirname($reflection->getFileName()));
foreach ($finder as $file) {
    require_once $file->getPathName();
    $className = 'Doctrine\\DBAL\\Platforms\\'.$file->getBasename('.php');

    $reflection = new ReflectionClass($className);
    if ($reflection->isAbstract()) {
        continue;
    }

    $platform = $reflection->newInstance();
    $targetFile = sprintf(__DIR__.'/../schema/%s.sql', $platform->name);
    file_put_contents($targetFile, implode("\n\n", $schema->toSql($platform)));
}
