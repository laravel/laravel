<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Aggregation;

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;

class TermsAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setField method.
     */
    public function testTermsAggregationSetField()
    {
        // Case #0 terms aggregation.
        $aggregation = new TermsAggregation('test_agg');
        $aggregation->setField('test_field');

        $result = [
            'terms' => ['field' => 'test_field'],
        ];

        $this->assertEquals($aggregation->toArray(), $result);
    }

    /**
     * Tests setSize method.
     */
    public function testTermsAggregationSetSize()
    {
        // Case #1 terms aggregation with size.
        $aggregation = new TermsAggregation('test_agg');
        $aggregation->setField('test_field');
        $aggregation->addParameter('size', 1);

        $result = [
            'terms' => [
                'field' => 'test_field',
                'size' => 1,

            ],
        ];

        $this->assertEquals($aggregation->toArray(), $result);

        // Case #2 terms aggregation with zero size.
        $aggregation = new TermsAggregation('test_agg');
        $aggregation->setField('test_field');
        $aggregation->addParameter('size', 0);

        $result = [
            'terms' => [
                'field' => 'test_field',
                'size' => 0,
            ],
        ];

        $this->assertEquals($aggregation->toArray(), $result);
    }

    /**
     * Tests minDocumentCount method.
     */
    public function testTermsAggregationMinDocumentCount()
    {
        // Case #3 terms aggregation with size and min document count.
        $aggregation = new TermsAggregation('test_agg');
        $aggregation->setField('test_field');
        $aggregation->addParameter('size', 1);
        $aggregation->addParameter('min_doc_count', 10);

        $result = [
            'terms' => [
                'field' => 'test_field',
                'size' => 1,
                'min_doc_count' => 10,
            ],
        ];

        $this->assertEquals($aggregation->toArray(), $result);
    }

    /**
     * Tests include, exclude method.
     */
    public function testTermsAggregationSimpleIncludeExclude()
    {
        // Case #4 terms aggregation with simple include, exclude.
        $aggregation = new TermsAggregation('test_agg');
        $aggregation->setField('test_field');
        $aggregation->addParameter('include', 'test_.*');
        $aggregation->addParameter('exclude', 'pizza_.*');

        $result = [
            'terms' => [
                'field' => 'test_field',
                'include' => 'test_.*',
                'exclude' => 'pizza_.*',
            ],
        ];

        $this->assertEquals($aggregation->toArray(), $result);
    }

    /**
     * Tests include, exclude with flags method.
     */
    public function testTermsAggregationIncludeExcludeFlags()
    {
        // Case #5 terms aggregation with include, exclude and flags.
        $aggregation = new TermsAggregation('test_agg');
        $aggregation->setField('test_field');
        $aggregation->addParameter(
            'include',
            [
                'pattern' => 'test_.*',
                'flags' => 'CANON_EQ|CASE_INSENSITIVE',
            ]
        );
        $aggregation->addParameter(
            'exclude',
            [
                'pattern' => 'pizza_.*',
                'flags' => 'CASE_INSENSITIVE',
            ]
        );

        $result = [
            'terms' => [
                'field' => 'test_field',
                'include' => [
                    'pattern' => 'test_.*',
                    'flags' => 'CANON_EQ|CASE_INSENSITIVE',
                ],
                'exclude' => [
                    'pattern' => 'pizza_.*',
                    'flags' => 'CASE_INSENSITIVE',
                ],
            ],
        ];

        $this->assertEquals($aggregation->toArray(), $result);
    }

    /**
     * Tests setOrder method.
     */
    public function testTermsAggregationSetOrder()
    {
        // Case #6 terms aggregation with order default direction.
        $aggregation = new TermsAggregation('test_agg');
        $aggregation->setField('test_field');
        $aggregation->addParameter('order', ['_count' => 'asc']);

        $result = [
            'terms' => [
                'field' => 'test_field',
                'order' => ['_count' => 'asc'],
            ],
        ];

        $this->assertEquals($aggregation->toArray(), $result);
    }

    /**
     * Tests setOrder DESC method.
     */
    public function testTermsAggregationSetOrderDESC()
    {
        // Case #7 terms aggregation with order term mode, desc direction.
        $aggregation = new TermsAggregation('test_agg');
        $aggregation->setField('test_field');
        $aggregation->addParameter('order', ['_term' => 'desc']);

        $result = [
            'terms' => [
                'field' => 'test_field',
                'order' => ['_term' => 'desc'],
            ],
        ];

        $this->assertEquals($aggregation->toArray(), $result);
    }

    /**
     * Tests getType method.
     */
    public function testTermsAggregationGetType()
    {
        $aggregation = new TermsAggregation('foo');
        $result = $aggregation->getType();
        $this->assertEquals('terms', $result);
    }
}
