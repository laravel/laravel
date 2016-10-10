<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Query;

use ONGR\ElasticsearchDSL\Query\GeoDistanceQuery;

class GeoDistanceQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testToArray().
     *
     * @return array
     */
    public function getArrayDataProvider()
    {
        return [
            // Case #1.
            [
                'location',
                '200km',
                ['lat' => 40, 'lon' => -70],
                [],
                ['distance' => '200km', 'location' => ['lat' => 40, 'lon' => -70]],
            ],
            // Case #2.
            [
                'location',
                '20km',
                ['lat' => 0, 'lon' => 0],
                ['parameter' => 'value'],
                ['distance' => '20km', 'location' => ['lat' => 0, 'lon' => 0], 'parameter' => 'value'],
            ],
        ];
    }

    /**
     * Tests toArray() method.
     *
     * @param string $field      Field name.
     * @param string $distance   Distance.
     * @param array  $location   Location.
     * @param array  $parameters Optional parameters.
     * @param array  $expected   Expected result.
     *
     * @dataProvider getArrayDataProvider
     */
    public function testToArray($field, $distance, $location, $parameters, $expected)
    {
        $query = new GeoDistanceQuery($field, $distance, $location, $parameters);
        $result = $query->toArray();
        $this->assertEquals(['geo_distance' => $expected], $result);
    }
}
