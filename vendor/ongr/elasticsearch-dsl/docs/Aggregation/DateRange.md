# Date Range Aggregation

> More info about date range aggregation is in the [official elasticsearch docs][1]

A range aggregation that is dedicated for date values.

## Simple example

```JSON
{
    "aggregations": {
        "range": {
            "date_range": {
                "field": "date",
                "format": "MM-yyy",
                "ranges": [
                    { "to": "now-10M/M" },
                    { "from": "now-10M/M" }
                ]
            }
        }
    }
}
```

And now the query via DSL:

```php
$dateRangeAggregation = new DateRangeAggregation('range');
$dateRangeAggregation->setField('date');
$dateRangeAggregation->setFormat('MM-yyy');
$dateRangeAggregation->addRange(null, 'now-10M/M');
$dateRangeAggregation->addRange('now-10M/M', null);

$search = new Search();
$search->addAggregation($dateRangeAggregation);

$queryArray = $search->toArray();
```

Or :

```php
$dateRangeAggregation = new DateRangeAggregation(
    'range',
    'date',
    'MM-yyy',
    [
        ['to' => 'now-10M/M'],
        ['from' => 'now-10M/M'],
    ]
);

$search = new Search();
$search->addAggregation($dateRangeAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-daterange-aggregation.html
