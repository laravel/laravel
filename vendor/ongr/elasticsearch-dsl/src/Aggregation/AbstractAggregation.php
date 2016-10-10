<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Aggregation;

use ONGR\ElasticsearchDSL\BuilderBag;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\NameAwareTrait;
use ONGR\ElasticsearchDSL\ParametersTrait;

/**
 * AbstractAggregation class.
 */
abstract class AbstractAggregation implements BuilderInterface
{
    use ParametersTrait;
    use NameAwareTrait;

    /**
     * @var string
     */
    private $field;

    /**
     * @var BuilderBag
     */
    private $aggregations;

    /**
     * Abstract supportsNesting method.
     *
     * @return bool
     */
    abstract protected function supportsNesting();

    /**
     * @return array|\stdClass
     */
    abstract protected function getArray();

    /**
     * Inner aggregations container init.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Adds a sub-aggregation.
     *
     * @param AbstractAggregation $abstractAggregation
     */
    public function addAggregation(AbstractAggregation $abstractAggregation)
    {
        if (!$this->aggregations) {
            $this->aggregations = $this->createBuilderBag();
        }

        $this->aggregations->add($abstractAggregation);
    }

    /**
     * Returns all sub aggregations.
     *
     * @return BuilderBag[]
     */
    public function getAggregations()
    {
        if ($this->aggregations) {
            return $this->aggregations->all();
        } else {
            return [];
        }
    }

    /**
     * Returns sub aggregation.
     * @param string $name Aggregation name to return.
     *
     * @return AbstractAggregation|null
     */
    public function getAggregation($name)
    {
        if ($this->aggregations && $this->aggregations->has($name)) {
            return $this->aggregations->get($name);
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $array = $this->getArray();
        $result = [
            $this->getType() => is_array($array) ? $this->processArray($array) : $array,
        ];

        if ($this->supportsNesting()) {
            $nestedResult = $this->collectNestedAggregations();

            if (!empty($nestedResult)) {
                $result['aggregations'] = $nestedResult;
            }
        }

        return $result;
    }

    /**
     * Process all nested aggregations.
     *
     * @return array
     */
    protected function collectNestedAggregations()
    {
        $result = [];
        /** @var AbstractAggregation $aggregation */
        foreach ($this->getAggregations() as $aggregation) {
            $result[$aggregation->getName()] = $aggregation->toArray();
        }

        return $result;
    }

    /**
     * Creates BuilderBag new instance.
     *
     * @return BuilderBag
     */
    private function createBuilderBag()
    {
        return new BuilderBag();
    }
}
