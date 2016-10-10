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

use ONGR\ElasticsearchDSL\ParametersTrait;

/**
 * Elasticsearch Span not query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-not-query.html
 *
 * @todo Add options support
 */
class SpanNotQuery implements SpanQueryInterface
{
    use ParametersTrait;

    /**
     * @var SpanQueryInterface
     */
    private $include;

    /**
     * @var SpanQueryInterface
     */
    private $exclude;

    /**
     * @param SpanQueryInterface $include
     * @param SpanQueryInterface $exclude
     * @param array              $parameters
     */
    public function __construct(SpanQueryInterface $include, SpanQueryInterface $exclude, array $parameters = [])
    {
        $this->include = $include;
        $this->exclude = $exclude;
        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'span_not';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = [
            'include' => $this->include->toArray(),
            'exclude' => $this->exclude->toArray(),
        ];

        return [$this->getType() => $query];
    }
}
