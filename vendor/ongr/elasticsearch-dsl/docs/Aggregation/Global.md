# Global Aggregation

> More info about cardinality aggregation is in the [official elasticsearch docs][1]

Defines a single bucket of all the documents within the search execution
context. This context is defined by the indices and the document types
you’re searching on, but is **not influenced** by the search query itself.

## Simple example

```JSON
{
    "aggregations": {
        "all_products": {
            "global": {},
            "aggregations": {
                "avg_price": {
                    "avg": {
                        "field": "price"
                    }
                }
            }
        }
    }
}
```

And now the query via DSL:

```php
$avgAggregation = new AvgAggregation('avg_price', 'price');
$globalAggregation = new GlobalAggregation('all_products');
$globalAggregation->addAggregation($avgAggregation);

$search = new Search();
$search->addAggregation($globalAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-global-aggregation.html
