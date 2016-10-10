# How to set custom parameters to your search

Elasticsearch supports number of custom parameters which can be added to your query. Detailed explanation of each parameter can be found at [official documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-body.html#_parameters_5)

## Setting `timeout` parameter

This option allows user to specify timeout for query execution in ["Time units"](https://www.elastic.co/guide/en/elasticsearch/reference/current/common-options.html#time-units).

Following code

```php
$search = new Search();
$search->setTimeout('5s');
```

would generate the query like this:
```json
{
  "timeout": "5s"
}
```

## Setting `from` and `size` parameters

These parameters are usually used for pagination.

Following code

```php
$search = new Search();
$search->setSize(25);
$search->setFrom(50);
```

would generate the query like this:
```json
{
  "size": 25,
  "from": 50
}
```

## Setting `terminate_after` parameter

This parameter can limit how many documents can be fetched from shard before query execution is terminated.

Following code

```php
$search = new Search();
$search->setTerminateAfter(1000);
```

would generate the query like this:
```json
{
  "terminate_after": 1000
}
```

## Setting `search_type` and `request_cache` parameters

These parameters are different from previous ones because they have to be passed not in query's body but as query string parameters.

Following code

```php
$search = new Search();
$search->setSearchType('dfs_query_then_fetch');
$search->setRequestCache(true);
```

would generate query string with `?search_type=dfs_query_then_fetch&request_cache=true`.