<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Fragment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\EventListener\FragmentListener;

/**
 * Adds the possibility to generate a fragment URI for a given Controller.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class RoutableFragmentRenderer implements FragmentRendererInterface
{
    /**
     * @internal
     */
    protected string $fragmentPath = '/_fragment';

    /**
     * Sets the fragment path that triggers the fragment listener.
     *
     * @see FragmentListener
     */
    public function setFragmentPath(string $path): void
    {
        $this->fragmentPath = $path;
    }

    /**
     * Generates a fragment URI for a given controller.
     *
     * @param bool $absolute Whether to generate an absolute URL or not
     * @param bool $strict   Whether to allow non-scalar attributes or not
     */
    protected function generateFragmentUri(ControllerReference $reference, Request $request, bool $absolute = false, bool $strict = true): string
    {
        return (new FragmentUriGenerator($this->fragmentPath))->generate($reference, $request, $absolute, $strict, false);
    }
}
