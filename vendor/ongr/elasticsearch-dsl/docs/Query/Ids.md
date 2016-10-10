# Ids Query

> More info about ids query is in the [official elasticsearch docs][1]

Filters documents that only have the provided ids.

## Simple example

```JSON
{
    "ids" : {
        "type" : "my_type",
        "values" : ["1", "4", "100"]
    }
}
```

In DSL:

```php
$idsQuery = new IdsQuery(['1', '4', '100'], ['type' => 'my_type']);

$search = new Search();
$search->addQuery($idsQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-ids-query.html
