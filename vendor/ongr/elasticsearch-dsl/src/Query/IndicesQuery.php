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

/**
 * Represents Elasticsearch "indices" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-indices-query.html
 */
class IndicesQuery implements BuilderInterface
{
    /**
     * @var string[]
     */
    private $indices;

    /**
     * @var BuilderInterface
     */
    private $query;

    /**
     * @var string|BuilderInterface
     */
    private $noMatchQuery;

    /**
     * @param string[]         $indices
     * @param BuilderInterface $query
     * @param BuilderInterface $noMatchQuery
     */
    public function __construct($indices, $query, $noMatchQuery = null)
    {
        $this->indices = $indices;
        $this->query = $query;
        $this->noMatchQuery = $noMatchQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'indices';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        if (count($this->indices) > 1) {
            $output = ['indices' => $this->indices];
        } else {
            $output = ['index' => $this->indices[0]];
        }

        $output['query'] = $this->query->toArray();

        if ($this->noMatchQuery !== null) {
            if (is_a($this->noMatchQuery, 'ONGR\ElasticsearchDSL\BuilderInterface')) {
                $output['no_match_query'] = $this->noMatchQuery->toArray();
            } else {
                $output['no_match_query'] = $this->noMatchQuery;
            }
        }

        return [$this->getType() => $output];
    }
}
