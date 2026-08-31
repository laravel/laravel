<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

/**
 * Allow adding classes to the class cache.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal since Symfony 7.1, to be deprecated in 8.1; use Symfony\Component\DependencyInjection\Extension\Extension instead
 */
abstract class Extension extends BaseExtension
{
    private array $annotatedClasses = [];

    /**
     * Gets the annotated classes to cache.
     *
     * @return string[]
     *
     * @deprecated since Symfony 7.1, to be removed in 8.0
     */
    public function getAnnotatedClassesToCompile(): array
    {
        trigger_deprecation('symfony/http-kernel', '7.1', 'The "%s()" method is deprecated since Symfony 7.1 and will be removed in 8.0.', __METHOD__);

        return $this->annotatedClasses;
    }

    /**
     * Adds annotated classes to the class cache.
     *
     * @param string[] $annotatedClasses An array of class patterns
     *
     * @deprecated since Symfony 7.1, to be removed in 8.0
     */
    public function addAnnotatedClassesToCompile(array $annotatedClasses): void
    {
        trigger_deprecation('symfony/http-kernel', '7.1', 'The "%s()" method is deprecated since Symfony 7.1 and will be removed in 8.0.', __METHOD__);

        $this->annotatedClasses = array_merge($this->annotatedClasses, $annotatedClasses);
    }
}
