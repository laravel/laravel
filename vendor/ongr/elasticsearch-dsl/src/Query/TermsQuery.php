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
 * Represents Elasticsearch "terms" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html
 */
class TermsQuery implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $terms;

    /**
     * Constructor.
     *
     * @param string $field      Field name
     * @param array  $terms      An array of terms
     * @param array  $parameters Optional parameters
     */
    public function __construct($field, $terms, array $parameters = [])
    {
        $this->field = $field;
        $this->terms = $terms;
        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'terms';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = [
            $this->field => $this->terms,
        ];

        $output = $this->processArray($query);

        return [$this->getType() => $output];
    }
}
