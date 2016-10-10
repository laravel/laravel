# Regexp Query

> More info about regexp query is in the [official elasticsearch docs][1]

The regexp query allows you to use regular expression term queries.

## Simple example

```JSON
{
    "filter": {
        "regexp":{
            "name.first" : "s.*y"
        }
    }
}
```

And now the query via DSL:

```php
$regexpQuery = new RegexpQuery('name.first', 's.*y');

$search = new Search();
$search->addQuery($regexpQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html
