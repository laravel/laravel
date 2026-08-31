<?php

namespace Illuminate\Routing;

class RouteUri
{
    /**
     * The route URI.
     *
     * @var string
     */
    public $uri;

    /**
     * The fields that should be used when resolving bindings.
     *
     * @var array
     */
    public $bindingFields = [];

    /**
     * Create a new route URI instance.
     *
     * @param  string  $uri
     * @param  array  $bindingFields
     */
    public function __construct(string $uri, array $bindingFields = [])
    {
        $this->uri = $uri;
        $this->bindingFields = $bindingFields;
    }

    /**
     * Parse the given URI.
     *
     * @param  string  $uri
     * @return static
     */
    public static function parse($uri)
    {
        preg_match_all('/\{([\w\:]+?)\??\}/', $uri, $matches);

        $bindingFields = [];

        foreach ($matches[0] as $match) {
            if (! str_contains($match, ':')) {
                continue;
            }

            $segments = explode(':', trim($match, '{}?'));

            $bindingFields[$segments[0]] = $segments[1];

            $uri = str_contains($match, '?')
                ? str_replace($match, '{'.$segments[0].'?}', $uri)
                : str_replace($match, '{'.$segments[0].'}', $uri);
        }

        return new static($uri, $bindingFields);
    }
}
