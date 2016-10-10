<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL;

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Highlight\Highlight;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\SearchEndpoint\AbstractSearchEndpoint;
use ONGR\ElasticsearchDSL\SearchEndpoint\AggregationsEndpoint;
use ONGR\ElasticsearchDSL\SearchEndpoint\FilterEndpoint;
use ONGR\ElasticsearchDSL\SearchEndpoint\HighlightEndpoint;
use ONGR\ElasticsearchDSL\SearchEndpoint\PostFilterEndpoint;
use ONGR\ElasticsearchDSL\SearchEndpoint\QueryEndpoint;
use ONGR\ElasticsearchDSL\SearchEndpoint\SearchEndpointFactory;
use ONGR\ElasticsearchDSL\SearchEndpoint\SearchEndpointInterface;
use ONGR\ElasticsearchDSL\SearchEndpoint\SortEndpoint;
use ONGR\ElasticsearchDSL\Serializer\Normalizer\CustomReferencedNormalizer;
use ONGR\ElasticsearchDSL\Serializer\OrderedSerializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use ONGR\ElasticsearchDSL\SearchEndpoint\SuggestEndpoint;

/**
 * Search object that can be executed by a manager.
 */
class Search
{
    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $from;

    /**
     * @var string
     */
    private $timeout;

    /**
     * @var int
     */
    private $terminateAfter;

    /**
     * @var string|null
     */
    private $scroll;

    /**
     * @var array|bool|string
     */
    private $source;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $scriptFields;

    /**
     * @var string
     */
    private $searchType;

    /**
     * @var string
     */
    private $requestCache;

    /**
     * @var bool
     */
    private $explain;

    /**
     * @var array
     */
    private $stats;

    /**
     * @var string[]
     */
    private $preference;

    /**
     * @var float
     */
    private $minScore;

    /**
     * @var OrderedSerializer
     */
    private $serializer;

    /**
     * @var SearchEndpointInterface[]
     */
    private $endpoints = [];

    /**
     * Initializes serializer.
     */
    public function __construct()
    {
        $this->serializer = new OrderedSerializer(
            [
                new CustomReferencedNormalizer(),
                new CustomNormalizer(),
            ]
        );
    }

    /**
     * Returns endpoint instance.
     *
     * @param string $type Endpoint type.
     *
     * @return SearchEndpointInterface
     */
    private function getEndpoint($type)
    {
        if (!array_key_exists($type, $this->endpoints)) {
            $this->endpoints[$type] = SearchEndpointFactory::get($type);
        }

        return $this->endpoints[$type];
    }

    /**
     * Destroys search endpoint.
     *
     * @param string $type Endpoint type.
     */
    public function destroyEndpoint($type)
    {
        unset($this->endpoints[$type]);
    }

    /**
     * Sets parameters to the endpoint.
     *
     * @param string $endpointName
     * @param array  $parameters
     */
    private function setEndpointParameters($endpointName, array $parameters)
    {
        /** @var AbstractSearchEndpoint $endpoint */
        $endpoint = $this->getEndpoint($endpointName);
        $endpoint->setParameters($parameters);
    }

    /**
     * Adds query to the search.
     *
     * @param BuilderInterface $query
     * @param string           $boolType
     * @param string           $key
     *
     * @return $this
     */
    public function addQuery(BuilderInterface $query, $boolType = BoolQuery::MUST, $key = null)
    {
        $endpoint = $this->getEndpoint(QueryEndpoint::NAME);
        $endpoint->addToBool($query, $boolType, $key);

        return $this;
    }

    /**
     * Returns queries inside BoolQuery instance.
     *
     * @return BuilderInterface
     */
    public function getQueries()
    {
        $endpoint = $this->getEndpoint(QueryEndpoint::NAME);

        return $endpoint->getBool();
    }

    /**
     * Sets query endpoint parameters.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setQueryParameters(array $parameters)
    {
        $this->setEndpointParameters(QueryEndpoint::NAME, $parameters);

        return $this;
    }

    /**
     * Adds a filter to the search.
     *
     * @param BuilderInterface $filter   Filter.
     * @param string           $boolType Example boolType values:
     *                                   - must
     *                                   - must_not
     *                                   - should.
     * @param string           $key
     *
     * @return $this
     */
    public function addFilter(BuilderInterface $filter, $boolType = BoolQuery::MUST, $key = null)
    {
        // Trigger creation of QueryEndpoint as filters depends on it
        $this->getEndpoint(QueryEndpoint::NAME);

        $endpoint = $this->getEndpoint(FilterEndpoint::NAME);
        $endpoint->addToBool($filter, $boolType, $key);

        return $this;
    }

