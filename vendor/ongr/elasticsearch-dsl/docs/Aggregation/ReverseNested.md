# Reverse Nested Aggregation

> More info about reverse nested aggregation is in the [official elasticsearch docs][1]

A special single bucket aggregation that enables aggregating on parent docs from nested documents.

## Simple example

```JSON
{
  "aggregations": {
    "comments": {
      "nested": {
        "path": "comments"
      },
      "aggregations": {
        "top_usernames": {
          "terms": {
            "field": "comments.username"
          },
          "aggregations": {
            "comment_to_issue": {
              "reverse_nested": {},
              "aggregations": {
                "top_tags_per_comment": {
                  "terms": {
                    "field": "tags"
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
```

And now the query via DSL:

```php
$tagsTermsAggregations = new TermsAggregation('top_tags_per_comment', 'tags');

$reverseNestedAggregation = new ReverseNestedAggregation('comment_to_issue');
$reverseNestedAggregation->addAggregation($tagsTermsAggregations);

$usernameTermsAggregation = new TermsAggregation('top_usernames', 'comments.username');
$usernameTermsAggregation->addAggregation($reverseNestedAggregation);

$nestedAggregation = new NestedAggregation('comments', 'comments');
$nestedAggregation->addAggregation($usernameTermsAggregation);

$search = new Search();
$search->addAggregation($nestedAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-reverse-nested-aggregation.html
