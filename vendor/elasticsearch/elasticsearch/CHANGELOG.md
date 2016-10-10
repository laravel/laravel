## Release 2.1.5
- whitelist search per-request cache parameters [[8105a9e]](http://github.com/elasticsearch/elasticsearch-php/commit/8105a9e)
- Add 'routing' parameter to Mget endpoint whitelist [[bb0a623]](http://github.com/elasticsearch/elasticsearch-php/commit/bb0a623)
- Add 'percolate_format' parameter to Percolate endpoint whitelist [[65462ba]](http://github.com/elasticsearch/elasticsearch-php/commit/65462ba)
- Remove $scroll_id variable entirely Set $body = $scroll_id in setScrollID() Return $body in getBody() [[05999d8]](http://github.com/elasticsearch/elasticsearch-php/commit/05999d8)
- Add update_all_types support to create index call [[6dcf4c5]](http://github.com/elasticsearch/elasticsearch-php/commit/6dcf4c5)
- Allow update_all_types as index setting [[4161290]](http://github.com/elasticsearch/elasticsearch-php/commit/4161290)
- Use request body for scroll ID instead of URI param [[016c191]](http://github.com/elasticsearch/elasticsearch-php/commit/016c191)

### Testing
- Update snapshot download script for 2.2 [[350cd6e]](http://github.com/elasticsearch/elasticsearch-php/commit/350cd6e)
- Update travis matrix [[04bcf81]](http://github.com/elasticsearch/elasticsearch-php/commit/04bcf81)
- Invoke phpunit dependency instead of travis phpunit.phar [[f9e0d99]](http://github.com/elasticsearch/elasticsearch-php/commit/f9e0d99)
- Do not track the util/elasticsearch directory [[25bd111]](http://github.com/elasticsearch/elasticsearch-php/commit/25bd111)

### Documentation
- Fix comment tag [[3b8e918]](http://github.com/elasticsearch/elasticsearch-php/commit/3b8e918)
- Add .github templates [[104a7ea]](http://github.com/elasticsearch/elasticsearch-php/commit/104a7ea)
- Minor fix to wrong documentation [[2863521]](http://github.com/elasticsearch/elasticsearch-php/commit/2863521)
- Wrong syntax in bulk index example [[17eb43a]](http://github.com/elasticsearch/elasticsearch-php/commit/17eb43a)
- Delete configuration.asciidoc~ [[3449d97]](http://github.com/elasticsearch/elasticsearch-php/commit/3449d97)
- Syntax errors in Exception handling [[ba5b38b]](http://github.com/elasticsearch/elasticsearch-php/commit/ba5b38b)
- Elasticsearch needed for autoload to find classes [[9410e14]](http://github.com/elasticsearch/elasticsearch-php/commit/9410e14)
- Small doc fix in futures doc [[86e559a]](http://github.com/elasticsearch/elasticsearch-php/commit/86e559a)
- Fix incorrect nesting of array [[3e041f3]](http://github.com/elasticsearch/elasticsearch-php/commit/3e041f3)


## Release 2.1.4
- Fix the host path handling [[15b5be3]](http://github.com/elasticsearch/elasticsearch-php/commit/15b5be3)
- fix body setter in bulk endpoint [[fa283ea]](http://github.com/elasticsearch/elasticsearch-php/commit/fa283ea)
- add support for generators and iterators in bulk endpoint [[b3d951e]](http://github.com/elasticsearch/elasticsearch-php/commit/b3d951e)

### Testing
- travis: drop PHP 7.0 from allowed failures, it passes well [[be5e710]](http://github.com/elasticsearch/elasticsearch-php/commit/be5e710)


## Release 2.1.3
- Fix bug where ping() and sniff() encounter NPE [[61ba0c5]](http://github.com/elasticsearch/elasticsearch-php/commit/61ba0c5)
- Add Indices/ForceMerge endpoint  [[4934583]](http://github.com/elasticsearch/elasticsearch-php/commit/4934583) [[6d61880]](http://github.com/elasticsearch/elasticsearch-php/commit/6d61880) [[58e63d7]](http://github.com/elasticsearch/elasticsearch-php/commit/58e63d7) [[dcd8833]](http://github.com/elasticsearch/elasticsearch-php/commit/dcd8833)
- Add Cat/Snapshots endpoint [[efa0e49]](http://github.com/elasticsearch/elasticsearch-php/commit/efa0e49)
- Add Cat/Repositories endpoint [[0c32b20]](http://github.com/elasticsearch/elasticsearch-php/commit/0c32b20)
- Add Cat/NodeAttrs endpoint [[724d64b]](http://github.com/elasticsearch/elasticsearch-php/commit/724d64b) [[4c3bde3]](http://github.com/elasticsearch/elasticsearch-php/commit/4c3bde3)
- Implement \Countable interface for Scroll Helper. [[013a1c7]](http://github.com/elasticsearch/elasticsearch-php/commit/013a1c7) [[17a929f]](http://github.com/elasticsearch/elasticsearch-php/commit/17a929f) [[272429a]](http://github.com/elasticsearch/elasticsearch-php/commit/272429a) [[55f4c0c]](http://github.com/elasticsearch/elasticsearch-php/commit/55f4c0c)
- Allows overwriting setters invoked from ClientBuilder::fromConfig method. [[08ac47c]](http://github.com/elasticsearch/elasticsearch-php/commit/08ac47c)
- When using connectionPool setter through ClientBuilder::fromConfig() method, connectionPoolArgs were automatically set to null causing AbstractConnectionPool to throw exception on null parameter. [[0b25033]](http://github.com/elasticsearch/elasticsearch-php/commit/0b25033)
- Ignore 404 for already cleared scroll_ids in SearchResponseIterator [[266aac4]](http://github.com/elasticsearch/elasticsearch-php/commit/266aac4)
- Add 'wait_if_ongoing' to Indices/Flush whitelist [[7b9cf8c]](http://github.com/elasticsearch/elasticsearch-php/commit/7b9cf8c)
- Set randomizeHosts = true by default [[dc91938]](http://github.com/elasticsearch/elasticsearch-php/commit/dc91938)
- Update Cat/Shards whitelist [[fe1dbe8]](http://github.com/elasticsearch/elasticsearch-php/commit/fe1dbe8)

### Documentation
- PHPdoc fix: Exists endpoint returns bool or array [[d70acbf]](http://github.com/elasticsearch/elasticsearch-php/commit/d70acbf)
- fixed wrong return param in phpdoc [[56a1107]](http://github.com/elasticsearch/elasticsearch-php/commit/56a1107)
- Fix formatting [[c548337]](http://github.com/elasticsearch/elasticsearch-php/commit/c548337)
- Update CatNamespace.php [[22cb3dc]](http://github.com/elasticsearch/elasticsearch-php/commit/22cb3dc)

### Testing
- [TEST] Update skiplist [[22e7cc5]](http://github.com/elasticsearch/elasticsearch-php/commit/22e7cc5)
- [TRAVIS] Add node attribute for nodeattr tests [[0aa9638]](http://github.com/elasticsearch/elasticsearch-php/commit/0aa9638)
- [TEST] cleanup cat repos [[bed0338]](http://github.com/elasticsearch/elasticsearch-php/commit/bed0338)
- [Travis] update snapshot handling [[1e6fa0c]](http://github.com/elasticsearch/elasticsearch-php/commit/1e6fa0c)

## Release 2.1.2

- Remove IntrospectionProcessor from default to make logs less noisy [[0a80de4]](http://github.com/elasticsearch/elasticsearch-php/commit/0a80de4)
- Remove redundant logging on failure [[f31a211]](http://github.com/elasticsearch/elasticsearch-php/commit/f31a211)

## Release 2.1.1

- More robust error logging during connection failure / retries [[0069fd6]](http://github.com/elasticsearch/elasticsearch-php/commit/0069fd6)

## Release 2.1.0
- Log failure when encountering non-retry hard curl exception [[8e7e03f]](http://github.com/elasticsearch/elasticsearch-php/commit/8e7e03f)
- Embed a MaxRetriesException inside a TransportException when thrown due to retries [[deff12f]](http://github.com/elasticsearch/elasticsearch-php/commit/deff12f)
- Add a SimpleConnectionPool [[4a3df40]](http://github.com/elasticsearch/elasticsearch-php/commit/4a3df40)
- If body is null/empty, return string instead of trying to decode [[c4f9d41]](http://github.com/elasticsearch/elasticsearch-php/commit/c4f9d41)
- Accept `field` on GetFieldMapping endpoint for bwc [[6ecf35e]](http://github.com/elasticsearch/elasticsearch-php/commit/6ecf35e)
- Fix typo in GetFieldMapping endpoint (field -> fields) [[2b323c4]](http://github.com/elasticsearch/elasticsearch-php/commit/2b323c4)
- Ping should return false on Transport/Connection errors [[699d9dd]](http://github.com/elasticsearch/elasticsearch-php/commit/699d9dd)

### Documentation Related
- [Docs] Clarify NoNodesAvailableException [[c0d6332]](http://github.com/elasticsearch/elasticsearch-php/commit/c0d6332)
- [Docs] Fix missing code block [[6793ad4]](http://github.com/elasticsearch/elasticsearch-php/commit/6793ad4)
- [Docs] Fix connection pool configuration docs [[26edc73]](http://github.com/elasticsearch/elasticsearch-php/commit/26edc73)
- [Docs] Add docs about per-request timeouts [[d5be83f]](http://github.com/elasticsearch/elasticsearch-php/commit/d5be83f)
- [Docs] Add missing subtitle [[22b51af]](http://github.com/elasticsearch/elasticsearch-php/commit/22b51af)
- [Docs] add elasticsearcher to Community page [[b3dd9a2]](http://github.com/elasticsearch/elasticsearch-php/commit/b3dd9a2)
- Update readme about versions [[8ad7709]](http://github.com/elasticsearch/elasticsearch-php/commit/8ad7709)
- Remove accidentally added log file [[0f43a5c]](http://github.com/elasticsearch/elasticsearch-php/commit/0f43a5c)

### Travis and Testing Related
- Blacklist old 0.4 branch from travis [[ea8f775]](http://github.com/elasticsearch/elasticsearch-php/commit/ea8f775)
- [Travis] Add 2.1 to travis matrix [[2f6d20f]](http://github.com/elasticsearch/elasticsearch-php/commit/2f6d20f)
- Update crud.asciidoc [[4a679b7]](http://github.com/elasticsearch/elasticsearch-php/commit/4a679b7)
- Readme: Travis badge added [[dce31b3]](http://github.com/elasticsearch/elasticsearch-php/commit/dce31b3)
- [TESTS] Small tweak to make test runner PHP-7 compatible [[47ae254]](http://github.com/elasticsearch/elasticsearch-php/commit/47ae254)
- [TESTS] temporary fix for cleaning up repos after tests [[d56da4b]](http://github.com/elasticsearch/elasticsearch-php/commit/d56da4b)
- travis: add path.repo to config [[6576184]](http://github.com/elasticsearch/elasticsearch-php/commit/6576184)
- [TESTS] Clear snapshots [[680cb90]](http://github.com/elasticsearch/elasticsearch-php/commit/680cb90)
- [TESTS] Simplify exception matching and handling [[eba21eb]](http://github.com/elasticsearch/elasticsearch-php/commit/eba21eb)
- Add RequestTimeout408 exception [[9d70653]](http://github.com/elasticsearch/elasticsearch-php/commit/9d70653)
- [TESTS] debug output tweaks [[9276e5e]](http://github.com/elasticsearch/elasticsearch-php/commit/9276e5e)
- travis: update version matrix [[6d47211]](http://github.com/elasticsearch/elasticsearch-php/commit/6d47211)
- [TESTS] use multiHandler() in tests, since singleHandler is not available in older versions of PHP [[e7fa5c8]](http://github.com/elasticsearch/elasticsearch-php/commit/e7fa5c8)
- travis: more find, less glob [[604e71a]](http://github.com/elasticsearch/elasticsearch-php/commit/604e71a)
- travis: more script tweaks [[cca9216]](http://github.com/elasticsearch/elasticsearch-php/commit/cca9216)
- travis: more script tweaks [[c92a5ad]](http://github.com/elasticsearch/elasticsearch-php/commit/c92a5ad)
- travis: add repo to config for snapshot tests [[d17f1e1]](http://github.com/elasticsearch/elasticsearch-php/commit/d17f1e1)
- travis: PHP 5.3 is no longer supported [[9eafee1]](http://github.com/elasticsearch/elasticsearch-php/commit/9eafee1)
- travis: Just kidding, try glob path instead [[214b0e7]](http://github.com/elasticsearch/elasticsearch-php/commit/214b0e7)
- travis: hardcode unzipping dir [[fe96602]](http://github.com/elasticsearch/elasticsearch-php/commit/fe96602)
- travis: flush output of RestSpecRunner [[bcefcff]](http://github.com/elasticsearch/elasticsearch-php/commit/bcefcff)
- travis: use snapshot builds per-branch instead of release versions [[18e4e72]](http://github.com/elasticsearch/elasticsearch-php/commit/18e4e72)
- travis: Rest-Spec should be obtained after install [[59546cf]](http://github.com/elasticsearch/elasticsearch-php/commit/59546cf)
- [Tests] Delete unreliable, bad tests [[31eb9e0]](http://github.com/elasticsearch/elasticsearch-php/commit/31eb9e0)
- travis: Add util script to ensure cluster is alive before proceeding [[563412a]](http://github.com/elasticsearch/elasticsearch-php/commit/563412a)
- travis: Use bwc startup options [[a8e63a5]](http://github.com/elasticsearch/elasticsearch-php/commit/a8e63a5)
- travis: better startup options for Elasticsearch [[d6624a2]](http://github.com/elasticsearch/elasticsearch-php/commit/d6624a2)
- travis: Set executable flag on shell script [[4b9fbcd]](http://github.com/elasticsearch/elasticsearch-php/commit/4b9fbcd)
- travis: typo in download shell script name [[3e0141a]](http://github.com/elasticsearch/elasticsearch-php/commit/3e0141a)
- Remove old, unused submodule [[c6e7570]](http://github.com/elasticsearch/elasticsearch-php/commit/c6e7570)
- travis: run RestSpecRunner before tests [[5355ec8]](http://github.com/elasticsearch/elasticsearch-php/commit/5355ec8)

## Release 2.0.3
- Prefer root_cause reason over the main reason if available [[aa2e313]](http://github.com/elasticsearch/elasticsearch-php/commit/aa2e313)
- Validate that index/type/id are non-null, non-empty when deleting [[8072aee]](http://github.com/elasticsearch/elasticsearch-php/commit/8072aee)
- Attempt to decode 400-level exceptions too [[9ab8904]](http://github.com/elasticsearch/elasticsearch-php/commit/9ab8904)
- [Docs] Header links are hard to see, make dedicated link [[7919ead]](http://github.com/elasticsearch/elasticsearch-php/commit/7919ead)
- Merge pull request #317 from ssm2003/patch-3 [[4147f16]](http://github.com/elasticsearch/elasticsearch-php/commit/4147f16)
- Merge pull request #316 from ssm2003/patch-2 [[d2b7552]](http://github.com/elasticsearch/elasticsearch-php/commit/d2b7552)
- Merge pull request #315 from ssm2003/patch-1 [[ad5f113]](http://github.com/elasticsearch/elasticsearch-php/commit/ad5f113)
- Populate lastRequestInfo with request/response [[68bfd10]](http://github.com/elasticsearch/elasticsearch-php/commit/68bfd10)
- [Docs] Add community integration page [[bcffa45]](http://github.com/elasticsearch/elasticsearch-php/commit/bcffa45)
- Merge pull request #313 from machour/patch-1 [[31137b3]](http://github.com/elasticsearch/elasticsearch-php/commit/31137b3)
- Fixed the bulk indexed with batches example [[80b7bdc]](http://github.com/elasticsearch/elasticsearch-php/commit/80b7bdc)


## Release 2.0.2
- Use curl opts for auth instead of inline syntax [[4b01af8]](http://github.com/elasticsearch/elasticsearch-php/commit/4b01af8)

## Release 2.0.1
- Add ClientBuilder::FromConfig to allow easier automated building of Client [[a07486d]](http://github.com/elasticsearch/elasticsearch-php/commit/a07486d)

## Release 2.0.0
- Added helper iterators for scrolled search [[24598e7]](http://github.com/elasticsearch/elasticsearch-php/commit/24598e7)
- comoposer command error [[8ed2885]](http://github.com/elasticsearch/elasticsearch-php/commit/8ed2885)

## Release 2.0.0-beta5
- [TESTS] Fix mistake due to poor cherry-picking skills :) [[1bae4ed]](http://github.com/elasticsearch/elasticsearch-php/commit/1bae4ed)
- Add Indices/ShardStores endpoint [[aa0f13f]](http://github.com/elasticsearch/elasticsearch-php/commit/aa0f13f)
- Add 'fields' param to Bulk Endpoint [[d434bf0]](http://github.com/elasticsearch/elasticsearch-php/commit/d434bf0)
- Add RenderSearchTemplate Endpoint [[dfd041d]](http://github.com/elasticsearch/elasticsearch-php/commit/dfd041d)
- Add `filter_path` to global param whitelist [[0943f73]](http://github.com/elasticsearch/elasticsearch-php/commit/0943f73)
- [TEST] Fix bug where inline stash replacements were not being honored [[a4d162e]](http://github.com/elasticsearch/elasticsearch-php/commit/a4d162e)
- [TEST] Fix stash replacement to make PHP 5.3 happy [[91a6e76]](http://github.com/elasticsearch/elasticsearch-php/commit/91a6e76)
- Add alias for TermVectors -> TermVector [[c4330e8]](http://github.com/elasticsearch/elasticsearch-php/commit/c4330e8)
- [TEST] fix bug where array_key_exists explodes due to doubles [[1b071b5]](http://github.com/elasticsearch/elasticsearch-php/commit/1b071b5)
- Add ''fielddata_fields' and 'filter_path'' param to Search whitelist [[2c2632f]](http://github.com/elasticsearch/elasticsearch-php/commit/2c2632f)
- Add 'human' param to Indices/Get whitelist [[e66197f]](http://github.com/elasticsearch/elasticsearch-php/commit/e66197f)
- Add Indices/GetUpgrade endpoint [[8bc2124]](http://github.com/elasticsearch/elasticsearch-php/commit/8bc2124)
- Add Indices/Upgrade endpoint [[138be12]](http://github.com/elasticsearch/elasticsearch-php/commit/138be12)
- Add Indices/flushSynced endpoint [[04d909b]](http://github.com/elasticsearch/elasticsearch-php/commit/04d909b)
- Add more missing query-string parameters [[a34c725]](http://github.com/elasticsearch/elasticsearch-php/commit/a34c725)
- Add missing query-string parameters [[f59a521]](http://github.com/elasticsearch/elasticsearch-php/commit/f59a521)
- Add missing whitelist parameters to SearchTemplate endpoint [[48aed04]](http://github.com/elasticsearch/elasticsearch-php/commit/48aed04)
- [TESTS] Add workaround for rest-spec package path change in core [[5f3939e]](http://github.com/elasticsearch/elasticsearch-php/commit/5f3939e)
- Add 'fields' parameter to FieldStats endpoint [[49a7ef4]](http://github.com/elasticsearch/elasticsearch-php/commit/49a7ef4)
- FieldStats endpoint accepts a body [[79a47cb]](http://github.com/elasticsearch/elasticsearch-php/commit/79a47cb)
- Add Cat/Segments Endpoint [[04a551f]](http://github.com/elasticsearch/elasticsearch-php/commit/04a551f)
- NoDocumentsToGetException needs nested original exception [[4b7e14e]](http://github.com/elasticsearch/elasticsearch-php/commit/4b7e14e)
- Fix error string handling when creating Exceptions [[f22c6c2]](http://github.com/elasticsearch/elasticsearch-php/commit/f22c6c2)


## Release 2.0.0-beta4
- [DOCS] Fix bulk example [[fce4848]](http://github.com/elasticsearch/elasticsearch-php/commit/fce4848)
- Add Indices/Seal endpoint [[a08252c]](http://github.com/elasticsearch/elasticsearch-php/commit/a08252c)
- [TEST] Fix YamlRunner to correctly stash values in object hierarchies [[2916727]](http://github.com/elasticsearch/elasticsearch-php/commit/2916727)
- Add SearchExists Endpoint [[a47eb67]](http://github.com/elasticsearch/elasticsearch-php/commit/a47eb67)
- Add more missing query-string parameters [[03bdb78]](http://github.com/elasticsearch/elasticsearch-php/commit/03bdb78)
- Add missing query-string parameters [[6efdedd]](http://github.com/elasticsearch/elasticsearch-php/commit/6efdedd)
- (pr/232) fix Warning for empty body when HEAD request - check if index exists [[e6fa2da]](http://github.com/elasticsearch/elasticsearch-php/commit/e6fa2da)

## Release 2.0.0-beta3

-  (HEAD, origin/2.0, 2.0) Add missing whitelist parameters to SearchTemplate endpoint [[74a7ca5]](http://github.com/elasticsearch/elasticsearch-php/commit/74a7ca5)
-  [TEST] Update version parsing to handle new format [[b86fca5]](http://github.com/elasticsearch/elasticsearch-php/commit/b86fca5)
-  Add FieldStats endpoint [[4935fd9]](http://github.com/elasticsearch/elasticsearch-php/commit/4935fd9)
-  Merge pull request #217 from simplechris/patch-4 [[5a92117]](http://github.com/elasticsearch/elasticsearch-php/commit/5a92117)
-  Add PSR-2 check to the contributing guidelines [[2ad3609]](http://github.com/elasticsearch/elasticsearch-php/commit/2ad3609)
-  Add .php_cs file [[17d0eac]](http://github.com/elasticsearch/elasticsearch-php/commit/17d0eac)
-  Add missing docblocks [[7305d45]](http://github.com/elasticsearch/elasticsearch-php/commit/7305d45)
-  Misc cleanup [[35cb77e]](http://github.com/elasticsearch/elasticsearch-php/commit/35cb77e)
-  Normalize blank lines between methods [[0f2cdae]](http://github.com/elasticsearch/elasticsearch-php/commit/0f2cdae)
-  Add blank line before 'return' statements to aid readability [[fabd2cc]](http://github.com/elasticsearch/elasticsearch-php/commit/fabd2cc)
-  Normalize spacing after explicit type casting [[057b1cd]](http://github.com/elasticsearch/elasticsearch-php/commit/057b1cd)
-  Remove unused 'use' statements [[f6b2882]](http://github.com/elasticsearch/elasticsearch-php/commit/f6b2882)
-  Normalize Elasticsearch\Common\Exception docblocks [[0a3a7ec]](http://github.com/elasticsearch/elasticsearch-php/commit/0a3a7ec)
-  Remove NamespaceFutureUtil trait [[9a75582]](http://github.com/elasticsearch/elasticsearch-php/commit/9a75582)
-  Remove commented-out debugging [[21c2278]](http://github.com/elasticsearch/elasticsearch-php/commit/21c2278)
-  Normalize docblocks [[eaf18e2]](http://github.com/elasticsearch/elasticsearch-php/commit/eaf18e2)
-  Remove redundant comments [[5c304a2]](http://github.com/elasticsearch/elasticsearch-php/commit/5c304a2)
-  Use PSR-2 coding standard [[ad3db43]](http://github.com/elasticsearch/elasticsearch-php/commit/ad3db43)
-  [TEST] Populate response body with exception message so that it can be verified [[65f1676]](http://github.com/elasticsearch/elasticsearch-php/commit/65f1676)
-  Merge pull request #220 from simplechris/fix/client-builder-handler-selection [[563124d]](http://github.com/elasticsearch/elasticsearch-php/commit/563124d)
-  ClientBuilder fixes for PHP 5.4 [[25eaa71]](http://github.com/elasticsearch/elasticsearch-php/commit/25eaa71)
-  Remove save_to streaming functionality - not needed with curl handlers [[f32e038]](http://github.com/elasticsearch/elasticsearch-php/commit/f32e038)
-  Merge pull request #216 from simplechris/patch-2 [[6cbe2fe]](http://github.com/elasticsearch/elasticsearch-php/commit/6cbe2fe)
-  Add missing use statements [[58527d5]](http://github.com/elasticsearch/elasticsearch-php/commit/58527d5)

## Release 2.0.0-beta2

- `curl_reset` was added in 5.5, use `curl_multi_init` to check for curl instead [[b722802]](http://github.com/elasticsearch/elasticsearch-php/commit/b722802)
- Update Composer.json with PSR-4 [[7bfd251]](http://github.com/elasticsearch/elasticsearch-php/commit/7bfd251)
- Add Cat\Plugins endpoint [[3044da9]](http://github.com/elasticsearch/elasticsearch-php/commit/3044da9)
- Indices/Stats endpoint should implode multi-valued metric parameters into a single string [[c2097b9]](http://github.com/elasticsearch/elasticsearch-php/commit/c2097b9)
- Fix typo in 'metric' argument of Stats function in IndicesNamespace.php [[db85afb]](http://github.com/elasticsearch/elasticsearch-php/commit/db85afb)
