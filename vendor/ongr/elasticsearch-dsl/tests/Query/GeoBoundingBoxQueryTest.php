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

use ONGR\ElasticsearchDSL\Query\GeoBoundingBoxQuery;

class GeoBoundingBoxQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if exception is thrown when geo points are not set.
     *
     * @expectedException \LogicException
     */
    public function testGeoBoundBoxQueryException()
    {
        $query = new GeoBoundingBoxQuery('location', []);
        $query->toArray();
    }

    /**
     * Data provider for testToArray().
     *
     * @return array
     */
    public function getArrayDataProvider()
    {
        return [
            // Case #1 (2 values).
            [
                'location',
                [
                    ['lat' => 40.73, 'lon' => -74.1],
                    ['lat' => 40.01, 'lon' => -71.12],
                ],
                ['parameter' => 'value'],
                [
                    'location' => [
                        'top_left' => ['lat' => 40.73, 'lon' => -74.1],
                        'bottom_right' => ['lat' => 40.01, 'lon' => -71.12],
                    ],
                    'parameter' => 'value',
                ],
            ],
            // Case #2 (4 values).
            [
                'location',
                [40.73, -74.1, 40.01, -71.12],
                ['parameter' => 'value'],
                [
                    'location' => [
                        'top' => 40.73,
                        'left' => -74.1,
                        'bottom' => 40.01,
                        'right' => -71.12,
                    ],
                    'parameter' => 'value',
                ],
            ],
        ];
    }

    /**
     * Tests toArray method.
     *
     * @param string $field      Field name.
     * @param array  $values     Bounding box values.
     * @param array  $parameters Optional parameters.
     * @param array  $expected   Expected result.
     *
     * @dataProvider getArrayDataProvider
     */
    public function testToArray($field, $values, $parameters, $expected)
    {
        $query = new GeoBoundingBoxQuery($field, $values, $parameters);
        $result = $query->toArray();
        $this->assertEquals(['geo_bounding_box' => $expected], $result);
    }
}
