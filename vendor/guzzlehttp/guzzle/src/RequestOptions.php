<?php

namespace GuzzleHttp;

/**
 * This class contains a list of built-in Guzzle request options.
 *
 * @see https://github.com/guzzle/guzzle/blob/7.15/docs/request-options.md
 */
final class RequestOptions
{
    /**
     * allow_redirects: (bool|array) Controls redirect behavior. Pass false
     * to disable redirects, pass true to enable redirects, pass an
     * associative to provide custom redirect settings. Defaults to "false".
     * This option only works if your handler has the RedirectMiddleware. When
     * passing an associative array, you can provide the following key value
     * pairs:
     *
     * - max: (int, default=5) maximum number of allowed redirects.
     * - strict: (bool, default=false) Set to true to use strict redirects
     *   meaning redirect POST requests with POST requests vs. doing what most
     *   browsers do which is redirect POST requests with GET requests. The
     *   QUERY method keeps its method and body across non-strict 301 and 302
     *   redirects, and a 303 redirect is followed with a body-less GET.
     * - referer: (bool, default=false) Set to true to enable the Referer
     *   header.
     * - protocols: (non-empty-array<array-key, string>, default=['http', 'https'])
     *   Allowed redirect protocols. Redirect matching is case-sensitive; use
     *   "http" and "https".
     * - on_redirect: (callable) PHP callable that is invoked when a redirect
     *   is encountered. The callable is invoked with the request, the redirect
     *   response that was received, and the effective URI. Any return value
     *   from the on_redirect function is ignored.
     * - track_redirects: (bool, default=false) Track redirected URI and status
     *   history in response headers.
     */
    public const ALLOW_REDIRECTS = 'allow_redirects';

    /**
     * auth: (array{0: string, 1: string, 2?: string|null}|string|false|null)
     * Pass an array of HTTP authentication parameters to use with the request.
     * The array must contain the username in index [0], the password in index
     * [1], and you can optionally provide a built-in authentication type in
     * index [2]. Pass false or null to disable authentication for a request.
     * String values are passed through for custom handlers.
     */
    public const AUTH = 'auth';

    /**
     * body: (resource|string|null|int|float|bool|\Psr\Http\Message\StreamInterface|(callable&object)|\Iterator|\Stringable)
     * Body to send in the request. Callable arrays are arrays, and arrays are
     * not valid body values in Guzzle.
     */
    public const BODY = 'body';

    /**
     * cert: (string|array{0: string, 1?: string|null}) Set to a string to
     * specify the path to a client certificate file. PEM is the default
     * certificate format. If a password is required, set cert to an array
     * containing the certificate path in the first array element followed by
     * the certificate password in the second array element. A null password is
     * treated the same as omitting it. Use cert_type to specify another
     * supported certificate format.
     */
    public const CERT = 'cert';

    /**
     * cert_type: (string) Specify the SSL client certificate file type.
     */
    public const CERT_TYPE = 'cert_type';

    /**
     * cookies: (false|GuzzleHttp\Cookie\CookieJarInterface, default=false)
     * Specifies whether or not cookies are used in a request or what cookie
     * jar to use or what cookies to send. This option only works if your
     * handler has the `cookie` middleware. Valid values are `false` and
     * an instance of {@see Cookie\CookieJarInterface}.
     */
    public const COOKIES = 'cookies';

    /**
     * connect_timeout: (int|float, default=0) Number of seconds to wait while
     * trying to connect to a server. Use 0 to wait 300 seconds (the default
     * behavior).
     */
    public const CONNECT_TIMEOUT = 'connect_timeout';

    /**
     * crypto_method: (int) A value describing the minimum TLS protocol
     * version to use.
     *
     * This setting must be set to one of the
     * ``STREAM_CRYPTO_METHOD_TLS*_CLIENT`` constants. PHP 7.4 or higher is
     * required in order to use TLS 1.3, and cURL 7.34.0 or higher is required
     * in order to specify a crypto method, with cURL 7.52.0 or higher being
     * required to use TLS 1.3.
     */
    public const CRYPTO_METHOD = 'crypto_method';

    /**
     * crypto_method_max: (int) A value describing the maximum TLS protocol
     * version to use.
     *
     * This setting must be set to one of the
     * ``STREAM_CRYPTO_METHOD_TLS*_CLIENT`` constants. On the stream handler,
     * PHP 7.3 or higher is required to set a maximum TLS version, and PHP 7.4
     * or higher is required to use TLS 1.3. cURL 7.54.0 or higher is required
     * in order to specify a maximum TLS version with the cURL handler.
     */
    public const CRYPTO_METHOD_MAX = 'crypto_method_max';

