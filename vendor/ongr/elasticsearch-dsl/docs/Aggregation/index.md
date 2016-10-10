# Aggregation

Objective aggregation builder represents all available [Elasticsearch aggregations][1].


To form an aggregation you have to create `Search` object. See below an example of min aggregation usage.

```php
$search = new Search();
$minAggregation = new MinAggregation('min_agg');
$minAggregation->setField('price');
$search->addAggregation($minAggregation);
$queryArray = $search->toArray();
```

There are 2 types of aggregation: bucketing and metric. The only difference in using them is that metric bucketing
aggregations supports nesting while metric aggregations will ignore any set nested aggregations.

## Nesting aggregations

Bucketing aggregation can have any number nested aggregations and nesting can go to unlimited depth.

Example nested aggregation.
```JSON
{
    "aggregations": {
        "agg_color": {
            "terms": {
                "field": "color"
            },
            "aggregations": {
                "agg_avg_price": {
                    "avg": {
                        "field": "price"
                    }
                },
                "agg_brand": {
                    "terms": {
                        "field": "brand"
                    },
                    "aggregations": {
                        "agg_avg_price": {
                            "avg": {
                                "field": "price"
                            }
                        }
                    }
                }
            }
        },
        "agg_avg_price": {
            "avg": {
                "field": "price"
            }
        }
    }
}
```

```php
$avgPriceAggregation = new AvgAggregation('avg_price');
$avgPriceAggregation->setField('price');

$brandTermAggregation = new TermsAggregation('brand');
$brandTermAggregation->setField('brand');
$brandTermAggregation->addAggregation($avgPriceAggregation);

$colorTermsAggregation = new TermsAggregation('color');
$colorTermsAggregation->setField('color');
$colorTermsAggregation->addAggregation($avgPriceAggregation);
$colorTermsAggregation->addAggregation($brandTermAggregation);

$search = new Search();
$search->addAggregation($colorTermsAggregation);
$search->addAggregation($avgPriceAggregation);

$queryArray = $search->toArray();
```

## Metric Aggregations
 - [Avg](Avg.md)
 - [Cardinality](Cardinality.md)
 - [ExtendedStats](ExtendedStats.md)
 - [Max](Max.md)
 - [Min](Min.md)
 - [PercentileRanks](PercentileRanks.md)
 - [Percentiles](Percentiles.md)
 - [Stats](Stats.md)
 - [Sum](Sum.md)
 - [TopHits](TopHits.md)
 - [ValueCount](ValueCount.md)
 
## Bucketing Aggregations
 - [Children](Children.md)
 - [DateRange](DateRange.md)
 - [Filter](Filter.md)
 - [Filters](Filters.md)
 - [GeoBounds](GeoBounds.md)
 - [GeoDistance](GeoDistance.md)
 - [GeoHashGrid](GeoHashGrid.md)
 - [Global](Global.md)
 - [Histogram](Histogram.md)
 - [Ipv4Range](Ipv4Range.md)
 - [Missing](Missing.md)
 - [Nested](Nested.md)
 - [Range](Range.md)
 - [ReverseNested](ReverseNested.md)
 - [Terms](Terms.md)

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations.html
