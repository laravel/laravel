# Value Count Aggregation

> More info about value count aggregation is in the [official elasticsearch docs][1]

A single-value metrics aggregation that counts the number of values that are extracted from the aggregated documents.

## Simple example

```JSON
{
    "aggregations" : {
        "grades_count" : { "value_count" : { "field" : "grade" } }
    }
}
```

And now the query via DSL:

```php
$valueCountAggregation = new ValueCountAggregation('grades_count', 'grade');

$search = new Search();
$search->addAggregation($valueCountAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-valuecount-aggregation.html