    /**
     * curl: (array) Raw cURL options to apply when using a built-in cURL handler.
     */
    public const CURL = 'curl';

    /**
     * debug: (bool|resource) Set to true or set to a PHP stream returned by
     * fopen()  enable debug output with the HTTP handler used to send a
     * request.
     */
    public const DEBUG = 'debug';

    /**
     * decode_content: (bool|string, default=true) Specify whether or not
     * Content-Encoding responses (gzip, deflate, etc.) are automatically
     * decoded.
     */
    public const DECODE_CONTENT = 'decode_content';

    /**
     * delay: (int|float) The amount of time to delay before sending in
     * milliseconds.
     */
    public const DELAY = 'delay';

    /**
     * expect: (bool|integer) Controls the behavior of the
     * "Expect: 100-Continue" header.
     *
     * Set to `true` to enable the "Expect: 100-Continue" header for all
     * requests that sends a body. Set to `false` to disable the
     * "Expect: 100-Continue" header for all requests. Set to a number so that
     * the size of the payload must be greater than the number in order to send
     * the Expect header. Setting to a number will send the Expect header for
     * all requests in which the size of the payload cannot be determined or
     * where the body is not rewindable.
     *
     * By default, Guzzle will add the "Expect: 100-Continue" header when the
     * size of the body of a request is greater than 1 MB and a request is
     * using HTTP/1.1.
     */
    public const EXPECT = 'expect';

    /**
     * form_params: (array<array-key, string|int|float|bool|null|array>)
     * Associative array of form field names to scalar, null, or nested array
     * values. Sets the Content-Type header to application/x-www-form-urlencoded
     * when no Content-Type header is already present.
     */
    public const FORM_PARAMS = 'form_params';

    /**
     * headers: (array<array-key, string|non-empty-array<array-key, string>>|null)
     * Associative array of HTTP headers. Each value MUST be a string or non-empty
     * array of strings.
     */
    public const HEADERS = 'headers';

    /**
     * http_errors: (bool, default=true) Set to false to disable exceptions
     * when a non- successful HTTP response is received. By default,
     * exceptions will be thrown for 4xx and 5xx responses. This option only
     * works if your handler has the `httpErrors` middleware.
     */
    public const HTTP_ERRORS = 'http_errors';

    /**
     * idn_conversion: (bool|int|null, default=false) A combination of IDNA_*
     * constants for PHP's idn_to_ascii() function. Set to false or null to
     * disable IDN support, or to true to use the default configuration
     * (IDNA_DEFAULT constant).
     */
    public const IDN_CONVERSION = 'idn_conversion';

    /**
     * json: (mixed) Adds JSON data to a request. The provided value is JSON
     * encoded and a Content-Type header of application/json will be added to
     * the request if no Content-Type header is already present.
     */
    public const JSON = 'json';

    /**
     * multipart: (array) Array of part arrays, each containing a required
     * "name" key mapping to the string or integer form field name, a required
     * "contents" key mapping to any non-array value accepted by PSR-7
     * Utils::streamFor() or a nested array of field values, an optional
     * "headers" array of string custom header values, and an optional
     * "filename" key mapping to a string to send as the filename in the part.
     * "headers" and "filename" cannot be used when "contents" is an array.
     */
    public const MULTIPART = 'multipart';

