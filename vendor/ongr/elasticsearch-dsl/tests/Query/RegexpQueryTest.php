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

use ONGR\ElasticsearchDSL\Query\RegexpQuery;

class RegexpQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toArray().
     */
    public function testToArray()
    {
        $query = new RegexpQuery('user', 's.*y');
        $expected = [
            'regexp' => [
                'user' => [
                    'value' => 's.*y',
                ],
            ],
        ];

        $this->assertEquals($expected, $query->toArray());
    }
}
