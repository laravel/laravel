# Has Parent Query

> More info about has parent query is in the [official elasticsearch docs][1]

The has parent query accepts a query and a parent type. The query is executed in the parent document space, which is
specified by the parent type. This filter returns child documents which associated parents have matched.

## Simple example

```JSON
{
    "has_parent" : {
        "parent_type" : "blog",
        "query" : {
            "term" : {
                "tag" : "something"
            }
        }
    }
}
```

In DSL:

```php
$termQuery = new TermQuery('tag', 'something');

$hasParentQuery = new HasParentQuery('blog', $termQuery);

$search = new Search();
$search->addQuery($hasParentQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-parent-query.html