    /**
     * Returns queries inside BoolFilter instance.
     *
     * @return BuilderInterface
     */
    public function getFilters()
    {
        $endpoint = $this->getEndpoint(FilterEndpoint::NAME);

        return $endpoint->getBool();
    }

    /**
     * Sets filter endpoint parameters.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setFilterParameters(array $parameters)
    {
        $this->setEndpointParameters(FilterEndpoint::NAME, $parameters);

        return $this;
    }

    /**
     * Adds a post filter to search.
     *
     * @param BuilderInterface $filter   Filter.
     * @param string           $boolType Example boolType values:
     *                                   - must
     *                                   - must_not
     *                                   - should.
     * @param string           $key
     *
     * @return int Key of post filter.
     */
    public function addPostFilter(BuilderInterface $filter, $boolType = BoolQuery::MUST, $key = null)
    {
        $this
            ->getEndpoint(PostFilterEndpoint::NAME)
            ->addToBool($filter, $boolType, $key);

        return $this;
    }

    /**
     * Returns queries inside BoolFilter instance.
     *
     * @return BuilderInterface
     */
    public function getPostFilters()
    {
        $endpoint = $this->getEndpoint(PostFilterEndpoint::NAME);

        return $endpoint->getBool();
    }

    /**
     * Sets post filter endpoint parameters.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setPostFilterParameters(array $parameters)
    {
        $this->setEndpointParameters(PostFilterEndpoint::NAME, $parameters);

        return $this;
    }

    /**
     * Adds aggregation into search.
     *
     * @param AbstractAggregation $aggregation
     *
     * @return $this
     */
    public function addAggregation(AbstractAggregation $aggregation)
    {
        $this->getEndpoint(AggregationsEndpoint::NAME)->add($aggregation, $aggregation->getName());

        return $this;
    }

    /**
     * Returns all aggregations.
     *
     * @return BuilderInterface[]
     */
    public function getAggregations()
    {
        return $this->getEndpoint(AggregationsEndpoint::NAME)->getAll();
    }

    /**
     * Adds sort to search.
     *
     * @param BuilderInterface $sort
     *
     * @return $this
     */
    public function addSort(BuilderInterface $sort)
    {
        $this->getEndpoint(SortEndpoint::NAME)->add($sort);

        return $this;
    }

    /**
     * Returns all set sorts.
     *
     * @return BuilderInterface[]
     */
    public function getSorts()
    {
        return $this->getEndpoint(SortEndpoint::NAME)->getAll();
    }

    /**
     * Allows to highlight search results on one or more fields.
     *
     * @param Highlight $highlight
     *
     * @return int Key of highlight.
     */
    public function addHighlight($highlight)
    {
        $this->getEndpoint(HighlightEndpoint::NAME)->add($highlight);

        return $this;
    }

    /**
     * Returns highlight builder.
     *
     * @return BuilderInterface
     */
    public function getHighlight()
    {
        /** @var HighlightEndpoint $highlightEndpoint */
        $highlightEndpoint = $this->getEndpoint(HighlightEndpoint::NAME);

        return $highlightEndpoint->getHighlight();
    }

    /**
    * Adds suggest into search.
    *
    * @param BuilderInterface $suggest
    *
    * @return $this
    */
    public function addSuggest(BuilderInterface $suggest)
    {
        $this->getEndpoint(SuggestEndpoint::NAME)->add($suggest, $suggest->getName());

        return $this;
    }

    /**
    * Returns all suggests.
    *
    * @return BuilderInterface[]
    */
    public function getSuggests()
    {
        return $this->getEndpoint(SuggestEndpoint::NAME)->getAll();
    }

    /**
     * Exclude documents which have a _score less than the minimum specified.
     *
     * @param float $minScore
     *
     * @return $this
     */
    public function setMinScore($minScore)
    {
        $this->minScore = $minScore;

        return $this;
    }

    /**
     * Returns min score value.
     *
     * @return float
     */
    public function getMinScore()
    {
        return $this->minScore;
    }

