<?php

namespace Elasticsearch\Namespaces;

/**
 * Class IndicesNamespace
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Namespaces\IndicesNamespace
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class IndicesNamespace extends AbstractNamespace
{
    /**
     * $params['index'] = (list) A comma-separated list of indices to check (Required)
     *
     * @param $params array Associative array of parameters
     *
     * @return bool
     */
    public function exists($params)
    {
        $index = $this->extractArgument($params, 'index');

        //manually make this verbose so we can check status code
        $params['client']['verbose'] = true;

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Exists $endpoint */
        $endpoint = $endpointBuilder('Indices\Exists');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);

        return BooleanRequestWrapper::performRequest($endpoint);
    }

    /**
     * $params['index'] = (list) A comma-separated list of indices to check (Required)
     *        ['feature'] = (list) A comma-separated list of features to return
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *        ['local']   = (bool) Return local information, do not retrieve the state from master node (default: false)
     *
     * @param $params array Associative array of parameters
     *
     * @return bool
     */
    public function get($params)
    {
        $index = $this->extractArgument($params, 'index');
        $feature = $this->extractArgument($params, 'feature');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Get $endpoint */
        $endpoint = $endpointBuilder('Indices\Get');
        $endpoint->setIndex($index)
                 ->setFeature($feature)
                 ->setParams($params);

        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']               = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *        ['operation_threading'] = () TODO: ?
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function segments($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Segments $endpoint */
        $endpoint = $endpointBuilder('Indices\Segments');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['name']    = (string) The name of the template (Required)
     *        ['timeout'] = (time) Explicit operation timeout
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function deleteTemplate($params)
    {
        $name = $this->extractArgument($params, 'name');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Template\Delete $endpoint */
        $endpoint = $endpointBuilder('Indices\Template\Delete');
        $endpoint->setName($name);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index'] = (list) A comma-separated list of index names to register warmer for; use `_all` or empty string to perform the operation on all indices (Required)
     *        ['name']  = (string) The name of the warmer (supports wildcards); leave empty to delete all warmers
     *        ['type']  = (list) A comma-separated list of document types to register warmer for; use `_all` or empty string to perform the operation on all types
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function deleteWarmer($params)
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        $type = $this->extractArgument($params, 'type');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Warmer\Delete $endpoint */
        $endpoint = $endpointBuilder('Indices\Warmer\Delete');
        $endpoint->setIndex($index)
                 ->setName($name)
                 ->setType($type);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']   = (list) A comma-separated list of indices to delete; use `_all` or empty string to delete all indices
     *        ['timeout'] = (time) Explicit operation timeout
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function delete($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Delete $endpoint */
        $endpoint = $endpointBuilder('Indices\Delete');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['fields']         = (boolean) A comma-separated list of fields for `fielddata` metric (supports wildcards)
     *        ['index']          = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *        ['indexing_types'] = (list) A comma-separated list of document types to include in the `indexing` statistics
     *        ['metric_family']  = (enum) Limit the information returned to a specific metric
     *        ['search_groups']  = (list) A comma-separated list of search groups to include in the `search` statistics
     *        ['all']            = (boolean) Return all available information
     *        ['clear']          = (boolean) Reset the default level of detail
     *        ['docs']           = (boolean) Return information about indexed and deleted documents
     *        ['fielddata']      = (boolean) Return information about field data
     *        ['filter_cache']   = (boolean) Return information about filter cache
     *        ['flush']          = (boolean) Return information about flush operations
     *        ['get']            = (boolean) Return information about get operations
     *        ['groups']         = (boolean) A comma-separated list of search groups for `search` statistics
     *        ['id_cache']       = (boolean) Return information about ID cache
     *        ['ignore_indices'] = (enum) When performed on multiple indices, allows to ignore `missing` ones
     *        ['indexing']       = (boolean) Return information about indexing operations
     *        ['merge']          = (boolean) Return information about merge operations
     *        ['refresh']        = (boolean) Return information about refresh operations
     *        ['search']         = (boolean) Return information about search operations; use the `groups` parameter to include information for specific search groups
     *        ['store']          = (boolean) Return information about the size of the index
     *        ['warmer']         = (boolean) Return information about warmers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function stats($params = array())
    {
        $metric = $this->extractArgument($params, 'metric');

        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Stats $endpoint */
        $endpoint = $endpointBuilder('Indices\Stats');
        $endpoint->setIndex($index)
                 ->setMetric($metric);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index'] = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *        ['body']  = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function putSettings($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Settings\Put $endpoint */
        $endpoint = $endpointBuilder('Indices\Settings\Put');
        $endpoint->setIndex($index)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']              = (list) A comma-separated list of index names; use `_all` or empty string for all indices
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function snapshotIndex($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Gateway\Snapshot $endpoint */
        $endpoint = $endpointBuilder('Indices\Gateway\Snapshot');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index'] = (list) A comma-separated list of index names; use `_all` or empty string for all indices
     *        ['type']  = (list) A comma-separated list of document types
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function getMapping($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        $type = $this->extractArgument($params, 'type');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Mapping\Get $endpoint */
        $endpoint = $endpointBuilder('Indices\Mapping\Get');
        $endpoint->setIndex($index)
                 ->setType($type);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']            = (list) A comma-separated list of index names; use `_all` or empty string for all indices
     *        ['type']             = (list) A comma-separated list of document types
     *        ['field']            = (list) A comma-separated list of document fields
     *        ['include_defaults'] = (bool) specifies default mapping values should be returned
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function getFieldMapping($params = array())
    {
        $index = $this->extractArgument($params, 'index');
        $type = $this->extractArgument($params, 'type');
        $fields = $this->extractArgument($params, 'fields');

        if (!isset($fields)) {
            $fields = $this->extractArgument($params, 'field');
        }


        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Mapping\GetField $endpoint */
        $endpoint = $endpointBuilder('Indices\Mapping\GetField');
        $endpoint->setIndex($index)
                 ->setType($type)
                 ->setFields($fields);

        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']              = (list) A comma-separated list of index names; use `_all` or empty string for all indices
     *        ['force']              = (boolean) TODO: ?
     *        ['full']               = (boolean) TODO: ?
     *        ['refresh']            = (boolean) Refresh the index after performing the operation
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function flush($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Flush $endpoint */
        $endpoint = $endpointBuilder('Indices\Flush');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']              = (list) A comma-separated list of index names; use `_all` or empty string for all indices
     *        ['force']              = (boolean) TODO: ?
     *        ['full']               = (boolean) TODO: ?
     *        ['refresh']            = (boolean) Refresh the index after performing the operation
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function flushSynced($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Flush $endpoint */
        $endpoint = $endpointBuilder('Indices\Flush');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $endpoint->setSynced(true);
        $response = $endpoint->performRequest();
        return $endpoint->resultOrFuture($response);
    }


    /**
     * $params['index']               = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *        ['operation_threading'] = () TODO: ?
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function refresh($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Refresh $endpoint */
        $endpoint = $endpointBuilder('Indices\Refresh');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']       = (list) A comma-separated list of index names; use `_all` or empty string for all indices
     *        ['detailed']    = (bool) Whether to display detailed information about shard recovery
     *        ['active_only'] = (bool) Display only those recoveries that are currently on-going
     *        ['human']       = (bool) Whether to return time and byte values in human-readable format.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function recovery($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Flush $endpoint */
        $endpoint = $endpointBuilder('Indices\Recovery');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']              = (list) A comma-separated list of index names; use `_all` to check the types across all indices (Required)
     *        ['type']               = (list) A comma-separated list of document types to check (Required)
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function existsType($params)
    {
        $index = $this->extractArgument($params, 'index');

        $type = $this->extractArgument($params, 'type');

        //manually make this verbose so we can check status code
        $params['client']['verbose'] = true;

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Type\Exists $endpoint */
        $endpoint = $endpointBuilder('Indices\Type\Exists');
        $endpoint->setIndex($index)
                 ->setType($type);
        $endpoint->setParams($params);

        return BooleanRequestWrapper::performRequest($endpoint);
    }

    /**
     * $params['index']   = (string) The name of the index with an alias
     *        ['name']    = (string) The name of the alias to be created or updated
     *        ['timeout'] = (time) Explicit timestamp for the document
     *        ['body']    = (time) Explicit timestamp for the document
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function putAlias($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Alias\Put $endpoint */
        $endpoint = $endpointBuilder('Indices\Alias\Put');
        $endpoint->setIndex($index)
                 ->setName($name)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index'] = (list) A comma-separated list of index names to restrict the operation; use `_all` or empty string to perform the operation on all indices (Required)
     *        ['name']  = (string) The name of the warmer (supports wildcards); leave empty to get all warmers
     *        ['type']  = (list) A comma-separated list of document types to restrict the operation; leave empty to perform the operation on all types
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function getWarmer($params)
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        $type = $this->extractArgument($params, 'type');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Warmer\Get $endpoint */
        $endpoint = $endpointBuilder('Indices\Warmer\Get');
        $endpoint->setIndex($index)
                 ->setName($name)
                 ->setType($type);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index'] = (list) A comma-separated list of index names to register the warmer for; use `_all` or empty string to perform the operation on all indices (Required)
     *        ['name']  = (string) The name of the warmer (Required)
     *        ['type']  = (list) A comma-separated list of document types to register the warmer for; leave empty to perform the operation on all types
     *        ['body']  = (list) A comma-separated list of document types to register the warmer for; leave empty to perform the operation on all types
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function putWarmer($params)
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        $type = $this->extractArgument($params, 'type');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Warmer\Put $endpoint */
        $endpoint = $endpointBuilder('Indices\Warmer\Put');
        $endpoint->setIndex($index)
                 ->setName($name)
                 ->setType($type)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['name']    = (string) The name of the template (Required)
     *        ['order']   = (number) The order for this template when merging multiple matching ones (higher numbers are merged later, overriding the lower numbers)
     *        ['timeout'] = (time) Explicit operation timeout
     *        ['body']    = (time) Explicit operation timeout
     *        ['create']  = (bool) Whether the index template should only be added if new or can also replace an existing one
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function putTemplate($params)
    {
        $name = $this->extractArgument($params, 'name');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Template\Put $endpoint */
        $endpoint = $endpointBuilder('Indices\Template\Put');
        $endpoint->setName($name)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']               = (list) A comma-separated list of index names to restrict the operation; use `_all` or empty string to perform the operation on all indices
     *        ['type']                = (list) A comma-separated list of document types to restrict the operation; leave empty to perform the operation on all types
     *        ['explain']             = (boolean) Return detailed information about the error
     *        ['ignore_indices']      = (enum) When performed on multiple indices, allows to ignore `missing` ones
     *        ['operation_threading'] = () TODO: ?
     *        ['source']              = (string) The URL-encoded query definition (instead of using the request body)
     *        ['body']                = (string) The URL-encoded query definition (instead of using the request body)
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function validateQuery($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        $type = $this->extractArgument($params, 'type');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Validate\Query $endpoint */
        $endpoint = $endpointBuilder('Indices\Validate\Query');
        $endpoint->setIndex($index)
                 ->setType($type)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['name']           = (list) A comma-separated list of alias names to return (Required)
     *        ['index']          = (list) A comma-separated list of index names to filter aliases
     *        ['ignore_indices'] = (enum) When performed on multiple indices, allows to ignore `missing` ones
     *        ['name']           = (list) A comma-separated list of alias names to return
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function getAlias($params)
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Alias\Get $endpoint */
        $endpoint = $endpointBuilder('Indices\Alias\Get');
        $endpoint->setIndex($index)
                 ->setName($name);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']            = (list) A comma-separated list of index names; use `_all` to perform the operation on all indices (Required)
     *        ['type']             = (string) The name of the document type
     *        ['ignore_conflicts'] = (boolean) Specify whether to ignore conflicts while updating the mapping (default: false)
     *        ['timeout']          = (time) Explicit operation timeout
     *        ['body']             = (time) Explicit operation timeout
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function putMapping($params)
    {
        $index = $this->extractArgument($params, 'index');

        $type = $this->extractArgument($params, 'type');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Mapping\Put $endpoint */
        $endpoint = $endpointBuilder('Indices\Mapping\Put');
        $endpoint->setIndex($index)
                 ->setType($type)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index'] = (list) A comma-separated list of index names; use `_all` for all indices (Required)
     *        ['type']  = (string) The name of the document type to delete (Required)
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function deleteMapping($params)
    {
        $index = $this->extractArgument($params, 'index');

        $type = $this->extractArgument($params, 'type');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Mapping\Delete $endpoint */
        $endpoint = $endpointBuilder('Indices\Mapping\Delete');
        $endpoint->setIndex($index)
                 ->setType($type);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['name'] = (string) The name of the template (Required)
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function getTemplate($params)
    {
        $name = $this->extractArgument($params, 'name');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Template\Get $endpoint */
        $endpoint = $endpointBuilder('Indices\Template\Get');
        $endpoint->setName($name);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['name'] = (string) The name of the template (Required)
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function existsTemplate($params)
    {
        $name = $this->extractArgument($params, 'name');

        //manually make this verbose so we can check status code
        $params['client']['verbose'] = true;

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Template\Exists $endpoint */
        $endpoint = $endpointBuilder('Indices\Template\Exists');
        $endpoint->setName($name);
        $endpoint->setParams($params);

        return BooleanRequestWrapper::performRequest($endpoint);
    }

    /**
     * $params['index']   = (string) The name of the index (Required)
     *        ['timeout'] = (time) Explicit operation timeout
     *        ['body']    = (time) Explicit operation timeout
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function create($params)
    {
        $index = $this->extractArgument($params, 'index');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Create $endpoint */
        $endpoint = $endpointBuilder('Indices\Create');
        $endpoint->setIndex($index)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']                = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *        ['flush']                = (boolean) Specify whether the index should be flushed after performing the operation (default: true)
     *        ['max_num_segments']     = (number) The number of segments the index should be merged into (default: dynamic)
     *        ['only_expunge_deletes'] = (boolean) Specify whether the operation should only expunge deleted documents
     *        ['operation_threading']  = () TODO: ?
     *        ['refresh']              = (boolean) Specify whether the index should be refreshed after performing the operation (default: true)
     *        ['wait_for_merge']       = (boolean) Specify whether the request should block until the merge process is finished (default: true)
     *        ['ignore_unavailable']   = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']     = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']     = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function optimize($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Optimize $endpoint */
        $endpoint = $endpointBuilder('Indices\Optimize');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']                = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *        ['flush']                = (boolean) Specify whether the index should be flushed after performing the operation (default: true)
     *        ['max_num_segments']     = (number) The number of segments the index should be merged into (default: dynamic)
     *        ['only_expunge_deletes'] = (boolean) Specify whether the operation should only expunge deleted documents
     *        ['operation_threading']  = () TODO: ?
     *        ['refresh']              = (boolean) Specify whether the index should be refreshed after performing the operation (default: true)
     *        ['wait_for_merge']       = (boolean) Specify whether the request should block until the merge process is finished (default: true)
     *        ['ignore_unavailable']   = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']     = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']     = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function forceMerge($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\ForceMerge $endpoint */
        $endpoint = $endpointBuilder('Indices\ForceMerge');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']   = (string) The name of the index with an alias (Required)
     *        ['name']    = (string) The name of the alias to be deleted (Required)
     *        ['timeout'] = (time) Explicit timestamp for the document
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function deleteAlias($params)
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Alias\Delete $endpoint */
        $endpoint = $endpointBuilder('Indices\Alias\Delete');
        $endpoint->setIndex($index)
                 ->setName($name);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']   = (string) The name of the index (Required)
     *        ['timeout'] = (time) Explicit operation timeout
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function open($params)
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Open $endpoint */
        $endpoint = $endpointBuilder('Indices\Open');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']        = (string) The name of the index to scope the operation
     *        ['analyzer']     = (string) The name of the analyzer to use
     *        ['field']        = (string) Use the analyzer configured for this field (instead of passing the analyzer name)
     *        ['filters']      = (list) A comma-separated list of filters to use for the analysis
     *        ['prefer_local'] = (boolean) With `true`, specify that a local shard should be used if available, with `false`, use a random shard (default: true)
     *        ['text']         = (string) The text on which the analysis should be performed (when request body is not used)
     *        ['tokenizer']    = (string) The name of the tokenizer to use for the analysis
     *        ['format']       = (enum) Format of the output
     *        ['body']         = (enum) Format of the output
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function analyze($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Analyze $endpoint */
        $endpoint = $endpointBuilder('Indices\Analyze');
        $endpoint->setIndex($index)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']              = (list) A comma-separated list of index name to limit the operation
     *        ['field_data']         = (boolean) Clear field data
     *        ['fielddata']          = (boolean) Clear field data
     *        ['fields']             = (list) A comma-separated list of fields to clear when using the `field_data` parameter (default: all)
     *        ['filter']             = (boolean) Clear filter caches
     *        ['filter_cache']       = (boolean) Clear filter caches
     *        ['filter_keys']        = (boolean) A comma-separated list of keys to clear when using the `filter_cache` parameter (default: all)
     *        ['id']                 = (boolean) Clear ID caches for parent/child
     *        ['id_cache']           = (boolean) Clear ID caches for parent/child
     *        ['recycler']           = (boolean) Clear the recycler cache
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function clearCache($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Cache\Clear $endpoint */
        $endpoint = $endpointBuilder('Indices\Cache\Clear');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']   = (list) A comma-separated list of index names to filter aliases
     *        ['timeout'] = (time) Explicit timestamp for the document
     *        ['body']    = (time) Explicit timestamp for the document
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function updateAliases($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Aliases\Update $endpoint */
        $endpoint = $endpointBuilder('Indices\Aliases\Update');
        $endpoint->setIndex($index)
                 ->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']   = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['timeout'] = (time) Explicit timestamp for the document
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function getAliases($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Aliases\Get $endpoint */
        $endpoint = $endpointBuilder('Indices\Aliases\Get');
        $endpoint->setIndex($index)
                 ->setName($name);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['name']               = (list) A comma-separated list of alias names to return (Required)
     *        ['index']              = (list) A comma-separated list of index names to filter aliases
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function existsAlias($params)
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        //manually make this verbose so we can check status code
        $params['client']['verbose'] = true;

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Alias\Exists $endpoint */
        $endpoint = $endpointBuilder('Indices\Alias\Exists');
        $endpoint->setIndex($index)
                 ->setName($name);
        $endpoint->setParams($params);

        return BooleanRequestWrapper::performRequest($endpoint);
    }

    /**
     * $params['index']               = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *        ['ignore_indices']      = (enum) When performed on multiple indices, allows to ignore `missing` ones
     *        ['operation_threading'] = () TODO: ?
     *        ['recovery']            = (boolean) Return information about shard recovery
     *        ['snapshot']            = (boolean) TODO: ?
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function status($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Status $endpoint */
        $endpoint = $endpointBuilder('Indices\Status');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index'] = (list) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function getSettings($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        $name = $this->extractArgument($params, 'name');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Settings\Get $endpoint */
        $endpoint = $endpointBuilder('Indices\Settings\Get');
        $endpoint->setIndex($index)
                 ->setName($name);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']   = (string) The name of the index (Required)
     *        ['timeout'] = (time) Explicit operation timeout
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function close($params)
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Close $endpoint */
        $endpoint = $endpointBuilder('Indices\Close');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']   = (string) The name of the index
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function seal($params)
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Seal $endpoint */
        $endpoint = $endpointBuilder('Indices\Seal');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']              = (list) A comma-separated list of index names; use `_all` or empty string for all indices
     *        ['wait_for_completion']= (boolean) Specify whether the request should block until the all segments are upgraded (default: false)
     *        ['only_ancient_segments'] = (boolean) If true, only ancient (an older Lucene major release) segments will be upgraded
     *        ['refresh']            = (boolean) Refresh the index after performing the operation
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function upgrade($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Upgrade\Post $endpoint */
        $endpoint = $endpointBuilder('Indices\Upgrade\Post');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();
        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']              = (list) A comma-separated list of index names; use `_all` or empty string for all indices
     *        ['wait_for_completion']= (boolean) Specify whether the request should block until the all segments are upgraded (default: false)
     *        ['only_ancient_segments'] = (boolean) If true, only ancient (an older Lucene major release) segments will be upgraded
     *        ['refresh']            = (boolean) Refresh the index after performing the operation
     *        ['ignore_unavailable'] = (bool) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices']   = (bool) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards']   = (enum) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function getUpgrade($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\Upgrade\Get $endpoint */
        $endpoint = $endpointBuilder('Indices\Upgrade\Get');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['index']   = (string) A comma-separated list of index names; use `_all` or empty string to perform the operation on all indices
     *        ['status']   = (list) A comma-separated list of statuses used to filter on shards to get store information for
     *        ['ignore_unavailable'] = (boolean) Whether specified concrete indices should be ignored when unavailable (missing or closed)
     *        ['allow_no_indices'] = (boolean) Whether to ignore if a wildcard indices expression resolves into no concrete indices. (This includes `_all` string or when no indices have been specified)
     *        ['expand_wildcards'] = (boolean) Whether to expand wildcard expression to concrete indices that are open, closed or both.
     *        ['operation_threading']
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function shardStores($params)
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Indices\ShardStores $endpoint */
        $endpoint = $endpointBuilder('Indices\ShardStores');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }
}
