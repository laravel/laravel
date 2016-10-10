# Sum Aggregation

> More info about sum aggregation is in the [official elasticsearch docs][1]

A single-value metrics aggregation that sums up numeric values that are extracted from the aggregated documents.

## Simple example

```JSON
{
    "aggregations" : {
        "intraday_return" : { "sum" : { "field" : "change" } }
    }
}
```

And now the query via DSL:

```php
$sumAggregation = new SumAggregation('intraday_return', 'change');

$search = new Search();
$search->addAggregation($sumAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-sum-aggregation.html
