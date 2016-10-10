<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Query\Span;

use ONGR\ElasticsearchDSL\Query\TermQuery;

/**
 * Elasticsearch span_term query class.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-term-query.html
 */
class SpanTermQuery extends TermQuery implements SpanQueryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'span_term';
    }
}
