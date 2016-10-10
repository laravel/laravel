<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Fixtures;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class SiblingHolder
{
    private $sibling0;
    private $sibling1;
    private $sibling2;

    public function __construct()
    {
        $sibling = new Sibling();

        $this->sibling0 = $sibling;
        $this->sibling1 = $sibling;
        $this->sibling2 = $sibling;
    }

    public function getSibling0()
    {
        return $this->sibling0;
    }

    public function getSibling1()
    {
        return $this->sibling1;
    }

    public function getSibling2()
    {
        return $this->sibling2;
    }
}

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class Sibling
{
    public function getCoopTilleuls()
    {
        return 'Les-Tilleuls.coop';
    }
}
