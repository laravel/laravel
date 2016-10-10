<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\SearchEndpoint;

use ONGR\ElasticsearchDSL\BuilderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;

/**
 * Interface used to define search endpoint.
 */
interface SearchEndpointInterface extends NormalizableInterface
{
    /**
     * Adds builder to search endpoint.
     *
     * @param BuilderInterface $builder Builder to add.
     * @param array            $key     Additional parameters relevant to builder.
     *
     * @return string Key of added builder.
     */
    public function add(BuilderInterface $builder, $key = null);

    /**
     * Adds builder to search endpoint's specific bool type container.
     *
     * @param BuilderInterface $builder  Builder to add.
     * @param array            $boolType Bool type for query or filter. If bool type is left null
     *                                       it will be treated as MUST.
     * @param array            $key      Additional parameters relevant to builder.
     *
     * @return string Key of added builder.
     */
    public function addToBool(BuilderInterface $builder, $boolType = null, $key = null);

    /**
     * Removes contained builder.
     *
     * @param int $key
     *
     * @return $this
     */
    public function remove($key);

    /**
     * Returns contained builder or null if Builder is not found.
     *
     * @param int $key
     *
     * @return BuilderInterface|null
     */
    public function get($key);

    /**
     * Returns contained builder or null if Builder is not found.
     *
     * @param string|null $boolType If bool type is left null it will return all builders from container.
     *
     * @return array
     */
    public function getAll($boolType = null);

    /**
     * Returns Bool filter or query instance with all builder objects inside.
     *
     * @return BuilderInterface
     */
    public function getBool();
}
