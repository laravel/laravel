# Top Hits Aggregation

> More info about top hits aggregation is in the [official elasticsearch docs][1]

A top hits metric aggregator keeps track of the most relevant document
being aggregated. This aggregator is intended to be used as a sub aggregator,
so that the top matching documents can be aggregated per bucket.

## Simple example

```JSON
{
    "aggregation": {
        "top-tags": {
            "terms": {
                "field": "tags",
                "size": 3
            },
            "aggregations": {
                "top_tag_hits": {
                    "top_hits": {
                        "sort": [
                            {
                                "last_activity_date": {
                                    "order": "desc"
                                }
                            }
                        ],
                        "_source": {
                            "include": [
                                "title"
                            ]
                        },
                        "size" : 1
                    }
                }
            }
        }
    }
}
```

And now the query via DSL:

```php
$sort = new FieldSort('last_activity_date', FieldSort::DESC);
$sorts = new Sorts();
$sorts->addSort($sort);
$topHitsAggregation = new TopHitsAggregation('top_tag_hits', 1, null, $sorts);
$topHitsAggregation->addParameter('_source', ['include' => ['title']]);

$termsAggregation = new TermsAggregation('top-tags', 'tags');
$termsAggregation->addParameter('size', 3);
$termsAggregation->addAggregation($topHitsAggregation);

$search = new Search();
$search->addAggregation($termsAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-top-hits-aggregation.html
