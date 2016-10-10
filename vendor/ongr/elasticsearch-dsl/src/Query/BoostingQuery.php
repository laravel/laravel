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
 * Represents Elasticsearch "boosting" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-boosting-query.html
 */
class BoostingQuery implements BuilderInterface
{
    /**
     * @var BuilderInterface
     */
    private $positive;

    /**
     * @var BuilderInterface
     */
    private $negative;

    /**
     * @var int|float
     */
    private $negativeBoost;

    /**
     * @param BuilderInterface $positive
     * @param BuilderInterface $negative
     * @param int|float        $negativeBoost
     */
    public function __construct(BuilderInterface $positive, BuilderInterface $negative, $negativeBoost)
    {
        $this->positive = $positive;
        $this->negative = $negative;
        $this->negativeBoost = $negativeBoost;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'boosting';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = [
            'positive' => $this->positive->toArray(),
            'negative' => $this->negative->toArray(),
            'negative_boost' => $this->negativeBoost,
        ];

        return [$this->getType() => $query];
    }
}
