<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Attribute;

/**
 * This class is meant to be used in {@see Route} to define an alias for a route.
 */
class DeprecatedAlias
{
    public function __construct(
        public readonly string $aliasName,
        public readonly string $package,
        public readonly string $version,
        public readonly string $message = '',
    ) {
    }

    #[\Deprecated('Use the "message" property instead', 'symfony/routing:7.4')]
    public function getMessage(): string
    {
        return $this->message;
    }

    #[\Deprecated('Use the "aliasName" property instead', 'symfony/routing:7.4')]
    public function getAliasName(): string
    {
        return $this->aliasName;
    }

    #[\Deprecated('Use the "package" property instead', 'symfony/routing:7.4')]
    public function getPackage(): string
    {
        return $this->package;
    }

    #[\Deprecated('Use the "version" property instead', 'symfony/routing:7.4')]
    public function getVersion(): string
    {
        return $this->version;
    }
}
