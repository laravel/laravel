# Has child Query

> More info about has child query is in the [official elasticsearch docs][1]

The has child query accepts a query and the child type to run against, and results in parent documents that have child
docs matching the query.

## Simple example

```JSON
{
    "has_child" : {
        "type" : "blog_tag",
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

$hasChildQuery = new HasChildQuery('blog_tag', $termQuery);

$search = new Search();
$search->addQuery($hasChildQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-child-query.html
