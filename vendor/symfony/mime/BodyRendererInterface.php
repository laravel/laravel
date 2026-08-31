<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface BodyRendererInterface
{
    public function render(Message $message): void;
}
