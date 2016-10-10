# Fuzzy Query

> More info about fuzzy query is in the [official elasticsearch docs][1]

The fuzzy query uses similarity based on Levenshtein edit distance for string fields, and a +/- margin on numeric and
date fields.

## Simple example

```JSON
{
    "fuzzy" : { "user" : "ki" }
}
```

In DSL:

```php
$fuzzyQuery = new FuzzyQuery('user', 'ki');

$search = new Search();
$search->addQuery($fuzzyQuery);

$queryArray = $search->toArray();
```

## With more advanced settings

```JSON
{
    "fuzzy" : {
        "user" : {
            "value" :         "ki",
            "boost" :         1.0,
            "fuzziness" :     2,
            "prefix_length" : 0,
            "max_expansions": 100
        }
    }
}
```

In DSL
```php
$fuzzyQuery = new FuzzyQuery(
    'user',
    'ki',
    [
        'boost' => 1,
        'fuzziness' => 2,
        'prefix_length' => 0,
        'max_expansions' => 100,
    ]
);


$search = new Search();
$search->addQuery($fuzzyQuery);

$queryArray = $search->toArray();
```

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html
