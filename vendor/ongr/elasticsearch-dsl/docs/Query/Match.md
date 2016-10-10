# Match Query

> More info about match query is in the [official elasticsearch docs][1]

A family of match queries that accept text/numerics/dates, analyzes it, and constructs a query out of it.

## Simple example

```JSON
{
    "match" : {
        "message" : "this is a test"
    }
}
```

In DSL:

```php
$matchQuery = new MatchQuery('message', 'this is a test');

$search = new Search();
$search->addQuery($matchQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html
