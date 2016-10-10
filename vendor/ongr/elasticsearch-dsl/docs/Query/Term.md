# Term Query

> More info about term query is in the [official elasticsearch docs][1]

The term query finds documents that contain the exact term specified in the inverted index.


## Simple example

```JSON
{
    "term" : { "user" : "Kimchy" } 
}
```

In DSL:

```php
$termQuery = new TermQuery('user', 'Kimchy');

$search = new Search();
$search->addQuery($termQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html
