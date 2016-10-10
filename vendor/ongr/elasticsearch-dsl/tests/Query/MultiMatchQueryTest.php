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

use ONGR\ElasticsearchDSL\Query\MultiMatchQuery;

class MultiMatchQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toArray().
     */
    public function testToArray()
    {
        $query = new MultiMatchQuery(['message', 'title'], 'this is a test');
        $expected = [
            'multi_match' => [
                'query' => 'this is a test',
                'fields' => ['message', 'title'],
            ],
        ];

        $this->assertEquals($expected, $query->toArray());
    }
}
