<?php

namespace Illuminate\Http;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
{
    use ResponseTrait, Macroable {
        Macroable::__call as macroCall;
    }

    /**
     * Create a new JSON response instance.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @param  bool  $json
     */
    public function __construct($data = null, $status = 200, $headers = [], $options = 0, $json = false)
    {
        $this->encodingOptions = $options;

        parent::__construct($data, $status, $headers, $json);
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    #[\Override]
    public static function fromJsonString(?string $data = null, int $status = 200, array $headers = []): static
    {
        return new static($data, $status, $headers, 0, true);
    }

    /**
     * Sets the JSONP callback.
     *
     * @param  string|null  $callback
     * @return $this
     */
    public function withCallback($callback = null)
    {
        return $this->setCallback($callback);
    }

    /**
     * Get the decoded JSON data from the response.
     *
     * @param  bool  $assoc
     * @param  int  $depth
     * @return mixed
     */
    public function getData($assoc = false, $depth = 512)
    {
        return json_decode($this->data, $assoc, $depth);
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    #[\Override]
    public function setData($data = []): static
    {
        $this->original = $data;

        // Ensure json_last_error() is cleared...
        json_decode('[]');

        $this->data = match (true) {
            $data instanceof Jsonable => $data->toJson($this->encodingOptions),
            $data instanceof JsonSerializable => json_encode($data->jsonSerialize(), $this->encodingOptions),
            $data instanceof Arrayable => json_encode($data->toArray(), $this->encodingOptions),
            default => json_encode($data, $this->encodingOptions),
        };

        if (! $this->hasValidJson(json_last_error())) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        return $this->update();
    }

    /**
     * Determine if an error occurred during JSON encoding.
     *
     * @param  int  $jsonError
     * @return bool
     */
    protected function hasValidJson($jsonError)
    {
        if ($jsonError === JSON_ERROR_NONE) {
            return true;
        }

        return $this->hasEncodingOption(JSON_PARTIAL_OUTPUT_ON_ERROR) &&
                    in_array($jsonError, [
                        JSON_ERROR_RECURSION,
                        JSON_ERROR_INF_OR_NAN,
                        JSON_ERROR_UNSUPPORTED_TYPE,
                    ]);
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    #[\Override]
    public function setEncodingOptions($options): static
    {
        $this->encodingOptions = (int) $options;

        return $this->setData($this->getData());
    }

    /**
     * Determine if a JSON encoding option is set.
     *
     * @param  int  $option
     * @return bool
     */
    public function hasEncodingOption($option)
    {
        return (bool) ($this->encodingOptions & $option);
    }
}
