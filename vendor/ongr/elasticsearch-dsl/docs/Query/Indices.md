# Indices Query

> More info about indices query is in the [official elasticsearch docs][1]

The indices query can be used when executed across multiple indices, allowing to have a query that executes
only when executed on an index that matches a specific list of indices, and another query that executes
when it is executed on an index that does not match the listed indices.

## Simple example

```JSON
{
    "indices" : {
        "indices" : ["index1", "index2"],
        "query" : {
            "term" : { "tag" : "wow" }
        },
        "no_match_query" : {
            "term" : { "tag" : "kow" }
        }
    }
}
```

In DSL:

```php
$matchTermQuery = new TermQuery('tag', 'wow');
$noMatchTermQuery = new TermQuery('tag', 'kow');

$indicesQuery = new IndicesQuery(['index1', 'index2'], $matchTermQuery, $noMatchTermQuery);

$search = new Search();
$search->addQuery($indicesQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-indices-query.html
