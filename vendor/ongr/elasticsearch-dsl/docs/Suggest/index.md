# Suggest

Objective suggest builder represents [Elasticsearch Term suggest][1].

To form a suggest you have to create `Search` object. See below an example of suggest usage.

```php
$search = new Search();
$suggest = new Suggest('my_suggest', 'searchText', ['field' => 'title', 'size' => 5]);
$search->addSuggest($suggest);
$queryArray = $search->toArray();
```

That will generate following JSON:

```JSON
"suggest": {
  "my_suggest": {
    "text": "searchText",
    "term": {
      "field": "title",
      "size": 5
    }
  }
}
```

You're able to create more than one suggest:

```php
$search = new Search();
$suggest1 = new Suggest('my_suggest1', 'the amsterdma meetpu', ['field' => 'body', 'size' => 5]);
$search->addSuggest($suggest1);
$suggest2 = new Suggest('my_suggest2', 'the rottredam meetpu', ['field' => 'title', 'size' => 5]);
$search->addSuggest($suggest2);
$queryArray = $search->toArray();
```

That will generate following JSON:

```JSON
"suggest": {
  "my_suggest1": {
    "text": "the amsterdma meetpu",
    "term": {
      "field": "body",
      "size": 5
    }
  },
  "my_suggest2": {
    "text": "the rottredam meetpu",
    "term": {
      "field": "title",
      "size": 5
    }
  }
}
```

If parameters `field` or `size` are not provided they will have default values, `field = _all` and `size = 3`

Find available parameters in [Elasticsearch Term suggest documentation][1]

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters-term.html