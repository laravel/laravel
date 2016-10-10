# Range Aggregation

> More info about range aggregation is in the [official elasticsearch docs][1]

A multi-bucket value source based aggregation that enables the user to define a set of
ranges - each representing a bucket.

## Simple example

```JSON
{
    "aggs" : {
        "price_ranges" : {
            "range" : {
                "field" : "price",
                "keyed" : false,
                "ranges" : [
                    { "to" : 50 },
                    { "from" : 50, "to" : 100 },
                    { "from" : 100 }
                ]
            }
        }
    }
}
```

And now the query via DSL:

```php
$rangeAggregation = new RangeAggregation(
    'price_ranges',
    'price',
    [
        ['to' => 50],
        ['from' => 50, 'to' => 100],
        ['from' => 100],
    ]
);

$search = new Search();
$search->addAggregation($rangeAggregation);

$queryArray = $search->toArray();
```

## Keyed example

```php
$rangeAggregation = new RangeAggregation(
    'price_ranges',
    'price',
    [
        ['key' => 'cheap', 'to' => 50],
        ['from' => 50, 'to' => 100],
        ['key' => 'expensive', 'from' => 100],
    ],
    true
);

$search = new Search();
$search->addAggregation($rangeAggregation);

$queryArray = $search->toArray();
```

## Adder example

```php
$rangeAggregation = new RangeAggregation('price_ranges', 'price');
$rangeAggregation->setKeyed(true);
$rangeAggregation->addRange(null, 50, 'cheap');
$rangeAggregation->addRange(50, 100);
$rangeAggregation->addRange(100, null, 'expensive');

$search = new Search();
$search->addAggregation($rangeAggregation);

$queryArray = $search->toArray();
```


[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-range-aggregation.html
