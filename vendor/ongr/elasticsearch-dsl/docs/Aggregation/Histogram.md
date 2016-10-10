# Histogram Aggregation

> More info about histogram aggregation is in the [official elasticsearch docs][1]

A multi-bucket values source based aggregation that can be applied on numeric values extracted from
the documents. It dynamically builds fixed size (a.k.a. interval) buckets over the values.

## Simple example

```JSON
{
    "aggregations": {
        "prices": {
            "histogram": {
                "field": "price",
                "interval": 50
            }
        }
    }
}
```

And now the query via DSL:

```php
$histogramAggregation = new HistogramAggregation('prices', 'price', 50);

$search = new Search();
$search->addAggregation($histogramAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-histogram-aggregation.html
