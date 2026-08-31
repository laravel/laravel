<?php

namespace Illuminate\Http\Concerns;

use Illuminate\Support\Str;

trait InteractsWithContentTypes
{
    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return Str::contains($this->header('CONTENT_TYPE') ?? '', ['/json', '+json']);
    }

    /**
     * Determine if the current request probably expects a JSON response.
     *
     * @return bool
     */
    public function expectsJson()
    {
        return ($this->ajax() && ! $this->pjax() && $this->acceptsAnyContentType()) || $this->wantsJson();
    }

    /**
     * Determine if the current request is asking for JSON.
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return isset($acceptable[0]) && Str::contains(strtolower($acceptable[0]), ['/json', '+json']);
    }

    /**
     * Determine if the current request is asking for Markdown.
     *
     * @return bool
     */
    public function wantsMarkdown()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return isset($acceptable[0]) && str_starts_with(strtolower($acceptable[0]), 'text/markdown');
    }

    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param  string|array  $contentTypes
     * @return bool
     */
    public function accepts($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();

        if (count($accepts) === 0) {
            return true;
        }

        $types = (array) $contentTypes;

        foreach ($accepts as $accept) {
            if ($accept && $pos = strpos($accept, ';')) {
                $accept = trim(substr($accept, 0, $pos));
            }

            if ($accept === '*/*' || $accept === '*') {
                return true;
            }

            foreach ($types as $type) {
                $accept = strtolower($accept);

                $type = strtolower($type);

                if ($this->matchesType($accept, $type) || $accept === strtok($type, '/').'/*') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return the most suitable content type from the given array based on content negotiation.
     *
     * @param  string|array  $contentTypes
     * @return string|null
     */
    public function prefers($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();

        $contentTypes = (array) $contentTypes;

        foreach ($accepts as $accept) {
            if ($accept && $pos = strpos($accept, ';')) {
                $accept = trim(substr($accept, 0, $pos));
            }

            if (in_array($accept, ['*/*', '*'])) {
                return $contentTypes[0];
            }

            foreach ($contentTypes as $contentType) {
                $type = $contentType;

                if (! is_null($mimeType = $this->getMimeType($contentType))) {
                    $type = $mimeType;
                }

                $accept = strtolower($accept);

                $type = strtolower($type);

                if ($this->matchesType($type, $accept) || $accept === strtok($type, '/').'/*') {
                    return $contentType;
                }
            }
        }
    }

    /**
     * Determine if the current request accepts any content type.
     *
     * @return bool
     */
    public function acceptsAnyContentType()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return count($acceptable) === 0 || (
            isset($acceptable[0]) && ($acceptable[0] === '*/*' || $acceptable[0] === '*')
        );
    }

    /**
     * Determines whether a request accepts JSON.
     *
     * @return bool
     */
    public function acceptsJson()
    {
        return $this->accepts('application/json');
    }

    /**
     * Determines whether a request accepts Markdown.
     *
     * @return bool
     */
    public function acceptsMarkdown()
    {
        return $this->accepts('text/markdown');
    }

    /**
     * Determines whether a request accepts HTML.
     *
     * @return bool
     */
    public function acceptsHtml()
    {
        return $this->accepts('text/html');
    }

    /**
     * Determine if the given content types match.
     *
     * @param  string  $actual
     * @param  string  $type
     * @return bool
     */
    public static function matchesType($actual, $type)
    {
        if ($actual === $type) {
            return true;
        }

        $split = explode('/', $actual);

        return isset($split[1]) && preg_match('#'.preg_quote($split[0], '#').'/.+\+'.preg_quote($split[1], '#').'#', $type);
    }

    /**
     * Get the data format expected in the response.
     *
     * @param  string  $default
     * @return string
     */
    public function format($default = 'html')
    {
        foreach ($this->getAcceptableContentTypes() as $type) {
            if ($format = $this->getFormat($type)) {
                return $format;
            }
        }

        return $default;
    }
}
