<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Aggregation;

use ONGR\ElasticsearchDSL\Aggregation\ChildrenAggregation;

/**
 * Unit test for children aggregation.
 */
class ChildrenAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests if ChildrenAggregation#getArray throws exception when expected.
     *
     * @expectedException \LogicException
     */
    public function testGetArrayException()
    {
        $aggregation = new ChildrenAggregation('foo');
        $aggregation->getArray();
    }

    /**
     * Tests getType method.
     */
    public function testChildrenAggregationGetType()
    {
        $aggregation = new ChildrenAggregation('foo');
        $result = $aggregation->getType();
        $this->assertEquals('children', $result);
    }

    /**
     * Tests getArray method.
     */
    public function testChildrenAggregationGetArray()
    {
        $mock = $this->getMockBuilder('ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $aggregation = new ChildrenAggregation('foo');
        $aggregation->addAggregation($mock);
        $aggregation->setChildren('question');
        $result = $aggregation->getArray();
        $expected = ['type' => 'question'];
        $this->assertEquals($expected, $result);
    }
}
