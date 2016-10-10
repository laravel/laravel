<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Unit\DSL;

use ONGR\ElasticsearchDSL\BuilderBag;
use ONGR\ElasticsearchDSL\BuilderInterface;

class BuilderBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests if bag knows if he has a builder.
     */
    public function testHas()
    {
        $bag = new BuilderBag();
        $fooBuilder = $this->getBuilder('foo');
        $builderName = $bag->add($fooBuilder);
        $this->assertTrue($bag->has($builderName));
    }

    /**
     * Tests if bag can remove a builder.
     */
    public function testRemove()
    {
        $bag = new BuilderBag();
        $fooBuilder = $this->getBuilder('foo');
        $acmeBuilder = $this->getBuilder('acme');
        $fooBuilderName = $bag->add($fooBuilder);
        $acmeBuilderName = $bag->add($acmeBuilder);

        $bag->remove($fooBuilderName);

        $this->assertFalse($bag->has($fooBuilderName), 'Foo builder should not exist anymore.');
        $this->assertTrue($bag->has($acmeBuilderName), 'Acme builder should exist.');
        $this->assertCount(1, $bag->all());
    }

    /**
     * Tests if bag can clear it's builders.
     */
    public function testClear()
    {
        $bag = new BuilderBag(
            [
                $this->getBuilder('foo'),
                $this->getBuilder('baz'),
            ]
        );

        $bag->clear();

        $this->assertEmpty($bag->all());
    }

    /**
     * Tests if bag can get a builder.
     */
    public function testGet()
    {
        $bag = new BuilderBag();
        $bazBuilder = $this->getBuilder('baz');
        $builderName = $bag->add($bazBuilder);

        $this->assertNotEmpty($bag->get($builderName));
    }

    /**
     * Returns builder.
     *
     * @param string $name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|BuilderInterface
     */
    private function getBuilder($name)
    {
        $friendlyBuilderMock = $this->getMock('ONGR\ElasticsearchDSL\BuilderInterface');

        $friendlyBuilderMock
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        $friendlyBuilderMock
            ->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue([]));

        return $friendlyBuilderMock;
    }
}
