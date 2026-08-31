<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\DirectoryAwareLoaderInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Routing\Exception\InvalidArgumentException;
use Symfony\Component\Routing\RouteCollection;

/**
 * A loader that discovers controller classes in a directory that follows PSR-4.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 */
final class Psr4DirectoryLoader extends Loader implements DirectoryAwareLoaderInterface
{
    private ?string $currentDirectory = null;

    public function __construct(
        private readonly FileLocatorInterface $locator,
    ) {
        // PSR-4 directory loader has no env-aware logic, so we drop the $env constructor parameter.
        parent::__construct();
    }

    /**
     * @param array{path: string, namespace: string} $resource
     */
    public function load(mixed $resource, ?string $type = null): ?RouteCollection
    {
        $excluded = $resource['_excluded'] ?? [];
        $path = $this->locator->locate($resource['path'], $this->currentDirectory);
        if (!is_dir($path)) {
            return new RouteCollection();
        }

        if (!preg_match('/^(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+\\\)++$/', trim($resource['namespace'], '\\').'\\')) {
            throw new InvalidArgumentException(\sprintf('Namespace "%s" is not a valid PSR-4 prefix.', $resource['namespace']));
        }

        return $this->loadFromDirectory($path, trim($resource['namespace'], '\\'), $excluded);
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'attribute' === $type && \is_array($resource) && isset($resource['path'], $resource['namespace']);
    }

    public function forDirectory(string $currentDirectory): static
    {
        $loader = clone $this;
        $loader->currentDirectory = $currentDirectory;

        return $loader;
    }

    private function loadFromDirectory(string $directory, string $psr4Prefix, array $excluded = []): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->addResource(new DirectoryResource($directory, '/\.php$/'));
        $files = iterator_to_array(new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS),
                static fn (\SplFileInfo $current) => !str_starts_with($current->getBasename(), '.')
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        ));
        usort($files, static fn (\SplFileInfo $a, \SplFileInfo $b) => (string) $a > (string) $b ? 1 : -1);

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $normalizedPath = rtrim(str_replace('\\', '/', $file->getPathname()), '/');
            if (isset($excluded[$normalizedPath])) {
                continue;
            }

            if ($file->isDir()) {
                $collection->addCollection($this->loadFromDirectory($file->getPathname(), $psr4Prefix.'\\'.$file->getFilename(), $excluded));

                continue;
            }
            if ('php' !== $file->getExtension() || !class_exists($className = $psr4Prefix.'\\'.$file->getBasename('.php')) || (new \ReflectionClass($className))->isAbstract()) {
                continue;
            }

            $collection->addCollection($this->import($className, 'attribute'));
        }

        return $collection;
    }
}
