# Max Aggregation

> More info about max aggregation is in the [official elasticsearch docs][1]

A single-value metrics aggregation that keeps track and returns the
maximum value among the numeric values extracted from the aggregated documents.

## Simple example

```JSON
{
    "aggregations" : {
        "max_price" : { "max" : { "field" : "price" } }
    }
}
```

And now the query via DSL:

```php
$maxAggregation = new MaxAggregation('max_price', 'price');

$search = new Search();
$search->addAggregation($maxAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-max-aggregation.html
