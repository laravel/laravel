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
 * Represents Elasticsearch "function_score" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
 */
class FunctionScoreQuery implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var BuilderInterface
     */
    private $query;

    /**
     * @var array[]
     */
    private $functions;

    /**
     * @param BuilderInterface $query
     * @param array            $parameters
     */
    public function __construct(BuilderInterface $query, array $parameters = [])
    {
        $this->query = $query;
        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'function_score';
    }

    /**
     * Modifier to apply query to the function score function.
     *
     * @param array            $function
     * @param BuilderInterface $query
     */
    private function applyQuery(array &$function, BuilderInterface $query = null)
    {
        if ($query) {
            $function['query'] = $query->toArray();
        }
    }

    /**
     * Creates field_value_factor function.
     *
     * @param string           $field
     * @param float            $factor
     * @param string           $modifier
     * @param BuilderInterface $query
     *
     * @return $this
     */
    public function addFieldValueFactorFunction($field, $factor, $modifier = 'none', BuilderInterface $query = null)
    {
        $function = [
            'field_value_factor' => [
                'field' => $field,
                'factor' => $factor,
                'modifier' => $modifier,
            ],
        ];

        $this->applyQuery($function, $query);

        $this->functions[] = $function;

        return $this;
    }

    /**
     * Add decay function to function score. Weight and query are optional.
     *
     * @param string           $type
     * @param string           $field
     * @param array            $function
     * @param array            $options
     * @param BuilderInterface $query
     *
     * @return $this
     */
    public function addDecayFunction(
        $type,
        $field,
        array $function,
        array $options = [],
        BuilderInterface $query = null
    ) {
        $function = [
            $type => array_merge(
                [$field => $function],
                $options
            ),
        ];

        $this->applyQuery($function, $query);

        $this->functions[] = $function;

        return $this;
    }

    /**
     * Adds function to function score without decay function. Influence search score only for specific query.
     *
     * @param float            $weight
     * @param BuilderInterface $query
     *
     * @return $this
     */
    public function addWeightFunction($weight, BuilderInterface $query = null)
    {
        $function = [
            'weight' => $weight,
        ];

        $this->applyQuery($function, $query);

        $this->functions[] = $function;

        return $this;
    }

    /**
     * Adds random score function. Seed is optional.
     *
     * @param mixed            $seed
     * @param BuilderInterface $query
     *
     * @return $this
     */
    public function addRandomFunction($seed = null, BuilderInterface $query = null)
    {
        $function = [
            'random_score' => $seed ? [ 'seed' => $seed ] : new \stdClass(),
        ];

        $this->applyQuery($function, $query);

        $this->functions[] = $function;

        return $this;
    }

    /**
     * Adds script score function.
     *
     * @param string           $script
     * @param array            $params
     * @param array            $options
     * @param BuilderInterface $query
     *
     * @return $this
     */
    public function addScriptScoreFunction(
        $script,
        array $params = [],
        array $options = [],
        BuilderInterface $query = null
    ) {
        $function = [
            'script_score' => array_merge(
                [
                    'script' => $script,
                    'params' => $params,
                ],
                $options
            ),
        ];

        $this->applyQuery($function, $query);

        $this->functions[] = $function;

        return $this;
    }

    /**
     * Adds custom simple function. You can add to the array whatever you want.
     *
     * @param array $function
     *
     * @return $this
     */
    public function addSimpleFunction(array $function)
    {
        $this->functions[] = $function;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = [
            'query' => $this->query->toArray(),
            'functions' => $this->functions,
        ];

        $output = $this->processArray($query);

        return [$this->getType() => $output];
    }
}
