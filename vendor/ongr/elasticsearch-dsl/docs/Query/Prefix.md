# Prefix Query

> More info about prefix query is in the [official elasticsearch docs][1]

Matches documents that have fields containing terms with a specified prefix.

## Simple example

```JSON
{
    "prefix" : { "user" : "ki" }
}
```

In DSL:

```php
$prefixQuery = new PrefixQuery('user', 'ki');

$search = new Search();
$search->addQuery($prefixQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html
