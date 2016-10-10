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
 * Represents Elasticsearch "limit" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-limit-query.html
 */
class LimitQuery implements BuilderInterface
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value Number of documents (per shard) to execute on
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'limit';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            $this->getType() => [
                'value' => $this->value,
            ],
        ];
    }
}
