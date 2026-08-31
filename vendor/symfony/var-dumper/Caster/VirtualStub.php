<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Caster;

class VirtualStub extends ConstStub
{
    public function __construct(\ReflectionProperty $property)
    {
        parent::__construct('~'.($property->hasType() ? ' '.$property->getType() : ''), 'Virtual property');
        $this->attr['virtual'] = true;
    }
}
