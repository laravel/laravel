# Boosting query

> More info about Boosting query is in the [official elasticsearch docs][1]

Lets take an example to write a query with Elasticsearch DSL.

```JSON
{
    "boosting" : {
        "positive" : {
            "term" : {
                "field1" : "value1"
            }
        },
        "negative" : {
            "term" : {
                "field2" : "value2"
            }
        },
        "negative_boost" : 0.2
    }
}
```

And now the query via DSL:

```php
$termQuery1 = new TermQuery("field1", "value1");
$termQuery2 = new TermQuery("field2", "value2");

$boostingQuery = new BoostingQuery($termQuery1, $termQuery2, 0.2);

$search = new Search();
$search->addQuery($boostingQuery);

$queryArray = $search->toArray();
```


[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-boosting-query.html
