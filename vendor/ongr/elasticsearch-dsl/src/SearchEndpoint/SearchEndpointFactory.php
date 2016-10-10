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

/**
 * Factory for search endpoints.
 */
class SearchEndpointFactory
{
    /**
     * @var array Holds namespaces for endpoints.
     */
    private static $endpoints = [
        'query' => 'ONGR\ElasticsearchDSL\SearchEndpoint\QueryEndpoint',
        'filter' => 'ONGR\ElasticsearchDSL\SearchEndpoint\FilterEndpoint',
        'post_filter' => 'ONGR\ElasticsearchDSL\SearchEndpoint\PostFilterEndpoint',
        'sort' => 'ONGR\ElasticsearchDSL\SearchEndpoint\SortEndpoint',
        'highlight' => 'ONGR\ElasticsearchDSL\SearchEndpoint\HighlightEndpoint',
        'aggregations' => 'ONGR\ElasticsearchDSL\SearchEndpoint\AggregationsEndpoint',
        'suggest' => 'ONGR\ElasticsearchDSL\SearchEndpoint\SuggestEndpoint',
    ];

    /**
     * Returns a search endpoint instance.
     *
     * @param string $type Type of endpoint.
     *
     * @return SearchEndpointInterface
     *
     * @throws \RuntimeException Endpoint does not exist.
     */
    public static function get($type)
    {
        if (!array_key_exists($type, self::$endpoints)) {
            throw new \RuntimeException('Endpoint does not exist.');
        }

        return new self::$endpoints[$type]();
    }
}
