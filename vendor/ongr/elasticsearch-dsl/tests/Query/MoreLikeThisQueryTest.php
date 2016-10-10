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

use ONGR\ElasticsearchDSL\Query\MoreLikeThisQuery;

class MoreLikeThisQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toArray().
     */
    public function testToArray()
    {
        $query = new MoreLikeThisQuery('this is a test', ['fields' => ['title', 'description']]);
        $expected = [
            'more_like_this' => [
                'fields' => ['title', 'description'],
                'like' => 'this is a test',
            ],
        ];

        $this->assertEquals($expected, $query->toArray());
    }
}
