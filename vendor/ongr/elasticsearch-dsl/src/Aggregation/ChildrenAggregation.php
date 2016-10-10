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

/**
 * Class representing ChildrenAggregation.
 */
class ChildrenAggregation extends AbstractAggregation
{
    use BucketingTrait;

    /**
     * @var string
     */
    private $children;

    /**
     * Return children.
     *
     * @return string
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param string $name
     * @param string $children
     */
    public function __construct($name, $children = null)
    {
        parent::__construct($name);

        $this->setChildren($children);
    }

    /**
     * Sets children.
     *
     * @param string $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'children';
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        if (count($this->getAggregations()) == 0) {
            throw new \LogicException("Children aggregation `{$this->getName()}` has no aggregations added");
        }

        return ['type' => $this->getChildren()];
    }
}
