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

use ONGR\ElasticsearchDSL\Aggregation\Type\BucketingTrait;
use ONGR\ElasticsearchDSL\BuilderInterface;

/**
 * Class representing FilterAggregation.
 */
class FilterAggregation extends AbstractAggregation
{
    use BucketingTrait;

    /**
     * @var BuilderInterface
     */
    protected $filter;

    /**
     * Inner aggregations container init.
     *
     * @param string           $name
     * @param BuilderInterface $filter
     */
    public function __construct($name, BuilderInterface $filter = null)
    {
        parent::__construct($name);

        if ($filter !== null) {
            $this->setFilter($filter);
        }
    }

    /**
     * Sets a filter.
     *
     * @param BuilderInterface $filter
     */
    public function setFilter(BuilderInterface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function setField($field)
    {
        throw new \LogicException("Filter aggregation, doesn't support `field` parameter");
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        if (!$this->filter) {
            throw new \LogicException("Filter aggregation `{$this->getName()}` has no filter added");
        }

        return $this->filter->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'filter';
    }
}
