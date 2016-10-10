elasticsearch-php
=================

[![Build Status](https://img.shields.io/travis/elastic/elasticsearch-php.svg?style=flat-square)](https://travis-ci.org/elastic/elasticsearch-php)


Official low-level client for Elasticsearch. Its goal is to provide common ground for all Elasticsearch-related code in PHP; because of this it tries to be opinion-free and very extendable.

To maintain consistency across all the low-level clients (Ruby, Python, etc), clients accept simple associative arrays as parameters.  All parameters, from the URI to the document body, are defined in the associative array.


Features
--------

 - One-to-one mapping with REST API and other language clients
 - Configurable, automatic discovery of cluster nodes
 - Persistent, Keep-Alive connections (within the lifetime of the script)
 - Load balancing (with pluggable selection strategy) across all available nodes. Defaults to round-robin
 - Pluggable connection pools to offer different connection strategies
 - Generalized, pluggable architecture - most components can be replaced with your own custom class if specialized behavior is required
 - Option to use asyncronous future, which enables parallel execution of curl requests to multiple nodes

Version Matrix
--------------

| Elasticsearch Version | Elasticsearch-PHP Branch |
| --------------------- | ------------------------ |
| >= 2.0                | 1.0 or 2.                |
| >= 1.0, < 2.0         | 1.0 or 2.0               |
| <= 0.90.x             | 0.4                      |

 - If you are using Elasticsearch 2.0+, prefer using the Elasticsearch-PHP 2.0 branch.  The 1.0 branch is compatible however.
 - If you are using Elasticsearch 1.0+, you must install the `1.0` or `2.0` Elasticsearch-PHP branch.
 - If you are using a version older than 1.0, you must install the `0.4` Elasticsearch-PHP branch. Since ES 0.90.x and below is now EOL, the corresponding `0.4` branch will not receive any more development or bugfixes.  Please upgrade.
 - You should never use Elasticsearch-PHP Master branch, as it tracks Elasticearch master and may contain incomplete features or breaks in backwards compat.  Only use ES-PHP master if you are developing against ES master for some reason.

Documentation
--------------
[Full documentation can be found here.](http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/2.0/index.html)  Docs are stored within the repo under /docs/, so if you see a typo or problem, please submit a PR to fix it!

Installation via Composer
-------------------------
The recommended method to install _Elasticsearch-PHP_ is through [Composer](http://getcomposer.org).

1. Add ``elasticsearch/elasticsearch`` as a dependency in your project's ``composer.json`` file (change version to suit your version of Elasticsearch):

    ```json
        {
            "require": {
                "elasticsearch/elasticsearch": "~2.0"
            }
        }
    ```

2. Download and install Composer:

    ```bash
        curl -s http://getcomposer.org/installer | php
    ```

3. Install your dependencies:

    ```bash
        php composer.phar install --no-dev
    ```

4. Require Composer's autoloader

    Composer also prepares an autoload file that's capable of autoloading all of the classes in any of the libraries that it downloads. To use it, just add the following line to your code's bootstrap process:

    ```php
        <?php
        require 'vendor/autoload.php';

        $client = ClientBuilder::create()->build();
    ```
You can find out more on how to install Composer, configure autoloading, and other best-practices for defining dependencies at [getcomposer.org](http://getcomposer.org).

You'll notice that the installation command specified `--no-dev`.  This prevents Composer from installing the various testing and development dependencies.  For average users, there is no need to install the test suite (which also includes the complete source code of Elasticsearch).  If you wish to contribute to development, just omit the `--no-dev` flag to be able to run tests.

PHP Version Requirement
----
Version 2.0 of this library requires at least PHP version 5.4.0 to function.  If you are on an older version of PHP, it is recommended
that you upgrade, as PHP 5.3 is official EOL.  Elasticsearch-PHP v0.4.x and v1.x are compatible with PHP 5.3.9+, but will
eventually stop being supported.

| PHP Version | Elasticsearch-PHP Branch |
| ----------- | ------------------------ |
| >= 5.4.0    | 2.0                      |
| >= 5.3.9    | 0.4, 1.0                 |

Quickstart
----


### Index a document

In elasticsearch-php, almost everything is configured by associative arrays.  The REST endpoint, document and optional parameters - everything is an associative array.

To index a document, we need to specify four pieces of information: index, type, id and a document body. This is done by
constructing an associative array of key:value pairs.  The request body is itself an associative array with key:value pairs
corresponding to the data in your document:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',
    'body' => ['testField' => 'abc']
];

$response = $client->index($params);
print_r($response);
```

The response that you get back indicates the document was created in the index that you specified.  The response is an
associative array containing a decoded version of the JSON that Elasticsearch returns:

```php
Array
(
    [_index] => my_index
    [_type] => my_type
    [_id] => my_id
    [_version] => 1
    [created] => 1
)

```

### Get a document

Let's get the document that we just indexed.  This will simply return the document:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];

$response = $client->get($params);
print_r($response);
```

The response contains some metadata (index, type, etc) as well as a `_source` field...this is the original document
that you sent to Elasticsearch.

```php
Array
(
    [_index] => my_index
    [_type] => my_type
    [_id] => my_id
    [_version] => 1
    [found] => 1
    [_source] => Array
        (
            [testField] => abc
        )

)
```

### Search for a document

Searching is a hallmark of Elasticsearch, so let's perform a search.  We are going to use the Match query as a demonstration:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'body' => [
        'query' => [
            'match' => [
                'testField' => 'abc'
            ]
        ]
    ]
];

$response = $client->search($params);
print_r($response);
```

The response is a little different from the previous responses.  We see some metadata (`took`, `timed_out`, etc) and
an array named `hits`.  This represents your search results.  Inside of `hits` is another array named `hits`, which contains
individual search results:

```php
Array
(
    [took] => 1
    [timed_out] =>
    [_shards] => Array
        (
            [total] => 5
            [successful] => 5
            [failed] => 0
        )

    [hits] => Array
        (
            [total] => 1
            [max_score] => 0.30685282
            [hits] => Array
                (
                    [0] => Array
                        (
                            [_index] => my_index
                            [_type] => my_type
                            [_id] => my_id
                            [_score] => 0.30685282
                            [_source] => Array
                                (
                                    [testField] => abc
                                )
                        )
                )
        )
)
```

### Delete a document

Alright, let's go ahead and delete the document that we added previously:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];

$response = $client->delete($params);
print_r($response);
```

You'll notice this is identical syntax to the `get` syntax.  The only difference is the operation: `delete` instead of
`get`.  The response will confirm the document was deleted:

```php
Array
(
    [found] => 1
    [_index] => my_index
    [_type] => my_type
    [_id] => my_id
    [_version] => 2
)
```


### Delete an index

Due to the dynamic nature of Elasticsearch, the first document we added automatically built an index with some default settings.  Let's delete that index because we want to specify our own settings later:

```php
$deleteParams = [
    'index' => 'my_index'
];
$response = $client->indices()->delete($deleteParams);
print_r($response);
```

The response:


```php
Array
(
    [acknowledged] => 1
)
```

### Create an index

Now that we are starting fresh (no data or index), let's add a new index with some custom settings:

```php
$params = [
    'index' => 'my_index',
    'body' => [
        'settings' => [
            'number_of_shards' => 2,
            'number_of_replicas' => 0
        ]
    ]
];

$response = $client->indices()->create($params);
print_r($response);
```

Elasticsearch will now create that index with your chosen settings, and return an acknowledgement:

```php
Array
(
    [acknowledged] => 1
)
```



Wrap up
=======

That was just a crash-course overview of the client and it's syntax.  If you are familiar with elasticsearch, you'll notice that the methods are named just like REST endpoints.

You'll also notice that the client is configured in a manner that facilitates easy discovery via the IDE.  All core actions are available under the `$client` object (indexing, searching, getting, etc).  Index and cluster management are located under the `$client->indices()` and `$client->cluster()` objects, respectively.

Check out the rest of the [Documentation](http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/index.html) to see how the entire client works.


Available Licenses
-------

Starting with version 1.3.1, Elasticsearch-PHP is available under two licenses: Apache v2.0 and LGPL v2.1.  Versions
prior to 1.3.1 are still licensed with only Apache v2.0.

The user may choose which license they wish to use.  Since there is no discriminating executable or distribution bundle
to differentiate licensing, the user should document their license choice externally, in case the library is re-distributed.
If no explicit choice is made, assumption is that redistribution obeys rules of both licenses.

### Contributions
All contributions to the library are to be so that they can be licensed under both licenses.

Apache v2.0 License:
>Copyright 2013-2014 Elasticsearch
>
>Licensed under the Apache License, Version 2.0 (the "License");
>you may not use this file except in compliance with the License.
>You may obtain a copy of the License at
>
>    http://www.apache.org/licenses/LICENSE-2.0
>
>Unless required by applicable law or agreed to in writing, software
>distributed under the License is distributed on an "AS IS" BASIS,
>WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
>See the License for the specific language governing permissions and
>limitations under the License.

LGPL v2.1 Notice:
>Copyright (C) 2013-2014 Elasticsearch
>
>This library is free software; you can redistribute it and/or
>modify it under the terms of the GNU Lesser General Public
>License as published by the Free Software Foundation; either
>version 2.1 of the License, or (at your option) any later version.
>
>This library is distributed in the hope that it will be useful,
>but WITHOUT ANY WARRANTY; without even the implied warranty of
>MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
>Lesser General Public License for more details.
>
>You should have received a copy of the GNU Lesser General Public
>License along with this library; if not, write to the Free Software
>Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
