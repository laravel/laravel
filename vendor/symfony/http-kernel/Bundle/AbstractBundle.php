<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Bundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * A Bundle that provides configuration hooks.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
abstract class AbstractBundle extends Bundle implements ConfigurableExtensionInterface
{
    protected string $extensionAlias = '';

    public function configure(DefinitionConfigurator $definition): void
    {
    }

    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
    }

    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if ('' === $this->extensionAlias) {
            $this->extensionAlias = Container::underscore(preg_replace('/Bundle$/', '', $this->getName()));
        }

        return $this->extension ??= new BundleExtension($this, $this->extensionAlias);
    }

    public function getPath(): string
    {
        if (!isset($this->path)) {
            $reflected = new \ReflectionObject($this);
            // assume the modern directory structure by default
            $this->path = \dirname($reflected->getFileName(), 2);
        }

        return $this->path;
    }
}
