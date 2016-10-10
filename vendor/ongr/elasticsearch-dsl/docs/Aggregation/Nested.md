# Nested Aggregation

> More info about nested aggregation is in the [official elasticsearch docs][1]

A special single bucket aggregation that enables aggregating nested documents.

## Simple example

```JSON
{
    "aggregations" : {
        "resellers" : {
            "nested" : {
                "path" : "resellers"
            },
            "aggregations" : {
                "min_price" : { "min" : { "field" : "resellers.price" } }
            }
        }
    }
}
```

And now the query via DSL:

```php
$minAggregation = new MinAggregation('min_price', 'resellers.price');
$nestedAggregation = new NestedAggregation('resellers', 'resellers');
$nestedAggregation->addAggregation($minAggregation);

$search = new Search();
$search->addAggregation($nestedAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-nested-aggregation.html
