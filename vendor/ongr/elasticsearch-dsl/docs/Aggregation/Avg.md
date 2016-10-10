# Avg Aggregation

> More info about avg aggregation is in the [official elasticsearch docs][1]

A single-value metrics aggregation that computes the average of numeric values that are extracted from the aggregated documents.


## Simple example

```JSON
{
    "aggregations": {
        "avg_grade": {
            "avg": {
                "field": "grade"
            }
        }
    }
}
```

And now the query via DSL:

```php
$avgAggregation = new AvgAggregation('avg_grade');
$avgAggregation->setField('grade');

$search = new Search();
$search->addAggregation($avgAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-avg-aggregation.html
