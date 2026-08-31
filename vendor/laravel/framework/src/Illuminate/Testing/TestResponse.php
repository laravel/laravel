<?php

namespace Illuminate\Testing;

use ArrayAccess;
use Closure;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Testing\Constraints\SeeInOrder;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponseAssert as PHPUnit;
use LogicException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @template TResponse of \Symfony\Component\HttpFoundation\Response
 *
 * @mixin \Illuminate\Http\Response
 */
class TestResponse implements ArrayAccess
{
    use Concerns\AssertsStatusCodes, Conditionable, Dumpable, Tappable, Macroable {
        __call as macroCall;
    }

    /**
     * The original request.
     *
     * @var \Illuminate\Http\Request|null
     */
    public $baseRequest;

    /**
     * The response to delegate to.
     *
     * @var TResponse
     */
    public $baseResponse;

    /**
     * The collection of logged exceptions for the request.
     *
     * @var \Illuminate\Support\Collection
     */
    public $exceptions;

    /**
     * The streamed content of the response.
     *
     * @var string
     */
    protected $streamedContent;

    /**
     * Create a new test response instance.
     *
     * @param  TResponse  $response
     * @param  \Illuminate\Http\Request|null  $request
     */
    public function __construct($response, $request = null)
    {
        $this->baseResponse = $response;
        $this->baseRequest = $request;
        $this->exceptions = new Collection;
    }

    /**
     * Create a new TestResponse from another response.
     *
     * @template R of TResponse
     *
     * @param  R  $response
     * @param  \Illuminate\Http\Request|null  $request
     * @return static<R>
     */
    public static function fromBaseResponse($response, $request = null)
    {
        return new static($response, $request);
    }

