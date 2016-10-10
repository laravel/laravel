# Function Score Query

> More info about function score query is in the [official elasticsearch docs][1]

The function score query allows you to modify the score of documents that are retrieved by a query. This can be useful
if, for example, a score function is computationally expensive and it is sufficient to compute the score on a filtered
set of documents.

## Example with linear decay function
```JSON
{
  "query": {
    "function_score": {
      "query": {
        "range": {
          "price": {
            "gte": 10,
            "lte": 100
          }
        }
      },
      "functions": [
        {
          "linear": {
            "price": {
              "origin": 10,
              "scale": 50,
              "offset": 0,
              "decay" : 0.5
            }
          }
        }
      ]
    }
  }
}
```

In DSL

```php
$rangeQuery = new RangeQuery('price');
$rangeQuery->addParameter(RangeQuery::GTE, 10);
$rangeQuery->addParameter(RangeQuery::LTE, 100);
$functionScoreQuery = new FunctionScoreQuery($rangeQuery);
$functionScoreQuery->addDecayFunction(
    'linear',
    'price',
    [
        'origin' => 10,
        'scale' => 50,
        'offset' => 0,
        'decay' => 0.5
    ]
);

$search = new Search();
$search->addQuery($functionScoreQuery);

$queryArray = $search->toArray();
```

## Example weight function to multiply score of subset

```JSON
{
  "query": {
    "function_score": {
      "query": {
        "match_all": {}
      },
      "functions": [
        {
          "weight": 2,
          "filter": {
            "range": {
              "price": {
                "gte": 10,
                "lte": 40
              }
            }
          }
        }
      ]
    }
  }
}
```

In DSL:

```php
$functionScoreQuery = new FunctionScoreQuery(new MatchAllQuery());
$rangeFilter = new RangeQuery('price', ['gte' => 10, 'lte' => 100]);
$functionScoreQuery->addWeightFunction(2, $rangeFilter);

$search = new Search();
$search->addQuery($functionScoreQuery);

$queryArray = $search->toArray();
```

## Example Field Value Factor Function

```php
$functionScoreQuery = new FunctionScoreQuery(new MatchAllQuery());
$existsQuery = new ExistsQuery('price');
$functionScoreQuery->addFieldValueFactorFunction('price', 0.5, 'ln', $existsQuery);

$search = new Search();
$search->addQuery($functionScoreQuery);

$queryArray = $search->toArray();
```

Will result in 

```JSON
{
  "query": {
    "function_score": {
      "query": {
        "match_all": {}
      },
      "functions": [
        {
          "field_value_factor": {
            "field": "price",
            "factor": 0.5,
            "modifier": "ln"
          },
          "filter": {
            "exists": {
              "field": "price"
            }
          }
        }
      ]
    }
  }
}
```

## Random function example

```php
$functionScoreQuery = new FunctionScoreQuery(new MatchAllQuery());
$functionScoreQuery->addRandomFunction();

$search = new Search();
$search->addQuery($functionScoreQuery);

$queryArray = $search->toArray();
```

Will result in

```JSON
{
  "query": {
    "function_score": {
      "query": {
        "match_all": {}
      },
      "functions": [
        {
          "random_score": {}
        }
      ]
    }
  }
}
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
