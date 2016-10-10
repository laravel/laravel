# Query String Query

> More info about query string query is in the [official elasticsearch docs][1]

A query that uses a query parser in order to parse its content.

## Simple example

```JSON
{
    "query_string" : {
        "default_field" : "content",
        "query" : "this AND that OR thus"
    }
}
```

In DSL:

```php
$queryStringQuery = new QueryStringQuery('this AND that OR thus');
$queryStringQuery->addParameter('default_field', 'content');

$search = new Search();
$search->addQuery($queryStringQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html
