<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Config;

use Symfony\Component\Config\FileLocator as BaseFileLocator;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * FileLocator uses the KernelInterface to locate resources in bundles.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileLocator extends BaseFileLocator
{
    public function __construct(
        private KernelInterface $kernel,
    ) {
        parent::__construct();
    }

    public function locate(string $file, ?string $currentPath = null, bool $first = true): string|array
    {
        if (isset($file[0]) && '@' === $file[0]) {
            $resource = $this->kernel->locateResource($file);

            return $first ? $resource : [$resource];
        }

        return parent::locate($file, $currentPath, $first);
    }
}
