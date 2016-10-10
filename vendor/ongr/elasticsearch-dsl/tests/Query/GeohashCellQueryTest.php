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

use ONGR\ElasticsearchDSL\Query\GeohashCellQuery;

class GeohashCellQueryTest extends \PHPUnit_Framework_TestCase
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
                ['lat' => 40, 'lon' => -70],
                [],
                ['location' => ['lat' => 40, 'lon' => -70]],
            ],
            // Case #2.
            [
                'location',
                ['lat' => 0, 'lon' => 0],
                ['parameter' => 'value'],
                ['location' => ['lat' => 0, 'lon' => 0], 'parameter' => 'value'],
            ],
        ];
    }

    /**
     * Tests toArray() method.
     *
     * @param string $field      Field name.
     * @param array  $location   Location.
     * @param array  $parameters Optional parameters.
     * @param array  $expected   Expected result.
     *
     * @dataProvider getArrayDataProvider
     */
    public function testToArray($field, $location, $parameters, $expected)
    {
        $query = new GeohashCellQuery($field, $location, $parameters);
        $result = $query->toArray();
        $this->assertEquals(['geohash_cell' => $expected], $result);
    }
}
