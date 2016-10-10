# Simple Query String Query

> More info about simple query string query is in the [official elasticsearch docs][1]

A query that uses the SimpleQueryParser to parse its context

## Simple example

```JSON
{
    "simple_query_string" : {
        "query": "\"fried eggs\" +(eggplant | potato) -frittata",
        "analyzer": "snowball",
        "fields": ["body^5","_all"],
        "default_operator": "and"
    }
}
```

In DSL:

```php
$simpleQueryStringQuery = new SimpleQueryStringQuery(
    '"fried eggs" +(eggplant | potato) -frittata',
    [
        'analyzer' => 'snowball',
        'fields' => ['body^5', '_all'],
        'default_operator' => 'and',
    ]
);

$search = new Search();
$search->addQuery($simpleQueryStringQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html
