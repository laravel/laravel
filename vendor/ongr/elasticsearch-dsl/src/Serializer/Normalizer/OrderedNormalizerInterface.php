<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Serializer\Normalizer;

/**
 * This should be implemented by normalizable object that required to be processed in specific order.
 */
interface OrderedNormalizerInterface
{
    /**
     * Returns normalization priority.
     *
     * @return int
     */
    public function getOrder();
}
