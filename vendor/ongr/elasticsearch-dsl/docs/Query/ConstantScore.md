# Constant score query

> More info about Constant score query is in the [official elasticsearch docs][1]

Inside constant score query you can insert filter or query.

Lets take an example to write a constant score query with filter inside.

```JSON
{
    "constant_score" : {
        "filter" : {
            "term" : { "user" : "kimchy"}
        },
        "boost" : 1.2
    }
}
```

And now the query via DSL:

```php
$termFilter = new TermQuery("user", "kimchy");
$constantScoreQuery = new ConstantScoreQuery($termFilter, ["boost" => 1.2]);

$search = new Search();
$search->addQuery($constantScoreQuery);

$queryArray = $search->toArray();
```

To form a query with query inside is very easy, just add a query in `ConstantScoreQuery` constructor instead of filter.

```JSON
{
    "constant_score" : {
        "query" : {
            "term" : { "user" : "kimchy"}
        },
        "boost" : 1.2
    }
}
```

via DSL:

```php
$termQuery = new TermQuery("user", "kimchy");
$constantScoreQuery = new ConstantScoreQuery($termQuery, ["boost" => 1.2]);

$search = new Search();
$search->addQuery($constantScoreQuery);

$queryArray = $search->toArray();
```


[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-constant-score-query.html
