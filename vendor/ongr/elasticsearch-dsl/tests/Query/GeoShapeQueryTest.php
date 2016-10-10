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

use ONGR\ElasticsearchDSL\Query\GeoShapeQuery;

class GeoShapeQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toArray() method.
     */
    public function testToArray()
    {
        $filter = new GeoShapeQuery(['param1' => 'value1']);
        $filter->addShape('location', 'envelope', [[13, 53], [14, 52]]);

        $expected = [
            'geo_shape' => [
                'location' => [
                    'shape' => [
                        'type' => 'envelope',
                        'coordinates' => [[13, 53], [14, 52]],
                    ],
                ],
                'param1' => 'value1',
            ],
        ];

        $this->assertEquals($expected, $filter->toArray());
    }

    /**
     * Test for toArray() in case of pre-indexed shape.
     */
    public function testToArrayIndexed()
    {
        $filter = new GeoShapeQuery(['param1' => 'value1']);
        $filter->addPreIndexedShape('location', 'DEU', 'countries', 'shapes', 'location');

        $expected = [
            'geo_shape' => [
                'location' => [
                    'indexed_shape' => [
                        'id' => 'DEU',
                        'type' => 'countries',
                        'index' => 'shapes',
                        'path' => 'location',
                    ],
                ],
                'param1' => 'value1',
            ],
        ];

        $this->assertEquals($expected, $filter->toArray());
    }
}
