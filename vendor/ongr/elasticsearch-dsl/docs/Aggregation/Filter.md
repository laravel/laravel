# Filter Aggregation

> More info about filter aggregation is in the [official elasticsearch docs][1]

Defines a single bucket of all the documents in the current document set context that
match a specified filter. Often this will be used to narrow down the current aggregation
context to a specific set of documents.

## Simple example

```JSON
{
    "aggregations" : {
        "grades_stats" : {
            "filter" : { "term": { "color": "red" } },
            "aggregations" : {
                "avg_price" : { "avg" : { "field" : "price" } }
            }
        }
    }
}
```

And now the query via DSL:

```php
$termFilter = new TermQuery('color', 'red');
$avgAggregation = new AvgAggregation('avg_price', 'price');

$filterAggregation = new FilterAggregation('grades_stats', $termFilter);
$filterAggregation->addAggregation($avgAggregation);

$search = new Search();
$search->addAggregation($filterAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filter-aggregation.html
