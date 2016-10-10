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

use ONGR\ElasticsearchDSL\Aggregation\GeoDistanceAggregation;

class GeoDistanceAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if exception is thrown when field is not set.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Geo distance aggregation must have a field set.
     */
    public function testGeoDistanceAggregationExceptionWhenFieldIsNotSet()
    {
        $agg = new GeoDistanceAggregation('test_agg');
        $agg->setOrigin('50, 70');
        $agg->getArray();
    }

    /**
     * Test if exception is thrown when origin is not set.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Geo distance aggregation must have an origin set.
     */
    public function testGeoDistanceAggregationExceptionWhenOriginIsNotSet()
    {
        $agg = new GeoDistanceAggregation('test_agg');
        $agg->setField('location');
        $agg->getArray();
    }

    /**
     * Test if exception is thrown when field is not set.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Either from or to must be set. Both cannot be null.
     */
    public function testGeoDistanceAggregationAddRangeException()
    {
        $agg = new GeoDistanceAggregation('test_agg');
        $agg->addRange();
    }

    /**
     * Data provider for testGeoDistanceAggregationGetArray().
     *
     * @return array
     */
    public function testGeoDistanceAggregationGetArrayDataProvider()
    {
        $out = [];
        $filterData = [
            'field' => 'location',
            'origin' => '52.3760, 4.894',
            'unit' => 'mi',
            'distance_type' => 'plane',
            'ranges' => [100, 300],
        ];

        $expectedResults = [
            'field' => 'location',
            'origin' => '52.3760, 4.894',
            'unit' => 'mi',
            'distance_type' => 'plane',
            'ranges' => [['from' => 100, 'to' => 300]],
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
     * @dataProvider testGeoDistanceAggregationGetArrayDataProvider
     */
    public function testGeoDistanceAggregationGetArray($filterData, $expected)
    {
        $aggregation = new GeoDistanceAggregation('foo');
        $aggregation->setOrigin($filterData['origin']);
        $aggregation->setField($filterData['field']);
        $aggregation->setUnit($filterData['unit']);
        $aggregation->setDistanceType($filterData['distance_type']);
        $aggregation->addRange($filterData['ranges'][0], $filterData['ranges'][1]);

        $result = $aggregation->getArray();
        $this->assertEquals($result, $expected);
    }

    /**
     * Tests getType method.
     */
    public function testGeoDistanceAggregationGetType()
    {
        $aggregation = new GeoDistanceAggregation('foo');
        $result = $aggregation->getType();
        $this->assertEquals('geo_distance', $result);
    }

    /**
     * Tests if parameters can be passed to constructor.
     */
    public function testConstructorFilter()
    {
        $aggregation = new GeoDistanceAggregation(
            'test',
            'fieldName',
            'originValue',
            [
                ['from' => 'value'],
                ['to' => 'value'],
                ['from' => 'value', 'to' => 'value2'],
            ],
            'unitValue',
            'distanceTypeValue'
        );

        $this->assertSame(
            [
                'geo_distance' => [
                    'field' => 'fieldName',
                    'origin' => 'originValue',
                    'unit' => 'unitValue',
                    'distance_type' => 'distanceTypeValue',
                    'ranges' => [
                        ['from' => 'value'],
                        ['to' => 'value'],
                        ['from' => 'value', 'to' => 'value2'],
                    ],
                ],
            ],
            $aggregation->toArray()
        );
    }
}
