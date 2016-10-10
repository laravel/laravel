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

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class GroupDummy extends GroupDummyParent implements GroupDummyInterface
{
    /**
     * @Groups({"a"})
     */
    private $foo;
    /**
     * @Groups({"b", "c", "name_converter"})
     */
    protected $bar;
    private $fooBar;
    private $symfony;

    /**
     * @Groups({"b"})
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    /**
     * @Groups({"c"})
     */
    public function getBar()
    {
        return $this->bar;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFooBar($fooBar)
    {
        $this->fooBar = $fooBar;
    }

    /**
     * @Groups({"a", "b", "name_converter"})
     */
    public function isFooBar()
    {
        return $this->fooBar;
    }

    public function setSymfony($symfony)
    {
        $this->symfony = $symfony;
    }

    public function getSymfony()
    {
        return $this->symfony;
    }
}
