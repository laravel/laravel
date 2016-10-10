# Wildcard query

> More info about Wildcard query is in the [official elasticsearch docs][1]

Matches documents that have fields matching a wildcard expression (not analyzed).

Lets take an example to write a wildcard query with Elasticsearch DSL.

```JSON
{
    "wildcard" : {
        "user" : {
            "value" : "ki*y"
        },
        "boost" : 2.0
    }
}
```

And now the query via DSL:

```php

$search = new Search();
$wildcard= new WildcardQuery('user', 'ki*y', ["boost" => 2.0]);
$search->addQuery($wildcard);
$queryArray = $search->toArray();

```


[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html
