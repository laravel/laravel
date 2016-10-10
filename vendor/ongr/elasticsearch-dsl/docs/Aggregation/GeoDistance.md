# Geo Distance Aggregation

> More info about geo distance aggregation is in the [official elasticsearch docs][1]

A multi-bucket aggregation that works on geo_point fields
and conceptually works very similar to the range aggregation.

## Simple example

```JSON
{
    "aggregations" : {
        "rings_around_amsterdam" : {
            "geo_distance" : {
                "field" : "location",
                "origin" : "52.3760, 4.894",
                "ranges" : [
                    { "to" : 100 },
                    { "from" : 100, "to" : 300 },
                    { "from" : 300 }
                ]
            }
        }
    }
}
```

And now the query via DSL:

```php
$geoDistanceAggregation = new GeoDistanceAggregation(
    'rings_around_amsterdam',
    'location',
    '52.3760, 4.894',
    [
        ['to' => 100],
        ['from' => 100, 'to' => 300],
        ['from' => 300],
    ]
);

$search = new Search();
$search->addAggregation($geoDistanceAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-geodistance-aggregation.html
