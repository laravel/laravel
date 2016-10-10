# Ipv4 Range Aggregation

> More info about ipv4 range aggregation is in the [official elasticsearch docs][1]

Just like the dedicated date range aggregation, there is also a dedicated
range aggregation for IPv4 typed fields.

## Simple example

```JSON
{
    "aggregations" : {
        "ip_range" : {
            "ip_range" : {
                "field" : "ip",
                "ranges" : [
                    { "to" : "10.0.0.5" },
                    { "from" : "10.0.0.5" }
                ]
            }
        }
    }
}
```

And now the query via DSL:

```php
$ipv4RangeAggregation = new Ipv4RangeAggregation(
    'ip_range',
    'ip',
    [
        ['to' => '10.0.0.5'],
        ['from' => '10.0.0.5'],
    ]
);

$search = new Search();
$search->addAggregation($ipv4RangeAggregation);

$queryArray = $search->toArray();
```

## Example using masks

```php
$ipv4RangeAggregation = new Ipv4RangeAggregation(
    'ip_range',
    'ip',
    ['10.0.0.0/25']
);

$search = new Search();
$search->addAggregation($ipv4RangeAggregation);

$queryArray = $search->toArray();
```

## Example using adders

```php
$ipv4RangeAggregation = new Ipv4RangeAggregation('ip_range', 'ip');
$ipv4RangeAggregation->addMask('10.0.0.0/25');
$ipv4RangeAggregation->addRange(null, '10.0.0.5');
$ipv4RangeAggregation->addRange('10.0.0.5');
$ipv4RangeAggregation->addRange('10.0.0.0', '10.0.0.127');

$search = new Search();
$search->addAggregation($ipv4RangeAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-iprange-aggregation.html
