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

use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;

/**
 * Unit test for Bool.
 */
class BoolQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for addToBool() without setting a correct bool operator.
     *
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage The bool operator acme is not supported
     */
    public function testBoolAddToBoolException()
    {
        $bool = new BoolQuery();
        $bool->add(new MatchAllQuery(), 'acme');
    }

    /**
     * Tests toArray() method.
     */
    public function testBoolToArray()
    {
        $bool = new BoolQuery();
        $bool->add(new TermQuery('key1', 'value1'), BoolQuery::SHOULD);
        $bool->add(new TermQuery('key2', 'value2'), BoolQuery::MUST);
        $bool->add(new TermQuery('key3', 'value3'), BoolQuery::MUST_NOT);
        $expected = [
            'bool' => [
                'should' => [
                    [
                        'term' => [
                            'key1' => 'value1',
                        ],
                    ],
                ],
                'must' => [
                    [
                        'term' => [
                            'key2' => 'value2',
                        ],
                    ],
                ],
                'must_not' => [
                    [
                        'term' => [
                            'key3' => 'value3',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $bool->toArray());
    }

    /**
     * Tests bool query in filter context.
     */
    public function testBoolInFilterContext()
    {
        $bool = new BoolQuery();
        $bool->add(new TermQuery('key1', 'value1'), BoolQuery::FILTER);
        $bool->add(new TermQuery('key2', 'value2'), BoolQuery::MUST);
        $expected = [
            'bool' => [
                'filter' => [
                    [
                        'term' => [
                            'key1' => 'value1',
                        ],
                    ],
                ],
                'must' => [
                    [
                        'term' => [
                            'key2' => 'value2',
                        ],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $bool->toArray());
    }

    /**
     * Test if simplified structure is returned in case single MUST query given.
     */
    public function testSingleMust()
    {
        $bool = new BoolQuery();
        $bool->add(new TermQuery('key2', 'value2'), BoolQuery::MUST);
        $expected = [
            'term' => [
                'key2' => 'value2',
            ],
        ];
        $this->assertEquals($expected, $bool->toArray());
    }
}
