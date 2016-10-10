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

use ONGR\ElasticsearchDSL\Query\RangeQuery;

class RangeQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toArray().
     */
    public function testToArray()
    {
        $query = new RangeQuery('age', ['gte' => 10, 'lte' => 20]);
        $expected = [
            'range' => [
                'age' => [
                    'gte' => 10,
                    'lte' => 20,
                ],
            ]
        ];

        $this->assertEquals($expected, $query->toArray());
    }
}
