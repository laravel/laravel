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
 * Represents Elasticsearch "regexp" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html
 */
class RegexpQuery implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var string Field to be queried.
     */
    private $field;

    /**
     * @var string The actual regexp value to be used.
     */
    private $regexpValue;

    /**
     * @param string $field
     * @param string $regexpValue
     * @param array  $parameters
     */
    public function __construct($field, $regexpValue, array $parameters = [])
    {
        $this->field = $field;
        $this->regexpValue = $regexpValue;
        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'regexp';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = [
            'value' => $this->regexpValue,
        ];

        $output = [
            $this->field => $this->processArray($query),
        ];

        return [$this->getType() => $output];
    }
}
