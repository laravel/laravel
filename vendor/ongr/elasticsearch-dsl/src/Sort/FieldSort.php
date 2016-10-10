<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Sort;

use ONGR\ElasticsearchDSL\BuilderInterface;

/**
 * Holds all the values required for basic sorting.
 */
class FieldSort implements BuilderInterface
{
    const ASC = 'asc';
    const DESC = 'desc';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $order;

    /**
     * @var array
     */
    private $params;

    /**
     * @var BuilderInterface
     */
    private $nestedFilter;

    /**
     * @param string $field  Field name.
     * @param string $order  Order direction.
     * @param array  $params Params that can be set to field sort.
     */
    public function __construct($field, $order = null, $params = [])
    {
        $this->field = $field;
        $this->order = $order;
        $this->params = $params;
    }

    /**
     * @return BuilderInterface
     */
    public function getNestedFilter()
    {
        return $this->nestedFilter;
    }

    /**
     * @param BuilderInterface $nestedFilter
     */
    public function setNestedFilter(BuilderInterface $nestedFilter)
    {
        $this->nestedFilter = $nestedFilter;
    }

    /**
     * Returns element type.
     *
     * @return string
     */
    public function getType()
    {
        return 'sort';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        if ($this->order) {
            $this->params['order'] = $this->order;
        }

        if ($this->nestedFilter) {
            $fieldValues = array_merge(
                $this->params,
                [
                    'nested_filter' => $this->nestedFilter->toArray(),
                ]
            );
        } else {
            $fieldValues = $this->params;
        }

        $output = [
            $this->field => empty($fieldValues) ? new \stdClass() : $fieldValues,
        ];

        return $output;
    }
}
