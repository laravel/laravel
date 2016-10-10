CHANGELOG
=========
   
v2.x (2016-x)
---

v2.0.0 (2016-03-03)
---

- [BC break] Aggregation name is not prefixed anymore
- [BC break] Removed all filters and filtered query
- [BC break] Query's `toArray()` now returns array WITH query type
- [Feature] Added TermSuggest and Suggest endpoint

v1.1.2 (2016-02-01)
---

- Deprecated `FuzzyLikeThisQuery` and `FuzzyLikeThisFieldQuery` queries

v1.1.1 (2016-01-26)
---

- Fixed query endpoint normalization when called repeatedly [#56](https://github.com/ongr-io/ElasticsearchDSL/pull/56)
- Deprecated `DslTypeAwareTrait` and `FilterOrQueryDetectionTrait` traits

v1.1.0 (2015-12-28)
---

- Fixed nested query in case `bool` with single `must` clause given [#32](https://github.com/ongr-io/ElasticsearchDSL/issues/32)
- Deprecated all filters and filtered query [#50](https://github.com/ongr-io/ElasticsearchDSL/issues/50)
- Added `filter` clause support for `BoolQuery` [#48](https://github.com/ongr-io/ElasticsearchDSL/issues/48)

v1.0.1 (2015-12-16)
---

- Fixed `function_score` query options handling [#35](https://github.com/ongr-io/ElasticsearchDSL/issues/35)
- Added Symfony 3 compatibility
- Added support for `timeout` and `terminate_after` options in Search endpoint [#34](https://github.com/ongr-io/ElasticsearchDSL/issues/34)
