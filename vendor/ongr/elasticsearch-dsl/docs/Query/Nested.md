# Nested Query

> More info about nested query is in the [official elasticsearch docs][1]

Nested query allows to query nested objects / documents (see [nested mapping][2]). The query is executed against the nested
objects / documents as if they were indexed as separate documents (they are, internally) and resulting in the root
parent document (or parent nested mapping).

## Simple example

```JSON
{
    "nested" : {
        "path" : "obj1",
        "score_mode" : "avg",
        "query" : {
            "bool" : {
                "must" : [
                    {
                        "match" : {"obj1.name" : "blue"}
                    },
                    {
                        "range" : {"obj1.count" : {"gt" : 5}}
                    }
                ]
            }
        }
    }
}
```

In DSL:

```php
$matchQuery = new MatchQuery('obj1.name', 'blue');
$rangeQuery = new RangeQuery('obj1.count', ['gt' => 5]);
$boolQuery = new BoolQuery();
$boolQuery->add($matchQuery);
$boolQuery->add($rangeQuery);

$nestedQuery = new NestedQuery(
    'obj1',
    $boolQuery
);
$nestedQuery->addParameter('score_mode', 'avg');

$search = new Search();
$search->addQuery($nestedQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html
[2]: https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-nested-type.html
