CHANGELOG
=========

2.3.0
-----

 * [BC BREAK] `Client::followRedirect()` won't redirect responses with
   a non-3xx Status Code and `Location` header anymore, as per 
   http://tools.ietf.org/html/rfc2616#section-14.30

 * added `Client::getInternalRequest()` and `Client::getInternalResponse()` to
   have access to the BrowserKit internal request and response objects

2.1.0
-----

 * [BC BREAK] The CookieJar internals have changed to allow cookies with the
   same name on different sub-domains/sub-paths