    /**
     * multiplex: (string) Controls how a request sent through a built-in
     * cURL handler relates to shared, multiplexed connections: how an HTTP/2
     * request pursues one, or, with Multiplexing::NONE, whether the transfer
     * may share its connection at all. When the option is not set,
     * multiplexing is left to libcurl: nothing waits, and established
     * multiplex-capable connections are still shared. Use
     * Multiplexing::EAGER to explicitly never wait for pending connections,
     * Multiplexing::WAIT to wait on libcurl-eligible pending connections with
     * CURLOPT_PIPEWAIT, normally to the same origin,
     * Multiplexing::REQUIRE_EAGER to fail unless a multiplexed protocol is
     * guaranteed while dialing eagerly, or Multiplexing::REQUIRE_WAIT for the
     * same guarantee while also waiting on pending connections. The required
     * modes require a handler that permits actual multiplexing, not merely a
     * multiplexed protocol, and are rejected on a Multiplexing::NONE handler.
     * The stream handler ignores EAGER and WAIT, and rejects the required
     * family; CurlHandler has no multi handle to multiplex over. Explicit
     * modes reject deprecated raw cURL options they conflict with: the
     * required family cannot be combined with a raw CURLOPT_HTTP_VERSION,
     * CURLOPT_URL, or CURLOPT_FOLLOWLOCATION; no explicit mode can be
     * combined with a raw CURLOPT_PIPEWAIT on the CurlMultiHandler; and
     * Multiplexing::NONE on a CurlMultiHandler that permits multiplexing
     * cannot be combined with the raw CURLOPT_HTTP_VERSION, CURLOPT_HTTPAUTH
     * (including the "auth" request option's "digest" and "ntlm" modes,
     * which set it), CURLOPT_PROXYAUTH, CURLOPT_FOLLOWLOCATION,
     * CURLOPT_HTTPHEADER, CURLOPT_ALTSVC, CURLOPT_ALTSVC_CTRL, or
     * CURLOPT_PROXYTYPE cURL options. The required family also
     * rejects final CURLOPT_HTTPAUTH masks that permit NTLM, which libcurl
     * retries over HTTP/1.1. The required family validates its cleartext
     * proxy rule against the final cURL configuration, after raw options
     * such as CURLOPT_PROXY and CURLOPT_PRE_PROXY are applied; only the
     * exact raw CURLOPT_NOPROXY wildcard '*' disables the primary proxy and
     * pre-proxy there, and raw host-specific patterns are conservatively
     * treated as leaving them active. These rejections are
     * configuration-conflict checks, not remote security checks.
     *
     * Multiplexing::NONE disables multiplexing for a whole handler when
     * passed as the "multiplex" client configuration option, which
     * configures the default handler and also becomes the default request
     * option, or, when constructing a handler directly, as the
     * CurlMultiHandler "multiplex" constructor option. A handler
     * configured with Multiplexing::NONE rejects explicitly requested wait
     * modes as a configuration conflict when the transfer would actually
     * wait, and always rejects the required modes, because they require a
     * handler that permits actual multiplexing, not merely a multiplexed
     * protocol. As a request option value, Multiplexing::NONE guarantees the
     * transfer does not share its connection with any concurrent transfer.
     * Multiplexing::NONE does not force HTTP/1.1: on a Multiplexing::NONE
     * handler, HTTP/2 still negotiates and each transfer keeps its
     * connection to itself.
     *
     * The request option value is accepted exactly where the guarantee
     * holds and can be verified: on a CurlMultiHandler configured with
     * Multiplexing::NONE, for requests whose declared protocol version is
     * HTTP/1.x, on CurlHandler, and on the stream handler, which never
     * multiplexes. An HTTP/2 request with a Multiplexing::NONE request
     * option is rejected on a CurlMultiHandler that permits multiplexing.
     * On a CurlMultiHandler that permits multiplexing, Multiplexing::NONE
     * is also rejected with a custom "handle_factory", alongside a raw
     * CURLMOPT_PIPELINING cURL multi option, and combined with the raw
     * CURLOPT_HTTP_VERSION, CURLOPT_HTTPAUTH (including the "auth" request
     * option's "digest" and "ntlm" modes, which set it), CURLOPT_PROXYAUTH,
     * CURLOPT_FOLLOWLOCATION, CURLOPT_HTTPHEADER, CURLOPT_ALTSVC,
     * CURLOPT_ALTSVC_CTRL, or CURLOPT_PROXYTYPE cURL options. It is also
     * rejected when the request carries an Expect: 100-continue header (its
     * 417 retries select connections outside the safeguards; remove an
     * explicitly supplied header, or set the "expect" request option to
     * false to prevent it being added automatically).
     *
     * On a client whose multi handler permits multiplexing, the ordinary
     * non-streaming default stack - both cURL handlers available and no
     * connection caps forcing multi-only routing - runs synchronous
     * requests on the CurlHandler path, which satisfies the guarantee for
     * any protocol version, while asynchronous requests run on the
     * CurlMultiHandler, so an HTTP/2 request with Multiplexing::NONE
     * succeeds synchronously and is rejected asynchronously on the same
     * client. Keep-alive reuse between consecutive transfers is
     * unaffected, except on libcurl versions below 7.77.0 and from 8.11.0
     * through 8.12.1, where an accepted HTTP/1.x request on a multiplexing
     * CurlMultiHandler forces a fresh connection. Custom handlers receive
     * the "multiplex" option unchanged: its semantics are handler-defined,
     * Guzzle does not guarantee it is honored, and a client-level
     * Multiplexing::NONE with a custom handler flows to it as a default
     * request option without client-side enforcement.
     */
    public const MULTIPLEX = 'multiplex';

    /**
     * on_headers: (callable) A callable that is invoked when the HTTP headers
     * of the response have been received but the body has not yet begun to
     * download.
     */
    public const ON_HEADERS = 'on_headers';

