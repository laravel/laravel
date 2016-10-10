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
use ONGR\ElasticsearchDSL\Aggregation\GeoBoundsAggregation;

/**
 * Unit test for geo bounds aggregation.
 */
class GeoBoundsAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if exception is thrown.
     *
     * @expectedException \LogicException
     */
    public function testGeoBoundsAggregationException()
    {
        $agg = new GeoBoundsAggregation('test_agg');
        $agg->getArray();
    }

    /**
     * Tests getType method.
     */
    public function testGeoBoundsAggregationGetType()
    {
        $agg = new GeoBoundsAggregation('foo');
        $result = $agg->getType();
        $this->assertEquals('geo_bounds', $result);
    }

    /**
     * Tests getArray method.
     */
    public function testGeoBoundsAggregationGetArray()
    {
        $agg = new GeoBoundsAggregation('foo');
        $agg->setField('bar');
        $agg->setWrapLongitude(true);
        $result = [
            'geo_bounds' => [
                'field' => 'bar',
                'wrap_longitude' => true,
            ],
        ];
        $this->assertEquals($result, $agg->toArray(), 'when wraplongitude is true');

        $agg->setWrapLongitude(false);
        $result = [
            'geo_bounds' => [
                'field' => 'bar',
                'wrap_longitude' => false,
            ],
        ];
        $this->assertEquals($result, $agg->toArray(), 'when wraplongitude is false');
    }
}
