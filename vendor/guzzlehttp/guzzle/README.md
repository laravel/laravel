![Guzzle](.github/logo.png?raw=true)

# Guzzle, PHP HTTP client

[![Latest Version](https://img.shields.io/github/release/guzzle/guzzle.svg?style=flat-square)](https://github.com/guzzle/guzzle/releases)
[![Build Status](https://img.shields.io/github/actions/workflow/status/guzzle/guzzle/ci.yml?label=ci%20build&style=flat-square)](https://github.com/guzzle/guzzle/actions?query=workflow%3ACI)
[![Total Downloads](https://img.shields.io/packagist/dt/guzzlehttp/guzzle.svg?style=flat-square)](https://packagist.org/packages/guzzlehttp/guzzle)

Guzzle is a PHP HTTP client that makes it easy to send HTTP requests and
trivial to integrate with web services.

- Simple interface for building query strings, POST requests, streaming large
  uploads, streaming large downloads, using HTTP cookies, uploading JSON data,
  etc...
- Can send both synchronous and asynchronous requests using the same interface.
- Uses PSR-7 interfaces for requests, responses, and streams. This allows you
  to utilize other PSR-7 compatible libraries with Guzzle.
- Supports PSR-18 allowing interoperability between other PSR-18 HTTP Clients.
- Abstracts away the underlying HTTP transport, allowing you to write
  environment and transport agnostic code; i.e., no hard dependency on cURL,
  PHP streams, sockets, or non-blocking event loops.
- Middleware system allows you to augment and compose client behavior.

```php
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://api.github.com/repos/guzzle/guzzle');

echo $response->getStatusCode(); // 200
echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
echo $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'

// Send an asynchronous request.
$request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
$promise = $client->sendAsync($request)->then(function ($response) {
    echo 'I completed! ' . $response->getBody();
});

$promise->wait();
```

## Help and docs

We use GitHub issues only to discuss bugs and new features. For support please refer to:

- [Documentation](docs/index.md)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/guzzle)
- [#guzzle](https://app.slack.com/client/T0D2S9JCT/CE6UAAKL4) channel on [PHP-HTTP Slack](https://slack.httplug.io/)
- [Gitter](https://gitter.im/guzzle/guzzle)


## Installing Guzzle

The recommended way to install Guzzle is through
[Composer](https://getcomposer.org/).

```bash
composer require guzzlehttp/guzzle
```


## Version Guidance

| Version | Status              | Packagist           | Namespace    | Repo                | Docs                | PSR-7 | PHP Version  |
|---------|---------------------|---------------------|--------------|---------------------|---------------------|-------|--------------|
| 3.x     | EOL (2016-10-31)    | `guzzle/guzzle`     | `Guzzle`     | [v3][guzzle-3-repo] | [v3][guzzle-3-docs] | No    | >=5.3.3,<7.0 |
| 4.x     | EOL (2016-10-31)    | `guzzlehttp/guzzle` | `GuzzleHttp` | [v4][guzzle-4-repo] | N/A                 | No    | >=5.4,<7.0   |
| 5.x     | EOL (2019-10-31)    | `guzzlehttp/guzzle` | `GuzzleHttp` | [v5][guzzle-5-repo] | [v5][guzzle-5-docs] | No    | >=5.4,<7.4   |
| 6.x     | EOL (2023-10-31)    | `guzzlehttp/guzzle` | `GuzzleHttp` | [v6][guzzle-6-repo] | [v6][guzzle-6-docs] | Yes   | >=5.5,<8.0   |
| 7.x     | Latest              | `guzzlehttp/guzzle` | `GuzzleHttp` | [v7][guzzle-7-repo] | [v7][guzzle-7-docs] | Yes   | >=7.2.5,<8.6 |

[guzzle-3-repo]: https://github.com/guzzle/guzzle3
[guzzle-4-repo]: https://github.com/guzzle/guzzle/tree/4.x
[guzzle-5-repo]: https://github.com/guzzle/guzzle/tree/5.3
[guzzle-6-repo]: https://github.com/guzzle/guzzle/tree/6.5
[guzzle-7-repo]: https://github.com/guzzle/guzzle/tree/7.15
[guzzle-3-docs]: https://github.com/guzzle/guzzle3/tree/master/docs
[guzzle-5-docs]: https://github.com/guzzle/guzzle/tree/5.3/docs
[guzzle-6-docs]: https://github.com/guzzle/guzzle/tree/6.5/docs
[guzzle-7-docs]: https://github.com/guzzle/guzzle/blob/7.15/docs/index.md


## Security

If you discover a security vulnerability within this package, please send an email to security@tidelift.com. All security vulnerabilities will be promptly addressed. Please do not disclose security-related issues publicly until a fix has been announced. Please see [Security Policy](https://github.com/guzzle/guzzle/security/policy) for more information.

## License

Guzzle is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.

## For Enterprise

Available as part of the Tidelift Subscription

The maintainers of Guzzle and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. [Learn more.](https://tidelift.com/subscription/pkg/packagist-guzzlehttp-guzzle?utm_source=packagist-guzzlehttp-guzzle&utm_medium=referral&utm_campaign=enterprise&utm_term=repo)
