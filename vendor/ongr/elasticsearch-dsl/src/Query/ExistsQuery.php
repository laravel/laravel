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
 * Represents Elasticsearch "exists" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html
 */
class ExistsQuery implements BuilderInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * Constructor.
     *
     * @param string $field Field value
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'exists';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            $this->getType() => [
                'field' => $this->field,
            ],
        ];
    }
}
