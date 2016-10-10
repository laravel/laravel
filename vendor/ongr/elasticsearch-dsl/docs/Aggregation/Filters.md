# Filters Aggregation

> More info about filters aggregation is in the [official elasticsearch docs][1]

Defines a multi bucket aggregations where each bucket is associated with a filter.
Each bucket will collect all documents that match its associated filter.

Filters can have names or be anonymous, this is controlled by setAnonymous method.
By default filters are not anonymous and trying to add filter without name will result
in exception.

## Named example

```JSON
{
  "aggregations" : {
    "grades_stats" : {
      "filters" : {
        "filters" : {
          "error" :   { "term" : { "body" : "error"   }},
          "warning" : { "term" : { "body" : "warning" }}
        }
      },
      "aggregations" : {
        "monthly" : {
          "histogram" : {
            "field" : "timestamp",
            "interval" : "1M"
          }
        }
      }
    }
  }
}
```

And now the query via DSL:

```php
$errorTermFilter = new TermQuery('body', 'error');
$warningTermFilter = new TermQuery('body', 'warning');

$histogramAggregation = new HistogramAggregation('monthly', 'timestamp');
$histogramAggregation->setInterval('1M');

$filterAggregation = new FiltersAggregation(
    'grades_stats',
    [
        'error' => $errorTermFilter,
        'warning' => $warningTermFilter,
    ]
);
$filterAggregation->addAggregation($histogramAggregation);

$search = new Search();
$search->addAggregation($filterAggregation);

$queryArray = $search->toArray();
```

## Anonymous example

```JSON
{
  "aggregations" : {
    "grades_stats" : {
      "filters" : {
        "filters" : [
            { "term" : { "body" : "error"   }},
            { "term" : { "body" : "warning" }}
          ]
        }
      },
      "aggregations" : {
        "monthly" : {
          "histogram" : {
            "field" : "timestamp",
            "interval" : "1M"
          }
        }
      }
    }
  }
}
```
And now the query via DSL:

```php
$errorTermFilter = new TermQuery('body', 'error');
$warningTermFilter = new TermQuery('body', 'warning');

$histogramAggregation = new HistogramAggregation('monthly', 'timestamp');
$histogramAggregation->setInterval('1M');

$filterAggregation = new FiltersAggregation(
    'grades_stats',
    [
        $errorTermFilter,
        $warningTermFilter,
    ],
    true
);
$filterAggregation->addAggregation($histogramAggregation);

$search = new Search();
$search->addAggregation($filterAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filters-aggregation.html
