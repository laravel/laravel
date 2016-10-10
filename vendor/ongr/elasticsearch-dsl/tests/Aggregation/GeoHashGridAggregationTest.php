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

use ONGR\ElasticsearchDSL\Aggregation\GeoHashGridAggregation;

/**
 * Unit test for geohash grid aggregation.
 */
class GeoHashGridAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if exception is thrown.
     *
     * @expectedException \LogicException
     */
    public function testGeoHashGridAggregationException()
    {
        $agg = new GeoHashGridAggregation('test_agg');
        $agg->getArray();
    }

    /**
     * Data provider for testGeoHashGridAggregationGetArray().
     *
     * @return array
     */
    public function getArrayDataProvider()
    {
        $out = [];

        $filterData = [
            'field' => 'location',
            'precision' => 3,
            'size' => 10,
            'shard_size' => 10,
        ];

        $expectedResults = [
            'field' => 'location',
            'precision' => 3,
            'size' => 10,
            'shard_size' => 10,
        ];

        $out[] = [$filterData, $expectedResults];

        return $out;
    }

    /**
     * Tests getArray method.
     *
     * @param array $filterData
     * @param array $expected
     *
     * @dataProvider getArrayDataProvider
     */
    public function testGeoHashGridAggregationGetArray($filterData, $expected)
    {
        $aggregation = new GeoHashGridAggregation('foo');
        $aggregation->setPrecision($filterData['precision']);
        $aggregation->setSize($filterData['size']);
        $aggregation->setShardSize($filterData['shard_size']);
        $aggregation->setField($filterData['field']);

        $result = $aggregation->getArray();
        $this->assertEquals($result, $expected);
    }

    /**
     * Tests getType method.
     */
    public function testGeoHashGridAggregationGetType()
    {
        $aggregation = new GeoHashGridAggregation('foo');
        $result = $aggregation->getType();
        $this->assertEquals('geohash_grid', $result);
    }
}
