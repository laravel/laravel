# Match All Query

> More info about match all query is in the [official elasticsearch docs][1]

A query that matches all documents

## Simple example

```JSON
{
    "match_all" : { }
}
```

In DSL:

```php
$matchAllQuery = new MatchAllQuery();

$search = new Search();
$search->addQuery($matchAllQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html
