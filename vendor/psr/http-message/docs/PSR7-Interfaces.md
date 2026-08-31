# Interfaces

The purpose of this list is to help in finding the methods when working with PSR-7. This can be considered as a cheatsheet for PSR-7 interfaces.

The interfaces defined in PSR-7 are the following:

| Class Name | Description |
|---|---|
| [Psr\Http\Message\MessageInterface](http://www.php-fig.org/psr/psr-7/#psrhttpmessagemessageinterface) | Representation of a HTTP message |
| [Psr\Http\Message\RequestInterface](http://www.php-fig.org/psr/psr-7/#psrhttpmessagerequestinterface) | Representation of an outgoing, client-side request. |
| [Psr\Http\Message\ServerRequestInterface](http://www.php-fig.org/psr/psr-7/#psrhttpmessageserverrequestinterface) | Representation of an incoming, server-side HTTP request. | 
| [Psr\Http\Message\ResponseInterface](http://www.php-fig.org/psr/psr-7/#psrhttpmessageresponseinterface) | Representation of an outgoing, server-side response. |
| [Psr\Http\Message\StreamInterface](http://www.php-fig.org/psr/psr-7/#psrhttpmessagestreaminterface) | Describes a data stream |
| [Psr\Http\Message\UriInterface](http://www.php-fig.org/psr/psr-7/#psrhttpmessageuriinterface) | Value object representing a URI. |
| [Psr\Http\Message\UploadedFileInterface](http://www.php-fig.org/psr/psr-7/#psrhttpmessageuploadedfileinterface) | Value object representing a file uploaded through an HTTP request. |

## `Psr\Http\Message\MessageInterface` Methods

| Method Name                        | Description | Notes |
|------------------------------------| ----------- | ----- |
| `getProtocolVersion()`             | Retrieve HTTP protocol version          |  1.0 or 1.1 |
| `withProtocolVersion($version)`    | Returns new message instance with given HTTP protocol version          |      |
| `getHeaders()`                     | Retrieve all HTTP Headers               | [Request Header List](https://en.wikipedia.org/wiki/List_of_HTTP_header_fields#Request_fields), [Response Header List](https://en.wikipedia.org/wiki/List_of_HTTP_header_fields#Response_fields)      |
| `hasHeader($name)`                 | Checks if HTTP Header with given name exists  | |
| `getHeader($name)`                 | Retrieves a array with the values for a single header | |
| `getHeaderLine($name)`             | Retrieves a comma-separated string of the values for a single header |  |
| `withHeader($name, $value)`        | Returns new message instance with given HTTP Header | if the header existed in the original instance, replaces the header value from the original message with the value provided when creating the new instance. |
| `withAddedHeader($name, $value)`   | Returns new message instance with appended value to given header | If header already exists value will be appended, if not a new header will be created |
| `withoutHeader($name)`             | Removes HTTP Header with given name| |
| `getBody()`                        | Retrieves the HTTP Message Body | Returns object implementing `StreamInterface`|
| `withBody(StreamInterface $body)`  | Returns new message instance with given HTTP Message Body | |


## `Psr\Http\Message\RequestInterface` Methods

Same methods as `Psr\Http\Message\MessageInterface`  + the following methods:

| Method Name                        | Description | Notes |
|------------------------------------| ----------- | ----- |
| `getRequestTarget()`                | Retrieves the message's request target              | origin-form, absolute-form, authority-form, asterisk-form ([RFC7230](https://www.rfc-editor.org/rfc/rfc7230.txt)) |
| `withRequestTarget($requestTarget)` | Return a new message instance with the specific request-target |      |
| `getMethod()`                       | Retrieves the HTTP method of the request.  |  GET, HEAD, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE (defined in [RFC7231](https://tools.ietf.org/html/rfc7231)), PATCH (defined in [RFC5789](https://tools.ietf.org/html/rfc5789)) |
| `withMethod($method)`               | Returns a new message instance with the provided HTTP method  | |
| `getUri()`                 | Retrieves the URI instance | |
| `withUri(UriInterface $uri, $preserveHost = false)` | Returns a new message instance with the provided URI |  |


## `Psr\Http\Message\ServerRequestInterface` Methods

Same methods as `Psr\Http\Message\RequestInterface`  + the following methods:

| Method Name                        | Description | Notes |
|------------------------------------| ----------- | ----- |
| `getServerParams() `               | Retrieve server parameters  | Typically derived from `$_SERVER`  |
| `getCookieParams()`                | Retrieves cookies sent by the client to the server. | Typically derived from `$_COOKIES` |
| `withCookieParams(array $cookies)` |  Returns a new request instance with the specified cookies      |   | 
| `withQueryParams(array $query)` | Returns a new request instance with the specified query string arguments  |  |
| `getUploadedFiles()` | Retrieve normalized file upload data  |  |
| `withUploadedFiles(array $uploadedFiles)` | Returns a new request instance with the specified uploaded files  |  |
| `getParsedBody()` | Retrieve any parameters provided in the request body  |  |
| `withParsedBody($data)` | Returns a new request instance with the specified body parameters  |  |
| `getAttributes()` | Retrieve attributes derived from the request  |  |
| `getAttribute($name, $default = null)` | Retrieve a single derived request attribute  |  |
| `withAttribute($name, $value)` | Returns a new request instance with the specified derived request attribute  |  |
| `withoutAttribute($name)` | Returns a new request instance that without the specified derived request attribute  |  |

## `Psr\Http\Message\ResponseInterface` Methods:

Same methods as `Psr\Http\Message\MessageInterface`  + the following methods:

| Method Name                        | Description | Notes |
|------------------------------------| ----------- | ----- |
| `getStatusCode()` | Gets the response status code. | |
| `withStatus($code, $reasonPhrase = '')` | Returns a new response instance with the specified status code and, optionally, reason phrase. | |
| `getReasonPhrase()` | Gets the response reason phrase associated with the status code. | |

##  `Psr\Http\Message\StreamInterface` Methods

| Method Name                        | Description | Notes |
|------------------------------------| ----------- | ----- |
| `__toString()` | Reads all data from the stream into a string, from the beginning to end. | |
| `close()` | Closes the stream and any underlying resources. | |
| `detach()` | Separates any underlying resources from the stream. | |
| `getSize()` | Get the size of the stream if known. | |
| `eof()` | Returns true if the stream is at the end of the stream.| |
| `isSeekable()` |  Returns whether or not the stream is seekable. | |
| `seek($offset, $whence = SEEK_SET)` | Seek to a position in the stream. | |
| `rewind()` | Seek to the beginning of the stream. | |
| `isWritable()` | Returns whether or not the stream is writable. | |
| `write($string)` | Write data to the stream. | |
| `isReadable()` | Returns whether or not the stream is readable. | |
| `read($length)` | Read data from the stream. | |
| `getContents()` | Returns the remaining contents in a string | |
| `getMetadata($key = null)()` | Get stream metadata as an associative array or retrieve a specific key. | |

## `Psr\Http\Message\UriInterface` Methods

| Method Name                        | Description | Notes |
|------------------------------------| ----------- | ----- |
| `getScheme()` | Retrieve the scheme component of the URI. | |
| `getAuthority()` | Retrieve the authority component of the URI. | |
| `getUserInfo()` | Retrieve the user information component of the URI. | |
| `getHost()` | Retrieve the host component of the URI. | |
| `getPort()` | Retrieve the port component of the URI. | |
| `getPath()` | Retrieve the path component of the URI. | |
| `getQuery()` | Retrieve the query string of the URI. | |
| `getFragment()` | Retrieve the fragment component of the URI. | |
| `withScheme($scheme)` | Return an instance with the specified scheme. | |
| `withUserInfo($user, $password = null)` | Return an instance with the specified user information. | |
| `withHost($host)` | Return an instance with the specified host. | |
| `withPort($port)` | Return an instance with the specified port. | |
| `withPath($path)` | Return an instance with the specified path. | |
| `withQuery($query)` | Return an instance with the specified query string. | |
| `withFragment($fragment)` | Return an instance with the specified URI fragment. | |
| `__toString()` | Return the string representation as a URI reference. | |

## `Psr\Http\Message\UploadedFileInterface` Methods

| Method Name                        | Description | Notes |
|------------------------------------| ----------- | ----- |
| `getStream()` | Retrieve a stream representing the uploaded file. | |
| `moveTo($targetPath)` | Move the uploaded file to a new location. | |
| `getSize()` | Retrieve the file size. | |
| `getError()` | Retrieve the error associated with the uploaded file. | |
| `getClientFilename()` | Retrieve the filename sent by the client. | |
| `getClientMediaType()` | Retrieve the media type sent by the client. | |

> `RequestInterface`, `ServerRequestInterface`, `ResponseInterface` extend `MessageInterface`  because the `Request` and the `Response` are `HTTP Messages`.
> When using `ServerRequestInterface`, both `RequestInterface` and `Psr\Http\Message\MessageInterface` methods are considered.