    /**
     * Assert that the response has a successful status code.
     *
     * @return $this
     */
    public function assertSuccessful()
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->isSuccessful(),
            $this->statusMessageWithDetails('>=200, <300', $this->getStatusCode())
        );

        return $this;
    }

    /**
     * Assert that the Precognition request was successful.
     *
     * @return $this
     */
    public function assertSuccessfulPrecognition()
    {
        $this->assertNoContent();

        PHPUnit::withResponse($this)->assertTrue(
            $this->headers->has('Precognition-Success'),
            'Header [Precognition-Success] not present on response.'
        );

        PHPUnit::withResponse($this)->assertSame(
            'true',
            $this->headers->get('Precognition-Success'),
            'The Precognition-Success header was found, but the value is not `true`.'
        );

        return $this;
    }

    /**
     * Assert that the response is a client error.
     *
     * @return $this
     */
    public function assertClientError()
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->isClientError(),
            $this->statusMessageWithDetails('>=400, < 500', $this->getStatusCode())
        );

        return $this;
    }

    /**
     * Assert that the response is a server error.
     *
     * @return $this
     */
    public function assertServerError()
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->isServerError(),
            $this->statusMessageWithDetails('>=500, < 600', $this->getStatusCode())
        );

        return $this;
    }

    /**
     * Assert that the response has the given status code.
     *
     * @param  int  $status
     * @return $this
     */
    public function assertStatus($status)
    {
        $message = $this->statusMessageWithDetails($status, $actual = $this->getStatusCode());

        PHPUnit::withResponse($this)->assertSame($status, $actual, $message);

        return $this;
    }

    /**
     * Get an assertion message for a status assertion containing extra details when available.
     *
     * @param  string|int  $expected
     * @param  string|int  $actual
     * @return string
     */
    protected function statusMessageWithDetails($expected, $actual)
    {
        return "Expected response status code [{$expected}] but received {$actual}.";
    }

    /**
     * Assert whether the response is redirecting to a given URI.
     *
     * @param  string|null  $uri
     * @return $this
     */
    public function assertRedirect($uri = null)
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->isRedirect(),
            $this->statusMessageWithDetails('201, 301, 302, 303, 307, 308', $this->getStatusCode()),
        );

        if (! is_null($uri)) {
            $this->assertLocation($uri);
        }

        return $this;
    }

    /**
     * Assert whether the response is redirecting to a URI that contains the given URI.
     *
     * @param  string  $uri
     * @return $this
     */
    public function assertRedirectContains($uri)
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->isRedirect(),
            $this->statusMessageWithDetails('201, 301, 302, 303, 307, 308', $this->getStatusCode()),
        );

        PHPUnit::withResponse($this)->assertTrue(
            Str::contains($this->headers->get('Location'), $uri), 'Redirect location ['.$this->headers->get('Location').'] does not contain ['.$uri.'].'
        );

        return $this;
    }

    /**
     * Assert whether the response is redirecting back to the previous location.
     *
     * @return $this
     */
    public function assertRedirectBack()
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->isRedirect(),
            $this->statusMessageWithDetails('201, 301, 302, 303, 307, 308', $this->getStatusCode()),
        );

        $this->assertLocation(app('url')->previous());

        return $this;
    }

    /**
     * Assert whether the response is redirecting back to the previous location with the given errors in the session.
     *
     * @param  string|array  $keys
     * @param  mixed  $format
     * @param  string  $errorBag
     * @return $this
     */
    public function assertRedirectBackWithErrors($keys = [], $format = null, $errorBag = 'default')
    {
        $this->assertRedirectBack();

        $this->assertSessionHasErrors($keys, $format, $errorBag);

        return $this;
    }

    /**
     * Assert whether the response is redirecting back to the previous location with no errors in the session.
     *
     * @return $this
     */
    public function assertRedirectBackWithoutErrors()
    {
        $this->assertRedirectBack();

        $this->assertSessionHasNoErrors();

        return $this;
    }

    /**
     * Assert whether the response is redirecting to a given route.
     *
     * @param  \BackedEnum|string  $name
     * @param  mixed  $parameters
     * @return $this
     */
    public function assertRedirectToRoute($name, $parameters = [])
    {
        $uri = route($name, $parameters);

        PHPUnit::withResponse($this)->assertTrue(
            $this->isRedirect(),
            $this->statusMessageWithDetails('201, 301, 302, 303, 307, 308', $this->getStatusCode()),
        );

        $this->assertLocation($uri);

        return $this;
    }

    /**
     * Assert whether the response is redirecting to a given signed route.
     *
     * @param  \BackedEnum|string|null  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return $this
     */
    public function assertRedirectToSignedRoute($name = null, $parameters = [], $absolute = true)
    {
        if (! is_null($name)) {
            $uri = route($name, $parameters);
        }

        PHPUnit::withResponse($this)->assertTrue(
            $this->isRedirect(),
            $this->statusMessageWithDetails('201, 301, 302, 303, 307, 308', $this->getStatusCode()),
        );

        $request = Request::create($this->headers->get('Location'));

        PHPUnit::withResponse($this)->assertTrue(
            $request->hasValidSignature($absolute), 'The response is not a redirect to a signed route.'
        );

        if (! is_null($name)) {
            $expectedUri = rtrim($request->fullUrlWithQuery([
                'signature' => null,
                'expires' => null,
            ]), '?');

            PHPUnit::withResponse($this)->assertEquals(
                app('url')->to($uri), $expectedUri
            );
        }

        return $this;
    }

    /**
     * Assert whether the response is redirecting to a given controller action.
     *
     * @param  string|array  $name
     * @param  array  $parameters
     * @return $this
     */
    public function assertRedirectToAction($name, $parameters = [])
    {
        $uri = action($name, $parameters);

        PHPUnit::withResponse($this)->assertTrue(
            $this->isRedirect(),
            $this->statusMessageWithDetails('201, 301, 302, 303, 307, 308', $this->getStatusCode()),
        );

        $this->assertLocation($uri);

        return $this;
    }

    /**
     * Asserts that the response contains the given header and equals the optional value.
     *
     * @param  string  $headerName
     * @param  mixed  $value
     * @return $this
     */
    public function assertHeader($headerName, $value = null)
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->headers->has($headerName), "Header [{$headerName}] not present on response."
        );

        $actual = $this->headers->get($headerName);

        if (! is_null($value)) {
            PHPUnit::withResponse($this)->assertEqualsIgnoringCase(
                $value, $this->headers->get($headerName),
                "Header [{$headerName}] was found, but value [{$actual}] does not match [{$value}]."
            );
        }

        return $this;
    }

    /**
     * Asserts that the response contains the given header and that its value contains the given string.
     *
     * @param  string  $headerName
     * @param  string  $value
     * @return $this
     */
    public function assertHeaderContains($headerName, $value)
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->headers->has($headerName), "Header [{$headerName}] not present on response."
        );

        $actual = $this->headers->get($headerName, '');

        PHPUnit::withResponse($this)->assertTrue(
            Str::contains($actual, $value),
            "Header [{$headerName}] was found, but [{$actual}] does not contain [{$value}]."
        );

        return $this;
    }

    /**
     * Asserts that the response does not contain the given header.
     *
     * @param  string  $headerName
     * @return $this
     */
    public function assertHeaderMissing($headerName)
    {
        PHPUnit::withResponse($this)->assertFalse(
            $this->headers->has($headerName), "Unexpected header [{$headerName}] is present on response."
        );

        return $this;
    }

    /**
     * Assert that the current location header matches the given URI.
     *
     * @param  string  $uri
     * @return $this
     */
    public function assertLocation($uri)
    {
        PHPUnit::withResponse($this)->assertEquals(
            app('url')->to($uri), app('url')->to($this->headers->get('Location', ''))
        );

        return $this;
    }

    /**
     * Assert that the response offers a file download.
     *
     * @param  string|null  $filename
     * @return $this
     */
    public function assertDownload($filename = null)
    {
        $contentDisposition = explode(';', $this->headers->get('content-disposition', ''));

        if (trim($contentDisposition[0]) !== 'attachment') {
            PHPUnit::withResponse($this)->fail(
                'Response does not offer a file download.'.PHP_EOL.
                'Disposition ['.trim($contentDisposition[0]).'] found in header, [attachment] expected.'
            );
        }

        if (! is_null($filename)) {
            if (isset($contentDisposition[1]) &&
                trim(explode('=', $contentDisposition[1])[0]) !== 'filename') {
                PHPUnit::withResponse($this)->fail(
                    'Unsupported Content-Disposition header provided.'.PHP_EOL.
                    'Disposition ['.trim(explode('=', $contentDisposition[1])[0]).'] found in header, [filename] expected.'
                );
            }

            $message = "Expected file [{$filename}] is not present in Content-Disposition header.";

            if (! isset($contentDisposition[1])) {
                PHPUnit::withResponse($this)->fail($message);
            } else {
                PHPUnit::withResponse($this)->assertSame(
                    $filename,
                    isset(explode('=', $contentDisposition[1])[1])
                        ? trim(explode('=', $contentDisposition[1])[1], " \"'")
                        : '',
                    $message
                );

                return $this;
            }
        } else {
            PHPUnit::withResponse($this)->assertTrue(true);

            return $this;
        }
    }

    /**
     * Asserts that the response contains the given cookie and equals the optional value.
     *
     * @param  string  $cookieName
     * @param  mixed  $value
     * @return $this
     */
    public function assertPlainCookie($cookieName, $value = null)
    {
        $this->assertCookie($cookieName, $value, false);

        return $this;
    }

    /**
     * Asserts that the response contains the given cookie and equals the optional value.
     *
     * @param  string  $cookieName
     * @param  mixed  $value
     * @param  bool  $encrypted
     * @param  bool  $unserialize
     * @return $this
     */
    public function assertCookie($cookieName, $value = null, $encrypted = true, $unserialize = false)
    {
        PHPUnit::withResponse($this)->assertNotNull(
            $cookie = $this->getCookie($cookieName, $encrypted && ! is_null($value), $unserialize),
            "Cookie [{$cookieName}] not present on response."
        );

        if (! $cookie || is_null($value)) {
            return $this;
        }

        $cookieValue = $cookie->getValue();

        PHPUnit::withResponse($this)->assertEquals(
            $value, $cookieValue,
            "Cookie [{$cookieName}] was found, but value [{$cookieValue}] does not match [{$value}]."
        );

        return $this;
    }

    /**
     * Asserts that the response contains the given cookie and is expired.
     *
     * @param  string  $cookieName
     * @return $this
     */
    public function assertCookieExpired($cookieName)
    {
        PHPUnit::withResponse($this)->assertNotNull(
            $cookie = $this->getCookie($cookieName, false),
            "Cookie [{$cookieName}] not present on response."
        );

        $expiresAt = Carbon::createFromTimestamp($cookie->getExpiresTime(), date_default_timezone_get());

        PHPUnit::withResponse($this)->assertTrue(
            $cookie->getExpiresTime() !== 0 && $expiresAt->lessThan(Carbon::now()),
            "Cookie [{$cookieName}] is not expired, it expires at [{$expiresAt}]."
        );

        return $this;
    }

    /**
     * Asserts that the response contains the given cookie and is not expired.
     *
     * @param  string  $cookieName
     * @return $this
     */
    public function assertCookieNotExpired($cookieName)
    {
        PHPUnit::withResponse($this)->assertNotNull(
            $cookie = $this->getCookie($cookieName, false),
            "Cookie [{$cookieName}] not present on response."
        );

        $expiresAt = Carbon::createFromTimestamp($cookie->getExpiresTime(), date_default_timezone_get());

        PHPUnit::withResponse($this)->assertTrue(
            $cookie->getExpiresTime() === 0 || $expiresAt->greaterThan(Carbon::now()),
            "Cookie [{$cookieName}] is expired, it expired at [{$expiresAt}]."
        );

        return $this;
    }

    /**
     * Asserts that the response does not contain the given cookie.
     *
     * @param  string  $cookieName
     * @return $this
     */
    public function assertCookieMissing($cookieName)
    {
        PHPUnit::withResponse($this)->assertNull(
            $this->getCookie($cookieName, false),
            "Cookie [{$cookieName}] is present on response."
        );

        return $this;
    }

    /**
     * Get the given cookie from the response.
     *
     * @param  string  $cookieName
     * @param  bool  $decrypt
     * @param  bool  $unserialize
     * @return \Symfony\Component\HttpFoundation\Cookie|null
     */
    public function getCookie($cookieName, $decrypt = true, $unserialize = false)
    {
        foreach ($this->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $cookieName) {
                if (! $decrypt) {
                    return $cookie;
                }

                $decryptedValue = CookieValuePrefix::remove(
                    app('encrypter')->decrypt($cookie->getValue(), $unserialize)
                );

                return new Cookie(
                    $cookie->getName(),
                    $decryptedValue,
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHttpOnly(),
                    $cookie->isRaw(),
                    $cookie->getSameSite(),
                    $cookie->isPartitioned()
                );
            }
        }
    }

    /**
     * Assert that the given string matches the response content.
     *
     * @param  string  $value
     * @return $this
     */
    public function assertContent($value)
    {
        PHPUnit::withResponse($this)->assertSame($value, $this->getContent());

        return $this;
    }

    /**
     * Assert that the response was streamed.
     *
     * @return $this
     */
    public function assertStreamed()
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->baseResponse instanceof StreamedResponse || $this->baseResponse instanceof StreamedJsonResponse,
            'Expected the response to be streamed, but it wasn\'t.'
        );

        return $this;
    }

    /**
     * Assert that the response was not streamed.
     *
     * @return $this
     */
    public function assertNotStreamed()
    {
        PHPUnit::withResponse($this)->assertTrue(
            ! $this->baseResponse instanceof StreamedResponse && ! $this->baseResponse instanceof StreamedJsonResponse,
            'Response was unexpectedly streamed.'
        );

        return $this;
    }

    /**
     * Assert that the given string matches the streamed response content.
     *
     * @param  string  $value
     * @return $this
     */
    public function assertStreamedContent($value)
    {
        PHPUnit::withResponse($this)->assertSame($value, $this->streamedContent());

        return $this;
    }

    /**
     * Assert that the given array matches the streamed JSON response content.
     *
     * @param  array  $value
     * @return $this
     */
    public function assertStreamedJsonContent($value)
    {
        return $this->assertStreamedContent(json_encode($value, JSON_THROW_ON_ERROR));
    }

    /**
     * Assert that the given string or array of strings are contained within the response.
     *
     * @param  string|list<string>  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertSee($value, $escape = true)
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map(e(...), $value) : $value;

        foreach ($values as $value) {
            PHPUnit::withResponse($this)->assertStringContainsString((string) $value, $this->getContent());
        }

        return $this;
    }

    /**
     * Assert that the given HTML string or array of HTML strings are contained within the response.
     *
     * @param  string|list<string>  $value
     * @return $this
     */
    public function assertSeeHtml($value)
    {
        return $this->assertSee($value, false);
    }

    /**
     * Assert that the given strings are contained in order within the response.
     *
     * @param  list<string>  $values
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeInOrder(array $values, $escape = true)
    {
        $values = $escape ? array_map(e(...), $values) : $values;

        PHPUnit::withResponse($this)->assertThat($values, new SeeInOrder($this->getContent()));

        return $this;
    }

    /**
     * Assert that the given HTML strings are contained in order within the response.
     *
     * @param  list<string>  $values
     * @return $this
     */
    public function assertSeeHtmlInOrder(array $values)
    {
        return $this->assertSeeInOrder($values, false);
    }

    /**
     * Assert that the given string or array of strings are contained within the response text.
     *
     * @param  string|list<string>  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeText($value, $escape = true)
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map(e(...), $value) : $value;

        $content = strip_tags($this->getContent());

        foreach ($values as $value) {
            PHPUnit::withResponse($this)->assertStringContainsString((string) $value, $content);
        }

        return $this;
    }

    /**
     * Assert that the given strings are contained in order within the response text.
     *
     * @param  list<string>  $values
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeTextInOrder(array $values, $escape = true)
    {
        $values = $escape ? array_map(e(...), $values) : $values;

        PHPUnit::withResponse($this)->assertThat($values, new SeeInOrder(strip_tags($this->getContent())));

        return $this;
    }

    /**
     * Assert that the given string or array of strings are not contained within the response.
     *
     * @param  string|list<string>  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertDontSee($value, $escape = true)
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map(e(...), $value) : $value;

        foreach ($values as $value) {
            PHPUnit::withResponse($this)->assertStringNotContainsString((string) $value, $this->getContent());
        }

        return $this;
    }

    /**
     * Assert that the given HTML string or array of HTML strings are not contained within the response.
     *
     * @param  string|list<string>  $value
     * @return $this
     */
    public function assertDontSeeHtml($value)
    {
        return $this->assertDontSee($value, false);
    }

    /**
     * Assert that the given string or array of strings are not contained within the response text.
     *
     * @param  string|list<string>  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertDontSeeText($value, $escape = true)
    {
        $value = Arr::wrap($value);

        $values = $escape ? array_map(e(...), $value) : $value;

        $content = strip_tags($this->getContent());

        foreach ($values as $value) {
            PHPUnit::withResponse($this)->assertStringNotContainsString((string) $value, $content);
        }

        return $this;
    }

    /**
     * Assert that the response is a superset of the given JSON.
     *
     * @param  array|callable  $value
     * @param  bool  $strict
     * @return $this
     */
    public function assertJson($value, $strict = false)
    {
        $json = $this->decodeResponseJson();

        if (is_array($value)) {
            $json->assertSubset($value, $strict);
        } else {
            $assert = AssertableJson::fromAssertableJsonString($json);

            $value($assert);

            if (Arr::isAssoc($assert->toArray())) {
                $assert->interacted();
            }
        }

        return $this;
    }

    /**
     * Assert that the expected value and type exists at the given path in the response.
     *
     * @param  string  $path
     * @param  mixed  $expect
     * @return $this
     */
    public function assertJsonPath($path, $expect)
    {
        $this->decodeResponseJson()->assertPath($path, $expect);

        return $this;
    }

    /**
     * Assert that the given path in the response contains all of the expected values without looking at the order.
     *
     * @param  string  $path
     * @param  array  $expect
     * @return $this
     */
    public function assertJsonPathCanonicalizing($path, array $expect)
    {
        $this->decodeResponseJson()->assertPathCanonicalizing($path, $expect);

        return $this;
    }

    /**
     * Assert that the response has the exact given JSON.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertExactJson(array $data)
    {
        $this->decodeResponseJson()->assertExact($data);

        return $this;
    }

    /**
     * Assert that the response has the similar JSON as given.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertSimilarJson(array $data)
    {
        $this->decodeResponseJson()->assertSimilar($data);

        return $this;
    }

    /**
     * Assert that the response contains the given JSON fragments.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertJsonFragments(array $data)
    {
        foreach ($data as $fragment) {
            $this->assertJsonFragment($fragment);
        }

        return $this;
    }

    /**
     * Assert that the response contains the given JSON fragment.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertJsonFragment(array $data)
    {
        $this->decodeResponseJson()->assertFragment($data);

        return $this;
    }

    /**
     * Assert that the response does not contain the given JSON fragment.
     *
     * @param  array  $data
     * @param  bool  $exact
     * @return $this
     */
    public function assertJsonMissing(array $data, $exact = false)
    {
        $this->decodeResponseJson()->assertMissing($data, $exact);

        return $this;
    }

    /**
     * Assert that the response does not contain the exact JSON fragment.
     *
     * @param  array  $data
     * @return $this
     */
    public function assertJsonMissingExact(array $data)
    {
        $this->decodeResponseJson()->assertMissingExact($data);

        return $this;
    }

    /**
     * Assert that the response does not contain the given path.
     *
     * @param  string  $path
     * @return $this
     */
    public function assertJsonMissingPath(string $path)
    {
        $this->decodeResponseJson()->assertMissingPath($path);

        return $this;
    }

    /**
     * Assert that the response has a given JSON structure.
     *
     * @param  array|null  $structure
     * @param  array|null  $responseData
     * @return $this
     */
    public function assertJsonStructure(?array $structure = null, ?array $responseData = null)
    {
        $this->decodeResponseJson()->assertStructure($structure, $responseData);

        return $this;
    }

    /**
     * Assert that the response has the exact JSON structure.
     *
     * @param  array|null  $structure
     * @param  array|null  $responseData
     * @return $this
     */
    public function assertExactJsonStructure(?array $structure = null, ?array $responseData = null)
    {
        $this->decodeResponseJson()->assertStructure($structure, $responseData, true);

        return $this;
    }

    /**
     * Assert that the response JSON has the expected count of items at the given key.
     *
     * @param  int  $count
     * @param  string|null  $key
     * @return $this
     */
    public function assertJsonCount(int $count, $key = null)
    {
        $this->decodeResponseJson()->assertCount($count, $key);

        return $this;
    }

    /**
     * Assert that the response has the given JSON validation errors.
     *
     * @param  string|array  $errors
     * @param  string  $responseKey
     * @return $this
     */
    public function assertJsonValidationErrors($errors, $responseKey = 'errors')
    {
        $errors = Arr::wrap($errors);

        PHPUnit::withResponse($this)->assertNotEmpty($errors, 'No validation errors were provided.');

        $jsonErrors = Arr::get($this->json(), $responseKey) ?? [];

        $errorMessage = $jsonErrors
            ? 'Response has the following JSON validation errors:'.
                    PHP_EOL.PHP_EOL.json_encode($jsonErrors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL
            : 'Response does not have JSON validation errors.';

        foreach ($errors as $key => $value) {
            if (is_int($key)) {
                $this->assertJsonValidationErrorFor($value, $responseKey);

                continue;
            }

            $this->assertJsonValidationErrorFor($key, $responseKey);

            foreach (Arr::wrap($value) as $expectedMessage) {
                $errorMissing = true;

                foreach (Arr::wrap($jsonErrors[$key]) as $jsonErrorMessage) {
                    if (Str::contains($jsonErrorMessage, $expectedMessage)) {
                        $errorMissing = false;

                        break;
                    }
                }

                if ($errorMissing) {
                    PHPUnit::withResponse($this)->fail(
                        "Failed to find a validation error in the response for key and message: '$key' => '$expectedMessage'".PHP_EOL.PHP_EOL.$errorMessage
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Assert that the response has the given JSON validation errors but does not have any other JSON validation errors.
     *
     * @param  string|array  $errors
     * @param  string  $responseKey
     * @return $this
     */
    public function assertOnlyJsonValidationErrors($errors, $responseKey = 'errors')
    {
        $this->assertJsonValidationErrors($errors, $responseKey);

        $jsonErrors = Arr::get($this->json(), $responseKey) ?? [];

        $expectedErrorKeys = (new Collection($errors))
            ->map(fn ($value, $key) => is_int($key) ? $value : $key)
            ->all();

        $unexpectedErrorKeys = Arr::except($jsonErrors, $expectedErrorKeys);

        PHPUnit::withResponse($this)->assertTrue(
            count($unexpectedErrorKeys) === 0,
            'Response has unexpected validation errors: '.(new Collection($unexpectedErrorKeys))->keys()->map(fn ($key) => "'{$key}'")->join(', ')
        );

        return $this;
    }

    /**
     * Assert the response has any JSON validation errors for the given key.
     *
     * @param  string  $key
     * @param  string  $responseKey
     * @return $this
     */
    public function assertJsonValidationErrorFor($key, $responseKey = 'errors')
    {
        $jsonErrors = Arr::get($this->json(), $responseKey) ?? [];

        $errorMessage = $jsonErrors
            ? 'Response has the following JSON validation errors:'.
            PHP_EOL.PHP_EOL.json_encode($jsonErrors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL
            : 'Response does not have JSON validation errors.';

        PHPUnit::withResponse($this)->assertArrayHasKey(
            $key,
            $jsonErrors,
            "Failed to find a validation error in the response for key: '{$key}'".PHP_EOL.PHP_EOL.$errorMessage
        );

        return $this;
    }

    /**
     * Assert that the response has no JSON validation errors for the given keys.
     *
     * @param  string|array|null  $keys
     * @param  string  $responseKey
     * @return $this
     */
    public function assertJsonMissingValidationErrors($keys = null, $responseKey = 'errors')
    {
        if ($this->getContent() === '') {
            PHPUnit::withResponse($this)->assertTrue(true);

            return $this;
        }

        $json = $this->json();

        if (! Arr::has($json, $responseKey)) {
            PHPUnit::withResponse($this)->assertTrue(true);

            return $this;
        }

        $errors = Arr::get($json, $responseKey, []);

        if (is_null($keys) && count($errors) > 0) {
            PHPUnit::withResponse($this)->fail(
                'Response has unexpected validation errors: '.PHP_EOL.PHP_EOL.
                json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
        }

        foreach (Arr::wrap($keys) as $key) {
            PHPUnit::withResponse($this)->assertFalse(
                isset($errors[$key]),
                "Found unexpected validation error for key: '{$key}'"
            );
        }

        return $this;
    }

    /**
     * Assert that the given key is a JSON array.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function assertJsonIsArray($key = null)
    {
        $data = $this->json($key);

        $encodedData = json_encode($data);

        PHPUnit::withResponse($this)->assertTrue(
            is_array($data)
            && str_starts_with($encodedData, '[')
            && str_ends_with($encodedData, ']')
        );

        return $this;
    }

    /**
     * Assert that the given key is a JSON object.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function assertJsonIsObject($key = null)
    {
        $data = $this->json($key);

        $encodedData = json_encode($data);

        PHPUnit::withResponse($this)->assertTrue(
            is_array($data)
            && str_starts_with($encodedData, '{')
            && str_ends_with($encodedData, '}')
        );

        return $this;
    }

    /**
     * Validate the decoded response JSON.
     *
     * @return \Illuminate\Testing\AssertableJsonString
     *
     * @throws \Throwable
     */
    public function decodeResponseJson()
    {
        if ($this->baseResponse instanceof StreamedResponse ||
            $this->baseResponse instanceof StreamedJsonResponse) {
            $testJson = new AssertableJsonString($this->streamedContent());
        } else {
            $testJson = new AssertableJsonString($this->getContent());
        }

        $decodedResponse = $testJson->json();

        if (is_null($decodedResponse) || $decodedResponse === false) {
            if ($this->exception) {
                throw $this->exception;
            } else {
                PHPUnit::withResponse($this)->fail('Invalid JSON was returned from the route.');
            }
        }

        return $testJson;
    }

    /**
     * Return the decoded response JSON.
     *
     * @param  string|null  $key
     * @return mixed
     */
    public function json($key = null)
    {
        return $this->decodeResponseJson()->json($key);
    }

    /**
     * Get the decoded JSON body of the response as a collection.
     *
     * @param  string|null  $key
     * @return \Illuminate\Support\Collection
     */
    public function collect($key = null)
    {
        return new Collection($this->json($key));
    }

    /**
     * Assert that the response view equals the given value.
     *
     * @param  string  $value
     * @return $this
     */
    public function assertViewIs($value)
    {
        $this->ensureResponseHasView();

        PHPUnit::withResponse($this)->assertEquals($value, $this->original->name());

        return $this;
    }

    /**
     * Assert that the response view has a given piece of bound data.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function assertViewHas($key, $value = null)
    {
        if (is_array($key)) {
            return $this->assertViewHasAll($key);
        }

        $this->ensureResponseHasView();

        $actual = Arr::get($this->original->gatherData(), $key);

        if (is_null($value)) {
            PHPUnit::withResponse($this)->assertTrue(Arr::has($this->original->gatherData(), $key), "Failed asserting that the data contains the key [{$key}].");
        } elseif ($value instanceof Closure) {
            PHPUnit::withResponse($this)->assertTrue($value($actual), "Failed asserting that the value at [{$key}] fulfills the expectations defined by the closure.");
        } elseif ($value instanceof Model) {
            PHPUnit::withResponse($this)->assertTrue($value->is($actual), "Failed asserting that the model at [{$key}] matches the given model.");
        } elseif ($value instanceof EloquentCollection) {
            PHPUnit::withResponse($this)->assertInstanceOf(EloquentCollection::class, $actual);
            PHPUnit::withResponse($this)->assertSameSize($value, $actual);

            $value->each(fn ($item, $index) => PHPUnit::withResponse($this)->assertTrue($actual->get($index)->is($item), "Failed asserting that the collection at [{$key}.[{$index}]]' matches the given collection."));
        } else {
            PHPUnit::withResponse($this)->assertEquals($value, $actual, "Failed asserting that [{$key}] matches the expected value.");
        }

        return $this;
    }

    /**
     * Assert that the response view has a given list of bound data.
     *
     * @param  array  $bindings
     * @return $this
     */
    public function assertViewHasAll(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if (is_int($key)) {
                $this->assertViewHas($value);
            } else {
                $this->assertViewHas($key, $value);
            }
        }

        return $this;
    }

    /**
     * Get a piece of data from the original view.
     *
     * @param  string|null  $key
     * @return mixed
     */
    public function viewData($key = null)
    {
        $this->ensureResponseHasView();

        $data = $this->original->gatherData();

        if (is_null($key)) {
            return $data;
        }

        return $data[$key];
    }

    /**
     * Assert that the response view is missing a piece of bound data.
     *
     * @param  string  $key
     * @return $this
     */
    public function assertViewMissing($key)
    {
        $this->ensureResponseHasView();

        PHPUnit::withResponse($this)->assertFalse(Arr::has($this->original->gatherData(), $key));

        return $this;
    }

    /**
     * Ensure that the response has a view as its original content.
     *
     * @return $this
     */
    protected function ensureResponseHasView()
    {
        if (! $this->responseHasView()) {
            return PHPUnit::withResponse($this)->fail('The response is not a view.');
        }

        return $this;
    }

    /**
     * Determine if the original response is a view.
     *
     * @return bool
     */
    protected function responseHasView()
    {
        return isset($this->original) && $this->original instanceof View;
    }

    /**
     * Assert that the given keys do not have validation errors.
     *
     * @param  string|array|null  $keys
     * @param  string  $errorBag
     * @param  string  $responseKey
     * @return $this
     */
    public function assertValid($keys = null, $errorBag = 'default', $responseKey = 'errors')
    {
        if ($this->baseResponse->headers->get('Content-Type') === 'application/json') {
            return $this->assertJsonMissingValidationErrors($keys, $responseKey);
        }

        if ($this->session()->get('errors')) {
            $errors = $this->session()->get('errors')->getBag($errorBag)->getMessages();
        } else {
            $errors = [];
        }

        if (empty($errors)) {
            PHPUnit::withResponse($this)->assertTrue(true);

            return $this;
        }

        if (is_null($keys) && count($errors) > 0) {
            PHPUnit::withResponse($this)->fail(
                'Response has unexpected validation errors: '.PHP_EOL.PHP_EOL.
                json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
        }

        foreach (Arr::wrap($keys) as $key) {
            PHPUnit::withResponse($this)->assertFalse(
                isset($errors[$key]),
                "Found unexpected validation error for key: '{$key}'"
            );
        }

        return $this;
    }

    /**
     * Assert that the response has the given validation errors.
     *
     * @param  string|array|null  $errors
     * @param  string  $errorBag
     * @param  string  $responseKey
     * @return $this
     */
    public function assertInvalid($errors = null,
                                  $errorBag = 'default',
                                  $responseKey = 'errors')
    {
        if ($this->baseResponse->headers->get('Content-Type') === 'application/json') {
            return $this->assertJsonValidationErrors($errors, $responseKey);
        }

        $this->assertSessionHas('errors');

        $sessionErrors = $this->session()->get('errors')->getBag($errorBag)->getMessages();

        $errorMessage = $sessionErrors
            ? 'Response has the following validation errors in the session:'.
                    PHP_EOL.PHP_EOL.json_encode($sessionErrors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL
            : 'Response does not have validation errors in the session.';

        foreach (Arr::wrap($errors) as $key => $value) {
            PHPUnit::withResponse($this)->assertArrayHasKey(
                $resolvedKey = (is_int($key)) ? $value : $key,
                $sessionErrors,
                "Failed to find a validation error in session for key: '{$resolvedKey}'".PHP_EOL.PHP_EOL.$errorMessage
            );

            foreach (Arr::wrap($value) as $message) {
                if (! is_int($key)) {
                    $hasError = false;

                    foreach (Arr::wrap($sessionErrors[$key]) as $sessionErrorMessage) {
                        if (Str::contains($sessionErrorMessage, $message)) {
                            $hasError = true;

                            break;
                        }
                    }

                    if (! $hasError) {
                        PHPUnit::withResponse($this)->fail(
                            "Failed to find a validation error for key and message: '$key' => '$message'".PHP_EOL.PHP_EOL.$errorMessage
                        );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Assert that the response has the given validation errors but does not have any other validation errors.
     *
     * @param  string|array|null  $errors
     * @param  string  $errorBag
     * @param  string  $responseKey
     * @return $this
     */
    public function assertOnlyInvalid($errors = null, $errorBag = 'default', $responseKey = 'errors')
    {
        if ($this->baseResponse->headers->get('Content-Type') === 'application/json') {
            return $this->assertOnlyJsonValidationErrors($errors, $responseKey);
        }

        $this->assertSessionHas('errors');

        $sessionErrors = $this->session()->get('errors')
            ->getBag($errorBag)
            ->getMessages();

        $expectedErrorKeys = (new Collection($errors))
            ->map(fn ($value, $key) => is_int($key) ? $value : $key)
            ->all();

        $unexpectedErrorKeys = Arr::except($sessionErrors, $expectedErrorKeys);

        PHPUnit::withResponse($this)->assertTrue(
            count($unexpectedErrorKeys) === 0,
            'Response has unexpected validation errors: '.(new Collection($unexpectedErrorKeys))->keys()->map(fn ($key) => "'{$key}'")->join(', ')
        );

        return $this;
    }

    /**
     * Assert that the session has a given value.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function assertSessionHas($key, $value = null)
    {
        if (is_array($key)) {
            return $this->assertSessionHasAll($key);
        }

        if (is_null($value)) {
            PHPUnit::withResponse($this)->assertTrue(
                $this->session()->has($key),
                "Session is missing expected key [{$key}]."
            );
        } elseif ($value instanceof Closure) {
            PHPUnit::withResponse($this)->assertTrue($value($this->session()->get($key)));
        } else {
            PHPUnit::withResponse($this)->assertEquals($value, $this->session()->get($key));
        }

        return $this;
    }

    /**
     * Assert that the session has a given list of values.
     *
     * @param  array  $bindings
     * @return $this
     */
    public function assertSessionHasAll(array $bindings)
    {
        $actual = [];
        $expected = [];

        foreach ($bindings as $key => $value) {
            if (is_int($key)) {
                $this->assertSessionHas($value);
            } elseif ($value instanceof Closure) {
                $this->assertSessionHas($key, $value);
            } else {
                $expected[$key] = $value;
                $actual[$key] = $this->session()->get($key);
            }
        }

        if (! empty($expected)) {
            PHPUnit::withResponse($this)->assertEquals($expected, $actual);
        }

        return $this;
    }

    /**
     * Assert that the session has a given value in the flashed input array.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function assertSessionHasInput($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if (is_int($k)) {
                    $this->assertSessionHasInput($v);
                } else {
                    $this->assertSessionHasInput($k, $v);
                }
            }

            return $this;
        }

        if (is_null($value)) {
            PHPUnit::withResponse($this)->assertTrue(
                $this->session()->hasOldInput($key),
                "Session is missing expected key [{$key}]."
            );
        } elseif ($value instanceof Closure) {
            PHPUnit::withResponse($this)->assertTrue($value($this->session()->getOldInput($key)));
        } else {
            PHPUnit::withResponse($this)->assertEquals($value, $this->session()->getOldInput($key));
        }

        return $this;
    }

    /**
     * Assert that the session has the given errors.
     *
     * @param  string|array  $keys
     * @param  mixed  $format
     * @param  string  $errorBag
     * @return $this
     */
    public function assertSessionHasErrors($keys = [], $format = null, $errorBag = 'default')
    {
        $this->assertSessionHas('errors');

        $keys = (array) $keys;

        $errors = $this->session()->get('errors')->getBag($errorBag);

        foreach ($keys as $key => $value) {
            if (is_int($key)) {
                PHPUnit::withResponse($this)->assertTrue($errors->has($value), "Session missing error: $value");
            } else {
                PHPUnit::withResponse($this)->assertContains(is_bool($value) ? (string) $value : $value, $errors->get($key, $format));
            }
        }

        return $this;
    }

    /**
     * Assert that the session is missing the given errors.
     *
     * @param  string|array  $keys
     * @param  string|null  $format
     * @param  string  $errorBag
     * @return $this
     */
    public function assertSessionDoesntHaveErrors($keys = [], $format = null, $errorBag = 'default')
    {
        $keys = (array) $keys;

        if (empty($keys)) {
            return $this->assertSessionHasNoErrors();
        }

        if (is_null($this->session()->get('errors'))) {
            PHPUnit::withResponse($this)->assertTrue(true);

            return $this;
        }

        $errors = $this->session()->get('errors')->getBag($errorBag);

        foreach ($keys as $key => $value) {
            if (is_int($key)) {
                PHPUnit::withResponse($this)->assertFalse($errors->has($value), "Session has unexpected error: $value");
            } else {
                PHPUnit::withResponse($this)->assertNotContains($value, $errors->get($key, $format));
            }
        }

        return $this;
    }

    /**
     * Assert that the session has no errors.
     *
     * @return $this
     */
    public function assertSessionHasNoErrors()
    {
        $hasErrors = $this->session()->has('errors');

        PHPUnit::withResponse($this)->assertFalse(
            $hasErrors,
            'Session has unexpected errors: '.PHP_EOL.PHP_EOL.
            json_encode((function () use ($hasErrors) {
                $errors = [];

                $sessionErrors = $this->session()->get('errors');

                if ($hasErrors && is_a($sessionErrors, ViewErrorBag::class)) {
                    foreach ($sessionErrors->getBags() as $bag => $messages) {
                        if (is_a($messages, MessageBag::class)) {
                            $errors[$bag] = $messages->all();
                        }
                    }
                }

                return $errors;
            })(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        );

        return $this;
    }

    /**
     * Assert that the session has the given errors.
     *
     * @param  string  $errorBag
     * @param  string|array  $keys
     * @param  mixed  $format
     * @return $this
     */
    public function assertSessionHasErrorsIn($errorBag, $keys = [], $format = null)
    {
        return $this->assertSessionHasErrors($keys, $format, $errorBag);
    }

    /**
     * Assert that the session does not have a given key.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function assertSessionMissing($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $value) {
                $this->assertSessionMissing($value);
            }

            return $this;
        }

        if (is_null($value)) {
            PHPUnit::withResponse($this)->assertFalse(
                $this->session()->has($key),
                "Session has unexpected key [{$key}]."
            );
        } elseif ($value instanceof Closure) {
            PHPUnit::withResponse($this)->assertFalse($value($this->session()->get($key)));
        } else {
            PHPUnit::withResponse($this)->assertNotEquals($value, $this->session()->get($key));
        }

        return $this;
    }

    /**
     * Get the current session store.
     *
     * @return \Illuminate\Session\Store
     */
    protected function session()
    {
        $session = app('session.store');

        if (! $session->isStarted()) {
            $session->start();
        }

        return $session;
    }

    /**
     * Dump the headers from the response and end the script.
     *
     * @return never
     */
    public function ddHeaders()
    {
        $this->dumpHeaders();

        exit(1);
    }

    /**
     * Dump the body of the response and end the script.
     *
     * @param  string|null  $key
     * @return never
     */
    public function ddBody($key = null)
    {
        $content = $this->content();

        if (json_validate($content)) {
            $this->ddJson($key);
        }

        dd($content);
    }

    /**
     * Dump the JSON payload from the response and end the script.
     *
     * @param  string|null  $key
     * @return never
     */
    public function ddJson($key = null)
    {
        dd($this->json($key));
    }

    /**
     * Dump the session from the response and end the script.
     *
     * @param  string|array  $keys
     * @return never
     */
    public function ddSession($keys = [])
    {
        $this->dumpSession($keys);

        exit(1);
    }

    /**
     * Dump the content from the response.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function dump($key = null)
    {
        $content = $this->getContent();

        $json = json_decode($content);

        if (json_last_error() === JSON_ERROR_NONE) {
            $content = $json;
        }

        if (! is_null($key)) {
            dump(data_get($content, $key));
        } else {
            dump($content);
        }

        return $this;
    }

    /**
     * Dump the headers from the response.
     *
     * @return $this
     */
    public function dumpHeaders()
    {
        dump($this->headers->all());

        return $this;
    }

    /**
     * Dump the session from the response.
     *
     * @param  string|array  $keys
     * @return $this
     */
    public function dumpSession($keys = [])
    {
        $keys = (array) $keys;

        if (empty($keys)) {
            dump($this->session()->all());
        } else {
            dump($this->session()->only($keys));
        }

        return $this;
    }

    /**
     * Get the streamed content from the response.
     *
     * @return string
     */
    public function streamedContent()
    {
        if (! is_null($this->streamedContent)) {
            return $this->streamedContent;
        }

        if (! $this->baseResponse instanceof StreamedResponse
            && ! $this->baseResponse instanceof BinaryFileResponse) {
            PHPUnit::withResponse($this)->fail('The response is not a streamed response.');
        }

        ob_start(function (string $buffer): string {
            $this->streamedContent .= $buffer;

            return '';
        });

        $this->sendContent();

        ob_end_clean();

        return $this->streamedContent;
    }

    /**
     * Set the previous exceptions on the response.
     *
     * @param  \Illuminate\Support\Collection  $exceptions
     * @return $this
     */
    public function withExceptions(Collection $exceptions)
    {
        $this->exceptions = $exceptions;

        return $this;
    }

    /**
     * Dynamically access base response parameters.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->baseResponse->{$key};
    }

    /**
     * Proxy isset() checks to the underlying base response.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->baseResponse->{$key});
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->responseHasView()
            ? isset($this->original->gatherData()[$offset])
            : isset($this->json()[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->responseHasView()
            ? $this->viewData($offset)
            : $this->json()[$offset];
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     *
     * @throws \LogicException
     */
    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     *
     * @throws \LogicException
     */
    public function offsetUnset($offset): void
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Handle dynamic calls into macros or pass missing methods to the base response.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $args);
        }

        return $this->baseResponse->{$method}(...$args);
    }
}
