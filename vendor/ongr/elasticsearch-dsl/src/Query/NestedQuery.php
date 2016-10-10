<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Query;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\ParametersTrait;

/**
 * Represents Elasticsearch "nested" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html
 */
class NestedQuery implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var string
     */
    private $path;

    /**
     * @var BuilderInterface
     */
    private $query;

    /**
     * @param string           $path
     * @param BuilderInterface $query
     * @param array            $parameters
     */
    public function __construct($path, BuilderInterface $query, array $parameters = [])
    {
        $this->path = $path;
        $this->query = $query;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'nested';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            $this->getType() => $this->processArray(
                [
                    'path' => $this->path,
                    'query' => $this->query->toArray(),
                ]
            )
        ];
    }
}
