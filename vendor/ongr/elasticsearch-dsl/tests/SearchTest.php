<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Unit\DSL;

use ONGR\ElasticsearchDSL\Query\MissingQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use ONGR\ElasticsearchDSL\Suggest\TermSuggest;

/**
 * Test for Search.
 */
class SearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Search constructor.
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf('ONGR\ElasticsearchDSL\Search', new Search());
    }

    /**
     * Data provider for test testSettingParams()
     *
     * @return array
     */
    public function getTestSettingParamsData()
    {
        $cases = [];

        $search = new Search();
        $search->setSize(3);
        $cases['Only size is set'] = [
            $search,
            [
                'size' => 3,
            ],
        ];

        $search = new Search();
        $search->setFrom(4);
        $cases['Only from is set'] = [
            $search,
            [
                'from' => 4,
            ],
        ];

        $search = new Search();
        $search->setTimeout('2s');
        $cases['Only timeout is set'] = [
            $search,
            [
                'timeout' => '2s',
            ],
        ];

        $search = new Search();
        $search->setTerminateAfter(100);
        $cases['Only terminate_after is set'] = [
            $search,
            [
                'terminate_after' => 100,
            ],
        ];

        $search = new Search();
        $search->setSize(3);
        $search->setFrom(4);
        $search->setTimeout('2s');
        $search->setTerminateAfter(100);
        $cases['Multiple parameters are set'] = [
            $search,
            [
                'size' => 3,
                'from' => 4,
                'timeout' => '2s',
                'terminate_after' => 100,
            ],
        ];

        return $cases;
    }

    /**
     * This test checks if parameters are correctly set into Search object.
     *
     * @dataProvider getTestSettingParamsData()
     *
     * @param Search    $search
     * @param array     $expected
     */
    public function testSettingParams($search, $expected)
    {
        $this->assertEquals(
            $expected,
            $search->toArray()
        );
    }

    /**
     * Data provider for test testSettingQueryParams()
     *
     * @return array
     */
    public function getTestSettingQueryParamsData()
    {
        $cases = [];

        $search = new Search();
        $search->setSearchType('dfs_query_then_fetch');
        $cases['Only search_type is set'] = [
            $search,
            [
                'search_type' => 'dfs_query_then_fetch',
            ],
        ];

        $search = new Search();
        $search->setRequestCache(true);
        $cases['Only request_cache is set'] = [
            $search,
            [
                'request_cache' => true,
            ],
        ];

        $search = new Search();
        $search->setScroll('1m');
        $cases['Only scroll is set'] = [
            $search,
            [
                'scroll' => '1m',
            ],
        ];

        $search = new Search();
        $search->setPreference('_local');
        $cases['Only preference is set'] = [
            $search,
            [
                'preference' => '_local',
            ],
        ];

        $search = new Search();
        $search->setSearchType('dfs_query_then_fetch');
        $search->setRequestCache(true);
        $search->setScroll('1m');
        $search->setPreference('_local');
        $cases['Multiple parameters are set'] = [
            $search,
            [
                'search_type' => 'dfs_query_then_fetch',
                'request_cache' => true,
                'scroll' => '1m',
                'preference' => '_local',
            ],
        ];

        return $cases;
    }

    /**
     * Test if query params are constructed correctly.
     *
     * @dataProvider getTestSettingQueryParamsData()
     *
     * @param Search    $search
     * @param array     $expected
     */
    public function testSettingQueryParams($search, $expected)
    {
        $this->assertEquals(
            $expected,
            $search->getQueryParams()
        );
    }

    /**
     * Data provider for testToArray().
     *
     * @return array
     */
    public function getTestToArrayData()
    {
        $cases = [];

        $cases['empty_search'] = [
            [],
            new Search(),
        ];

        $cases['single_term_query'] = [
            [
                'query' => [
                    'term' => ['foo' => 'bar'],
                ],
            ],
            (new Search())->addQuery(new TermQuery('foo', 'bar')),
        ];

        $cases['single_term_filter'] = [
            [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'term' => ['foo' => 'bar'],
                            ],
                        ],
                    ],
                ],
            ],
            (new Search())->addFilter(new TermQuery('foo', 'bar')),
        ];

        $cases['single_query_query_and_filter'] = [
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['foo' => 'bar']],
                        ],
                        'filter' => [
                            [
                                'missing' => ['field' => 'baz'],
                            ],
                        ],
                    ],
                ],
            ],
            (new Search())->addQuery(new TermQuery('foo', 'bar'))->addFilter(new MissingQuery('baz')),
        ];

        $cases['sort_by_price'] = [
            [
                'sort' => [
                    [
                        'price' => [
                            'order' => 'asc',
                        ],
                    ],
                ],
            ],
            (new Search())->addSort(new FieldSort('price', 'asc')),
        ];

        $cases['single_suggest'] = [
            [
                'suggest' => [
                    'foo' => [
                        'text' => 'bar',
                        'term' => ['field' => 'title', 'size' => 2],
                    ],
                ],
            ],
            (new Search())->addSuggest(new TermSuggest('foo', 'bar', ['field' => 'title', 'size' => 2])),
        ];

        $cases['multiple_suggests'] = [
            [
                'suggest' => [
                    'foo' => [
                        'text' => 'bar',
                        'term' => ['field' => 'title', 'size' => 2],
                    ],
                    'bar' => [
                        'text' => 'foo',
                        'term' => ['field' => 'title', 'size' => 2],
                    ],
                ],
            ],
            (new Search())
                ->addSuggest(new TermSuggest('foo', 'bar', ['field' => 'title', 'size' => 2]))
                ->addSuggest(new TermSuggest('bar', 'foo', ['field' => 'title', 'size' => 2])),
        ];

        return $cases;
    }

    /**
     * @param array  $expected
     * @param Search $search
     *
     * @dataProvider getTestToArrayData()
     */
    public function testToArray($expected, $search)
    {
        $this->assertEquals($expected, $search->toArray());

        // Double check
        $this->assertEquals($expected, $search->toArray());
    }
}
