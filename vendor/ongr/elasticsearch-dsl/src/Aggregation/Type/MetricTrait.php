<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Aggregation\Type;

/**
 * Trait used by Aggregations which do not support nesting.
 */
trait MetricTrait
{
    /**
     * Metric aggregations does not support nesting.
     *
     * @return bool
     */
    protected function supportsNesting()
    {
        return false;
    }
}
