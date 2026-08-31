<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

/**
 * Response represents an HTTP response in JSON format.
 *
 * Note that this class does not force the returned JSON content to be an
 * object. It is however recommended that you do return an object as it
 * protects yourself against XSSI and JSON-JavaScript Hijacking.
 *
 * @see https://github.com/OWASP/CheatSheetSeries/blob/master/cheatsheets/AJAX_Security_Cheat_Sheet.md#always-return-json-with-an-object-on-the-outside
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
class JsonResponse extends Response
{
    protected mixed $data;
    protected ?string $callback = null;

    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    // 15 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    public const DEFAULT_ENCODING_OPTIONS = 15;

    protected int $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;

    /**
     * @param bool $json If the data is already a JSON string
     */
    public function __construct(mixed $data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct('', $status, $headers);

        if ($json && !\is_string($data) && !is_numeric($data) && !$data instanceof \Stringable) {
            throw new \TypeError(\sprintf('"%s": If $json is set to true, argument $data must be a string or object implementing __toString(), "%s" given.', __METHOD__, get_debug_type($data)));
        }

        $data ??= new \ArrayObject();

        $json ? $this->setJson($data) : $this->setData($data);
    }

    /**
     * Factory method for chainability.
     *
     * Example:
     *
     *     return JsonResponse::fromJsonString('{"key": "value"}')
     *         ->setSharedMaxAge(300);
     *
     * @param string $data    The JSON response string
     * @param int    $status  The response status code (200 "OK" by default)
     * @param array  $headers An array of response headers
     */
    public static function fromJsonString(string $data, int $status = 200, array $headers = []): static
    {
        return new static($data, $status, $headers, true);
    }

    /**
     * Sets the JSONP callback.
     *
     * @param string|null $callback The JSONP callback or null to use none
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When the callback name is not valid
     */
    public function setCallback(?string $callback): static
    {
        if (null !== $callback) {
            // partially taken from https://geekality.net/2011/08/03/valid-javascript-identifier/
            // partially taken from https://github.com/willdurand/JsonpCallbackValidator
            //      JsonpCallbackValidator is released under the MIT License. See https://github.com/willdurand/JsonpCallbackValidator/blob/v1.1.0/LICENSE for details.
            //      (c) William Durand <william.durand1@gmail.com>
            $pattern = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*(?:\[(?:"(?:\\\.|[^"\\\])*"|\'(?:\\\.|[^\'\\\])*\'|\d+)\])*?$/u';
            $reserved = [
                'break', 'do', 'instanceof', 'typeof', 'case', 'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 'for', 'switch', 'while',
                'debugger', 'function', 'this', 'with', 'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 'extends', 'super',  'const', 'export',
                'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 'null', 'true', 'false',
            ];
            $parts = explode('.', $callback);
            foreach ($parts as $part) {
                if (!preg_match($pattern, $part) || \in_array($part, $reserved, true)) {
                    throw new \InvalidArgumentException('The callback name is not valid.');
                }
            }
        }

        $this->callback = $callback;

        return $this->update();
    }

    /**
     * Sets a raw string containing a JSON document to be sent.
     *
     * @return $this
     */
    public function setJson(string $json): static
    {
        $this->data = $json;

        return $this->update();
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setData(mixed $data = []): static
    {
        try {
            $data = json_encode($data, $this->encodingOptions);
        } catch (\Exception $e) {
            if ('Exception' === $e::class && str_starts_with($e->getMessage(), 'Failed calling ')) {
                throw $e->getPrevious() ?: $e;
            }
            throw $e;
        }

        if (\JSON_THROW_ON_ERROR & $this->encodingOptions) {
            return $this->setJson($data);
        }

        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return $this->setJson($data);
    }

    /**
     * Returns options used while encoding data to JSON.
     */
    public function getEncodingOptions(): int
    {
        return $this->encodingOptions;
    }

    /**
     * Sets options used while encoding data to JSON.
     *
     * @return $this
     */
    public function setEncodingOptions(int $encodingOptions): static
    {
        $this->encodingOptions = $encodingOptions;

        return $this->setData(json_decode($this->data));
    }

    /**
     * Updates the content and headers according to the JSON data and callback.
     *
     * @return $this
     */
    protected function update(): static
    {
        if (null !== $this->callback) {
            // Not using application/javascript for compatibility reasons with older browsers.
            $this->headers->set('Content-Type', 'text/javascript');

            return $this->setContent(\sprintf('/**/%s(%s);', $this->callback, $this->data));
        }

        // Only set the header when there is none or when it equals 'text/javascript' (from a previous update with callback)
        // in order to not overwrite a custom definition.
        if (!$this->headers->has('Content-Type') || 'text/javascript' === $this->headers->get('Content-Type')) {
            $this->headers->set('Content-Type', 'application/json');
        }

        return $this->setContent($this->data);
    }
}
