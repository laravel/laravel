# CHANGELOG

## 1.1.0 - 2015-05-19

* Added `CURL_HTTP_VERSION_2_0`
* The PHP stream wrapper handler now sets `allow_self_signed` to `false` to
  match the cURL handler when `verify` is set to `true` or a certificate file.
* Ensuring that a directory exists before using the `save_to` option.
* Response protocol version is now correctly extracted from a response.
* Fixed a bug in which the result of `CurlFactory::retryFailedRewind` did not
  return an array.

## 1.0.7 - 2015-03-29

* PHP 7 fixes.

## 1.0.6 - 2015-02-26

* Bug fix: futures now extend from React's PromiseInterface to ensure that they
  are properly forwarded down the promise chain.
* The multi handle of the CurlMultiHandler is now created lazily.

## 1.0.5 - 2014-12-10

* Adding more error information to PHP stream wrapper exceptions.
* Added digest auth integration test support to test server.

## 1.0.4 - 2014-12-01

* Added support for older versions of cURL that do not have CURLOPT_TIMEOUT_MS.
* Setting debug to `false` does not enable debug output.
* Added a fix to the StreamHandler to return a `FutureArrayInterface` when an
  error occurs.

## 1.0.3 - 2014-11-03

* Setting the `header` stream option as a string to be compatible with GAE.
* Header parsing now ensures that header order is maintained in the parsed
  message.

## 1.0.2 - 2014-10-28

* Now correctly honoring a `version` option is supplied in a request.
  See https://github.com/guzzle/RingPHP/pull/8

## 1.0.1 - 2014-10-26

* Fixed a header parsing issue with the `CurlHandler` and `CurlMultiHandler`
  that caused cURL requests with multiple responses to merge repsonses together
  (e.g., requests with digest authentication).

## 1.0.0 - 2014-10-12

* Initial release.
