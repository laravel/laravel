# Terms Query

> More info about terms query is in the [official elasticsearch docs][1]

A query that match on any of the provided terms.

## Simple example

```JSON
{
    "terms" : {
        "tags" : [ "blue", "pill" ],
        "minimum_should_match" : 1
    }
}
```

In DSL:

```php
$termsQuery = new TermsQuery(
    'tags',
    ['blue', 'pill'],
    ['minimum_should_match' => 1]
);

$search = new Search();
$search->addQuery($termsQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html