    /**
     * Paginate reed removed lts from.
     *
     * @param int $from
     *
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Sets timeout for query execution.
     *
     * @param string $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Sets maximum number of documents per shard.
     *
     * @param int $terminateAfter
     *
     * @return $this
     */
    public function setTerminateAfter($terminateAfter)
    {
        $this->terminateAfter = $terminateAfter;

        return $this;
    }

    /**
     * Returns results offset value.
     *
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set maximum number of results.
     *
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Returns maximum number of results query can request.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Allows to control how the _source field is returned with every hit.
     *
     * @param array|bool|string $source
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Returns source value.
     *
     * @return array|bool|string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Allows to selectively load specific stored fields for each document represented by a search hit.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Returns field value.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Allows to return a script evaluation (based on different fields) for each hit.
     *
     * @param array $scriptFields
     *
     * @return $this
     */
    public function setScriptFields($scriptFields)
    {
        $this->scriptFields = $scriptFields;

        return $this;
    }

    /**
     * Returns containing script fields.
     *
     * @return array
     */
    public function getScriptFields()
    {
        return $this->scriptFields;
    }

    /**
     * Sets explain property in request body search.
     *
     * @param bool $explain
     *
     * @return $this
     */
    public function setExplain($explain)
    {
        $this->explain = $explain;

        return $this;
    }

    /**
     * Returns if explain property is set in request body search.
     *
     * @return bool
     */
    public function isExplain()
    {
        return $this->explain;
    }

    /**
     * Sets a stats group.
     *
     * @param array $stats
     *
     * @return $this
     */
    public function setStats($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * Returns a stats group.
     *
     * @return array
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Setter for scroll duration, effectively setting if search is scrolled or not.
     *
     * @param string|null $duration
     *
     * @return $this
     */
    public function setScroll($duration = '5m')
    {
        $this->scroll = $duration;

        return $this;
    }

    /**
     * Returns scroll duration.
     *
     * @return string|null
     */
    public function getScroll()
    {
        return $this->scroll;
    }

    /**
     * Set search type.
     *
     * @param string $searchType
     *
     * @return $this
     */
    public function setSearchType($searchType)
    {
        $this->searchType = $searchType;

        return $this;
    }

    /**
     * Returns search type used.
     *
     * @return string
     */
    public function getSearchType()
    {
        return $this->searchType;
    }


    /**
     * Set request cache.
     *
     * @param string $requestCache
     *
     * @return $this
     */
    public function setRequestCache($requestCache)
    {
        $this->requestCache = $requestCache;

        return $this;
    }

    /**
     * Returns request cache.
     *
     * @return string
     */
    public function getRequestCache()
    {
        return $this->requestCache;
    }

    /**
     * Setter for preference.
     *
     * Controls which shard replicas to execute the search request on.
     *
     * @param mixed $preferenceParams Example values:
     *                                _primary
     *                                _primary_first
     *                                _local
     *                                _only_node:xyz (xyz - node id)
     *                                _prefer_node:xyz (xyz - node id)
     *                                _shards:2,3 (2 and 3 specified shards)
     *                                custom value
     *                                string[] combination of params.
     *
     * @return $this
     */
    public function setPreference($preferenceParams)
    {
        if (is_string($preferenceParams)) {
            $this->preference[] = $preferenceParams;
        }

        if (is_array($preferenceParams) && !empty($preferenceParams)) {
            $this->preference = $preferenceParams;
        }

        return $this;
    }

    /**
     * Returns preference params as string.
     *
     * @return string
     */
    public function getPreference()
    {
        return $this->preference ? implode(';', $this->preference) : null;
    }

    /**
     * Returns query url parameters.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return array_filter(
            [
                'scroll' => $this->getScroll(),
                'search_type' => $this->getSearchType(),
                'request_cache' => $this->getRequestCache(),
                'preference' => $this->getPreference(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $output = array_filter($this->serializer->normalize($this->endpoints));

        $params = [
            'from' => 'from',
            'size' => 'size',
            'fields' => 'fields',
            'scriptFields' => 'script_fields',
            'explain' => 'explain',
            'stats' => 'stats',
            'minScore' => 'min_score',
            'source' => '_source',
            'timeout' => 'timeout',
            'terminateAfter' => 'terminate_after',
        ];

        foreach ($params as $field => $param) {
            if ($this->$field !== null) {
                $output[$param] = $this->$field;
            }
        }

        return $output;
    }
}
