<?php

namespace Illuminate\Http;

use Illuminate\Support\Str;

trait FileHelpers
{
    /**
     * The cache copy of the file's hash name.
     *
     * @var string|null
     */
    protected $hashName = null;

    /**
     * Get the fully-qualified path to the file.
     *
     * @return string
     */
    public function path()
    {
        return $this->getRealPath();
    }

    /**
     * Get the file's extension.
     *
     * @return string
     */
    public function extension()
    {
        return $this->guessExtension();
    }

    /**
     * Get a filename for the file.
     *
     * @param  string|null  $path
     * @return string
     */
    public function hashName($path = null)
    {
        if ($path) {
            $path = rtrim($path, '/').'/';
        }

        $hash = $this->hashName ?: $this->hashName = Str::random(40);

        if ($extension = $this->guessExtension()) {
            $extension = '.'.$extension;
        }

        return $path.$hash.$extension;
    }

    /**
     * Get the dimensions of the image (if applicable).
     *
     * @return array|null
     */
    public function dimensions()
    {
        return @getimagesize($this->getRealPath());
    }
}