    /**
     * on_stats: (callable) allows you to get access to transfer statistics of
     * a request and access the lower level transfer details of the handler
     * associated with your client. ``on_stats`` is a callable that is invoked
     * when a handler has finished sending a request. The callback is invoked
     * with transfer statistics about the request, the response received, or
     * the error encountered. Included in the data is the total amount of time
     * taken to send the request.
     */
    public const ON_STATS = 'on_stats';

    /**
     * on_trailers: (callable) A callable that is invoked by the built-in cURL
     * handlers once per successful transfer, after the response body has been
     * received, with an associative array of the parsed HTTP trailers followed
     * by the response. Trailer field names are lowercased and grouped
     * case-insensitively; values keep their wire order. Malformed trailer
     * field lines are discarded before parsing. Trailer fields are reported
     * separately from response headers and are never merged into the response.
     */
    public const ON_TRAILERS = 'on_trailers';

    /**
     * progress: (callable) Defines a function to invoke when transfer
     * progress is made. The function accepts the following positional
     * arguments: the total number of bytes expected to be downloaded, the
     * number of bytes downloaded so far, the number of bytes expected to be
     * uploaded, the number of bytes uploaded so far.
     */
    public const PROGRESS = 'progress';

    /**
     * protocols: (non-empty-array<array-key, string>, default=['http', 'https'])
     * Allowed URI schemes. Built-in handlers accept only the case-sensitive
     * values "http" and "https".
     */
    public const PROTOCOLS = 'protocols';

    /**
     * proxy: (string|array) Pass a string to specify an HTTP proxy, or an
     * array to specify different proxies for different protocols (where the
     * key is the protocol and the value is a proxy string or null). Provide a
     * "no" key as a comma-delimited string, array of strings, or null to
     * specify hosts or host-and-port pairs that should not be proxied.
     */
    public const PROXY = 'proxy';

    /**
     * query: (array<array-key, mixed>|string) Associative array of query string
     * values to add to the request. This option uses PHP's http_build_query()
     * to create the string representation. Pass a string value if you need
     * more control than what this method provides
     */
    public const QUERY = 'query';

    /**
     * sink: (resource|string|\Psr\Http\Message\StreamInterface) Where the data
     * of the response is written to. Defaults to a PHP temp stream. Providing
     * a string will write data to a file by the given name.
     */
    public const SINK = 'sink';

    /**
     * synchronous: (bool) Set to true to inform HTTP handlers that you intend
     * on waiting on the response. This can be useful for optimizations. Note
     * that a promise is still returned if you are using one of the async
     * client methods.
     */
    public const SYNCHRONOUS = 'synchronous';

    /**
     * ssl_key: (array{0: string, 1?: string|null}|string) Specify the path to
     * a private SSL key file. PEM is the default private key format. If a
     * password is required, set ssl_key to an array containing the key path in
     * the first array element followed by the key password in the second
     * element. A null password is treated the same as omitting it. Use
     * ssl_key_type to specify another supported key format.
     */
    public const SSL_KEY = 'ssl_key';

    /**
     * ssl_key_type: (string) Specify the SSL private key file type.
     */
    public const SSL_KEY_TYPE = 'ssl_key_type';

    /**
     * stream: (bool) Set to true to attempt to stream a response rather than
     * download it all up-front.
     */
    public const STREAM = 'stream';

    /**
     * stream_context: (array) PHP stream context options to merge into the
     * context used by the built-in stream handler.
     */
    public const STREAM_CONTEXT = 'stream_context';

    /**
     * verify: (bool|string, default=true) Describes the SSL certificate
     * verification behavior of a request. Set to true to enable SSL
     * certificate verification using the system CA bundle when available
     * (the default). Set to false to disable certificate verification (this
     * is insecure!). Set to a string to provide the path to a CA bundle on
     * disk to enable verification using a custom certificate.
     */
    public const VERIFY = 'verify';

    /**
     * timeout: (int|float, default=0) Number describing the timeout of the
     * request in seconds. Use 0 to wait indefinitely (the default behavior).
     */
    public const TIMEOUT = 'timeout';

    /**
     * read_timeout: (int|float, default=default_socket_timeout ini setting)
     * Number describing the body read timeout, for stream requests.
     */
    public const READ_TIMEOUT = 'read_timeout';

    /**
     * retries: (int) Current retry count used by the retry middleware.
     */
    public const RETRIES = 'retries';

    /**
     * version: (string|int|float) Specifies the HTTP protocol version to attempt
     * to use.
     */
    public const VERSION = 'version';

    /**
     * force_ip_resolve: (string) Set to "v4" to force IPv4 resolution or "v6"
     * for IPv6 resolution when supported by the handler.
     */
    public const FORCE_IP_RESOLVE = 'force_ip_resolve';
}
