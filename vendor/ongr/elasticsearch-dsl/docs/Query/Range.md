# Range Query

> More info about range query is in the [official elasticsearch docs][1]

Matches documents with fields that have terms within a certain range.

## Simple example

```JSON
{
    {
        "range" : {
            "age" : {
                "gte" : 10,
                "lte" : 20,
                "boost" : 2.0
            }
        }
    }
}
```

In DSL:

```php
$rangeQuery = new RangeQuery(
    'age',
    [
        'gte' => 10,
        'lte' => 20,
        'boost' => 2.0,
    ]
);

$search = new Search();
$search->addQuery($rangeQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html
