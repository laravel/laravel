# Geo Bounds Aggregation

> More info about geo bounds aggregation is in the [official elasticsearch docs][1]

A metric aggregation that computes the bounding box containing all geo_point values for a field.

## Simple example

```JSON
{
    "aggregations" : {
        "viewport" : {
            "geo_bounds" : {
                "field" : "location",
                "wrap_longitude" : true
            }
        }
    }
}
```

And now the query via DSL:

```php
$geoBoundsAggregation = new GeoBoundsAggregation('viewport', 'location');

$search = new Search();
$search->addAggregation($geoBoundsAggregation);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-geobounds-aggregation.html
