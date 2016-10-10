# Missing Aggregation

> More info about missing aggregation is in the [official elasticsearch docs][1]

A field data based single bucket aggregation, that creates a bucket of all documents
in the current document set context that are missing a field value.

## Simple example

```JSON
{
     "aggregations" : {
         "products_without_a_price" : {
             "missing" : { "field" : "price" }
         }
     }
 }
```

And now the query via DSL:

```php
$missingAggregation = new MissingAggregation('products_without_a_price', 'price');

$search = new Search();
$search->addAggregation($missingAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-missing-aggregation.html
