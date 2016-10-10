<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Aggregation\Integration;

use ONGR\ElasticsearchDSL\Aggregation\FiltersAggregation;
use ONGR\ElasticsearchDSL\Aggregation\HistogramAggregation;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use PHPUnit_Framework_TestCase;

/**
 * Tests integration of examples from the documentation.
 */
class SearchTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests integration of the FiltersAggregation named example from the documentation.
     *
     * @link https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Filters.md#named-example
     */
    public function testFiltersAggregationNamedExample()
    {
        $errorTermFilter = new TermQuery('body', 'error');
        $warningTermFilter = new TermQuery('body', 'warning');

        $histogramAggregation = new HistogramAggregation('monthly', 'timestamp');
        $histogramAggregation->setInterval('1M');

        $filterAggregation = new FiltersAggregation(
            'grades_stats',
            [
                'error' => $errorTermFilter,
                'warning' => $warningTermFilter,
            ]
        );
        $filterAggregation->addAggregation($histogramAggregation);

        $search = new Search();
        $search->addAggregation($filterAggregation);

        $this->assertSame(
            [
                'aggregations' => [
                    'grades_stats' => [
                        'filters' => [
                            'filters' => [
                                'error' => [
                                    'term' => [
                                        'body' => 'error',
                                    ],
                                ],
                                'warning' => [
                                    'term' => [
                                        'body' => 'warning',
                                    ],
                                ],
                            ],
                        ],
                        'aggregations' => [
                            'monthly' => [
                                'histogram' => [
                                    'field' => 'timestamp',
                                    'interval' => '1M',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $search->toArray()
        );
    }

    /**
     * Tests integration of the FiltersAggregation anonymous example from the documentation.
     *
     * @link https://github.com/ongr-io/ElasticsearchDSL/blob/master/docs/Aggregation/Filters.md#anonymous-example
     */
    public function testFiltersAggregationAnonymousExample()
    {
        $errorTermFilter = new TermQuery('body', 'error');
        $warningTermFilter = new TermQuery('body', 'warning');

        $histogramAggregation = new HistogramAggregation('monthly', 'timestamp');
        $histogramAggregation->setInterval('1M');

        $filterAggregation = new FiltersAggregation(
            'grades_stats',
            [
                'error' => $errorTermFilter,
                'warning' => $warningTermFilter,
            ],
            true
        );
        $filterAggregation->addAggregation($histogramAggregation);

        $search = new Search();
        $search->addAggregation($filterAggregation);

        $this->assertSame(
            [
                'aggregations' => [
                    'grades_stats' => [
                        'filters' => [
                            'filters' => [
                                [
                                    'term' => [
                                        'body' => 'error',
                                    ],
                                ],
                                [
                                    'term' => [
                                        'body' => 'warning',
                                    ],
                                ],
                            ],
                        ],
                        'aggregations' => [
                            'monthly' => [
                                'histogram' => [
                                    'field' => 'timestamp',
                                    'interval' => '1M',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $search->toArray()
        );
    }
}
