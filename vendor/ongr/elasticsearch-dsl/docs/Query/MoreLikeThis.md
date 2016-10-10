# More Like This Query

> More info about more like this query is in the [official elasticsearch docs][1]

The More Like This Query (MLT Query) finds documents that are "like" a given set of documents.

## Simple example

```JSON
{
    "more_like_this" : {
        "fields" : ["title", "description"],
        "like_text" : "Once upon a time",
        "min_term_freq" : 1,
        "max_query_terms" : 12
    }
}
```

In DSL:

```php
$moreLikeThisQuery = new MoreLikeThisQuery(
    'Once upon a time',
    [
        'fields' => ['title', 'description'],
        'min_term_freq' => 1,
        'max_query_terms' => 12,
    ]
);

$search = new Search();
$search->addQuery($moreLikeThisQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-mlt-query.html
