# Percentile Ranks Aggregation

> More info about percentile ranks aggregation is in the [official elasticsearch docs][1]

A multi-value metrics aggregation that calculates one or more percentile
ranks over numeric values extracted from the aggregated documents.

## Simple example

```JSON
{
    "aggregations" : {
        "load_time_outlier" : {
            "percentile_ranks" : {
                "field" : "load_time",
                "values" : [15, 30]
            }
        }
    }
}
```

And now the query via DSL:

```php
$percentileRanksAggregation = new PercentileRanksAggregation('load_time_outlier', 'load_time', [15, 30]);

$search = new Search();
$search->addAggregation($percentileRanksAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-percentile-rank-aggregation.html
