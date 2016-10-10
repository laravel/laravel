# Min Aggregation

> More info about min aggregation is in the [official elasticsearch docs][1]

A single-value metrics aggregation that keeps track and returns the minimum value among
numeric values extracted from the aggregated documents.

## Simple example

```JSON
{
    "aggregations" : {
        "min_price" : { "min" : { "field" : "price" } }
    }
}
```

And now the query via DSL:

```php
$minAggregation = new MinAggregation('min_price', 'price');

$search = new Search();
$search->addAggregation($minAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-min-aggregation.html
