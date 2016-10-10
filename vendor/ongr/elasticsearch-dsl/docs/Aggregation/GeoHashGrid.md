# Geo Hash Grid Aggregation

> More info about geo hash grid aggregation is in the [official elasticsearch docs][1]

A multi-bucket aggregation that works on geo_point fields and groups points into buckets
that represent cells in a grid.

## Simple example

```JSON
{
    "aggregations" : {
        "GrainGeoHashGrid" : {
            "geohash_grid" : {
                "field" : "location",
                "precision" : 3
            }
        }
    }
}
```

And now the query via DSL:

```php
$geoHashGridAggregation = new GeoHashGridAggregation(
    'GrainGeoHashGrid',
    'location',
    3
);

$search = new Search();
$search->addAggregation($geoHashGridAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-geohashgrid-aggregation.html
