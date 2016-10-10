# Common terms query

> More info about Common terms query is in the [official elasticsearch docs][1]

There are so many use cases with `Common Terms` query. We highly recommend to take a look at the [official docs][1] before continuing.

Lets take first example to write easy `Common query` with Elasticsearch DSL.

```JSON
{
  "common": {
    "name": {
      "query": "this is bonsai cool",
      "cutoff_frequency": 0.001,
      "minimum_should_match": {
          "low_freq" : 2,
          "high_freq" : 3
       }
    }
  }
}
```

And now the query via DSL:

```php
$commonTermsQuery = new CommonTermsQuery(
    "field_name",
    "this is bonsai cool",
    [
        "cutoff_frequency" => 0.001,
        "minimum_should_match" => [
          "low_freq" => 2,
          "high_freq" => 3,
        ],
    ]
);

$search = new Search();
$search->addQuery($commonTermsQuery);

$queryArray = $search->toArray();
```


[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-common-terms-query.html
