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

use ONGR\ElasticsearchDSL\Query\IndicesQuery;

class IndicesQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testToArray().
     *
     * @return array
     */
    public function getTestToArrayData()
    {
        $mock = $this->getMock('ONGR\ElasticsearchDSL\BuilderInterface');
        $mock
            ->expects($this->any())
            ->method('toArray')
            ->willReturn(['term' => ['foo' => 'bar']]);

        return [
            [
                $mock,
                $mock,
                [
                    'indices' => ['foo', 'bar'],
                    'query' => ['term' => ['foo' => 'bar']],
                    'no_match_query' => ['term' => ['foo' => 'bar']],
                ]
            ],
            [
                $mock,
                'all',
                [
                    'indices' => ['foo', 'bar'],
                    'query' => ['term' => ['foo' => 'bar']],
                    'no_match_query' => 'all',
                ]
            ],
            [
                $mock,
                null,
                [
                    'indices' => ['foo', 'bar'],
                    'query' => ['term' => ['foo' => 'bar']],
                ]
            ],
        ];
    }

    /**
     * Tests toArray().
     *
     * @param $query
     * @param $noMatchQuery
     * @param $expected
     *
     * @dataProvider getTestToArrayData()
     */
    public function testToArray($query, $noMatchQuery, $expected)
    {
        $query = new IndicesQuery(['foo', 'bar'], $query, $noMatchQuery);
        $this->assertEquals(['indices' => $expected], $query->toArray());
    }
}
