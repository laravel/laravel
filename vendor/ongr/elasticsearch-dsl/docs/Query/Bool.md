# Bool query

> More info about Bool query is in the [official elasticsearch docs][1]

It's a query that matches documents matching boolean combinations of other queries.

To create a bool query unlike other queries you don't have to create `BoolQuery` object. Just add queries to the search object and it will form bool query automatically.

Lets take an example to write a bool query with Elasticsearch DSL.

```JSON
{
    "bool" : {
        "must" : {
            "term" : { "user" : "kimchy" }
        },
        "must_not" : {
            "range" : {
                "age" : { "from" : 10, "to" : 20 }
            }
        },
        "should" : [
            {
                "term" : { "tag" : "wow" }
            },
            {
                "term" : { "tag" : "elasticsearch" }
            }
        ],
        "minimum_should_match" : 1,
        "boost" : 1.0
    }
}
```

And now the query via DSL:

```php
$termQueryForUser = new TermQuery("user", "kimchy");
$termQueryForTag1 = new TermQuery("tag", "wow");
$termQueryForTag2 = new TermQuery("tag", "elasticsearch");
$rangeQuery = new RangeQuery("age", ["from" => 10, "to" => 20]);

$bool = new BoolQuery();
$bool->addParameter("minimum_should_match", 1);
$bool->addParameter("boost", 1);
$bool->add($termQueryForUser, BoolQuery::MUST);
$bool->add($rangeQuery, BoolQuery::MUST_NOT);
$bool->add($termQueryForTag1, BoolQuery::SHOULD);
$bool->add($termQueryForTag2, BoolQuery::SHOULD);

$search = new Search();
$search->addQuery($bool);

$queryArray = $search->toArray();
```

There is also an exception due adding queries to the search. If you add only one query without type it will form simple query. e.g. lets try to create match all query.

```php
$search = new Search();
$matchAllQuery = new MatchAllQuery();
$search->addQuery($matchAllQuery);
$queryArray = $search->toArray();
```

You will get this query:
```JSON
{
    "query": {
        "match_all": {}
    }
}
```

> More info about `Search` look in the [How to search](../HowTo/HowToSearch.md) chapter.



[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
