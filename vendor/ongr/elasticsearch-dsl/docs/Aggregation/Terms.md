# Terms Aggregation

> More info about terms aggregation is in the [official elasticsearch docs][1]

A multi-bucket value source based aggregation where buckets are dynamically
built - one per unique value.

## Simple example

```JSON
{
    "aggregations" : {
        "genders" : {
            "terms" : { "field" : "gender" }
        }
    }
}
```

And now the query via DSL:

```php
$termsAggregation = new TermsAggregation('genders', 'gender');

$search = new Search();
$search->addAggregation($termsAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-terms-aggregation.html
