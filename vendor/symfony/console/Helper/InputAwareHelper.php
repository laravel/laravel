<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * An implementation of InputAwareInterface for Helpers.
 *
 * @author Wouter J <waldio.webdesign@gmail.com>
 */
abstract class InputAwareHelper extends Helper implements InputAwareInterface
{
    protected InputInterface $input;

    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }
}
