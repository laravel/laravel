# Cardinality Aggregation

> More info about cardinality aggregation is in the [official elasticsearch docs][1]

A single-value metrics aggregation that calculates an approximate count of distinct values.

## Simple example

```JSON
{
     "aggregations" : {
         "author_count" : {
             "cardinality" : {
                 "field" : "author"
             }
         }
     }
 }
```

And now the query via DSL:

```php
$cardinalityAggregation = new CardinalityAggregation('author_count');
$cardinalityAggregation->setField('author');

$search = new Search();
$search->addAggregation($cardinalityAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-cardinality-aggregation.html
