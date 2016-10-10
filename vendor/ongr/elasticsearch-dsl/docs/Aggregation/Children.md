# Children Aggregation

> More info about children aggregation is in the [official elasticsearch docs][1]

A special single bucket aggregation that enables aggregating from buckets on parent
document types to buckets on child documents.

## Simple example

```JSON
{
    "aggregations": {
        "author_count": {
            "children": {
                "type": "answer"
            },
            "aggregations": {
                "top_names": {
                    "terms": {
                        "field": "owner.display_name"
                    }
                }
            }
        }
    }
}
```

And now the query via DSL:

```php
$termsAggregation = new TermsAggregation('top_names', 'owner.display_name');

$childrenAggregation = new ChildrenAggregation('author_count', 'answer');
$childrenAggregation->addAggregation($termsAggregation);

$search = new Search();
$search->addAggregation($childrenAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-children-aggregation.html
