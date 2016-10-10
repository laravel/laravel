# Multi Match Query

> More info about multi match query is in the [official elasticsearch docs][1]

The multi match query builds on the [match][2] query to allow multi-field queries:

## Simple example

```JSON
{
  "multi_match" : {
    "query":    "this is a test", 
    "fields": [ "subject", "message" ] 
  }
}
```

In DSL:

```php
$multiMatchQuery = new MultiMatchQuery(
    ['subject', 'message'],
    'this is a test'
);

$search = new Search();
$search->addQuery($multiMatchQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html
[2]: Match.md
