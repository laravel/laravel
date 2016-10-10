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
 * Represents Elasticsearch "missing" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-missing-query.html
 */
class MissingQuery implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var string
     */
    private $field;

    /**
     * Constructor.
     *
     * @param string $field      Field name
     * @param array  $parameters Optional parameters
     */
    public function __construct($field, array $parameters = [])
    {
        $this->field = $field;
        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'missing';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = ['field' => $this->field];
        $output = $this->processArray($query);

        return [$this->getType() => $output];
    }
}
