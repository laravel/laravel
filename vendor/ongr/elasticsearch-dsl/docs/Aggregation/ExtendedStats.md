# Extended Stats Aggregation

> More info about extended stats aggregation is in the [official elasticsearch docs][1]

A multi-value metrics aggregation that computes stats over numeric values extracted from the aggregated documents.

## Simple example

```JSON
{
    "aggregations" : {
        "grades_stats" : {
            "extended_stats" : { "field" : "grade" }
        }
    }
}
```

And now the query via DSL:

```php
$extendedStatsAggregation = new ExtendedStatsAggregation('grades_stats', 'grade');

$search = new Search();
$search->addAggregation($extendedStatsAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-extendedstats-aggregation.html
