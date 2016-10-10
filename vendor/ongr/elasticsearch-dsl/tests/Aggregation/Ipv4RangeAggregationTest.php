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

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Ipv4RangeAggregation;

class Ipv4RangeAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test exception when field and range are not set.
     *
     * @expectedException \LogicException
     */
    public function testIfExceptionIsThrownWhenFieldAndRangeAreNotSet()
    {
        $agg = new Ipv4RangeAggregation('foo');
        $agg->toArray();
    }

    /**
     * Tests if field and range  can be passed to constructor.
     */
    public function testConstructorFilter()
    {
        $aggregation = new Ipv4RangeAggregation('test', 'fieldName', [['from' => 'fromValue']]);
        $this->assertSame(
            [
                'ip_range' => [
                    'field' => 'fieldName',
                    'ranges' => [['from' => 'fromValue']],
                ],
            ],
            $aggregation->toArray()
        );

        $aggregation = new Ipv4RangeAggregation('test', 'fieldName', ['maskValue']);
        $this->assertSame(
            [
                'ip_range' => [
                    'field' => 'fieldName',
                    'ranges' => [['mask' => 'maskValue']],
                ],
            ],
            $aggregation->toArray()
        );
    }
}
