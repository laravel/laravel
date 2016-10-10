# Query

Objective query builder represents all available [Elasticsearch queries][1].

To form a query you have to create `Search` object. See below an example of match all query usage.

```php
$search = new Search();
$matchAllQuery = new MatchAllQuery();
$search->addQuery($matchAllQuery);
$queryArray = $search->toArray();
```

Query handles are necessary little things like where to put `\stdClass` and where to simple array. So by using DSL builder you can be always sure that it will form a correct query.

Here's `$queryArray` var_dump:

```php
//$queryArray content
'query' =>
    [
      'match_all' => \stdClass(),
    ]
```

For more information how to combine search queries take a look at [How to search](../HowTo/HowToSearch.md) chapter.


## Queries:
 - [Bool](Bool.md)
 - [Boosting](Boosting.md)
 - [Common terms](CommonTerms.md)
 - [Constant Score](ConstantScore.md)
 - [DisMax](DisMax.md)
 - [Function score](FunctionScore.md)
 - [Fuzzy](Fuzzy.md)
 - [Has child](HasChild.md)
 - [Has parent](HasParent.md)
 - [Ids](Ids.md)
 - [Indices](Indices.md)
 - [Match all](MatchAll.md)
 - [Match](Match.md)
 - [More like this](MoreLikeThis.md)
 - [Multi match](MultiMatch.md)
 - [Nested](Nested.md)
 - [Prefix](Prefix.md)
 - [Query string](QueryString.md)
 - [Range](Range.md)
 - [Regexp](Regexp.md)
 - [Simple query string](SimpleQueryString.md)
 - [Term](Term.md)
 - [Terms](Terms.md)
 - [Wildcard](Wildcard.md)

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-queries.html
