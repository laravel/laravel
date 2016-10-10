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
 * Class representing ip range aggregation.
 */
class Ipv4RangeAggregation extends AbstractAggregation
{
    use BucketingTrait;

    /**
     * @var array
     */
    private $ranges = [];

    /**
     * Inner aggregations container init.
     *
     * @param string $name
     * @param string $field
     * @param array  $ranges
     */
    public function __construct($name, $field = null, $ranges = [])
    {
        parent::__construct($name);

        $this->setField($field);
        foreach ($ranges as $range) {
            if (is_array($range)) {
                $from = isset($range['from']) ? $range['from'] : null;
                $to = isset($range['to']) ? $range['to'] : null;
                $this->addRange($from, $to);
            } else {
                $this->addMask($range);
            }
        }
    }

    /**
     * Add range to aggregation.
     *
     * @param string|null $from
     * @param string|null $to
     *
     * @return Ipv4RangeAggregation
     */
    public function addRange($from = null, $to = null)
    {
        $range = array_filter(
            [
                'from' => $from,
                'to' => $to,
            ]
        );

        $this->ranges[] = $range;

        return $this;
    }

    /**
     * Add ip mask to aggregation.
     *
     * @param string $mask
     *
     * @return Ipv4RangeAggregation
     */
    public function addMask($mask)
    {
        $this->ranges[] = ['mask' => $mask];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'ip_range';
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        if ($this->getField() && !empty($this->ranges)) {
            return [
                'field' => $this->getField(),
                'ranges' => array_values($this->ranges),
            ];
        }
        throw new \LogicException('Ip range aggregation must have field set and range added.');
    }
}
