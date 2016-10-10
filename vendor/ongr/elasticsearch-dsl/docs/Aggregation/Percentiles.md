# Percentiles Aggregation

> More info about percentiles aggregation is in the [official elasticsearch docs][1]

A multi-value metrics aggregation that calculates one or more percentiles over
numeric values extracted from the aggregated documents.

## Simple example

```JSON
{
    "aggregations" : {
        "load_time_outlier" : {
            "percentiles" : {
                "field" : "load_time"
            }
        }
    }
}
```

And now the query via DSL:

```php
$percentilesAggregation = new PercentilesAggregation('load_time_outlier', 'load_time');

$search = new Search();
$search->addAggregation($percentilesAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-cardinality-aggregation.html
