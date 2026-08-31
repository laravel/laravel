<?php

namespace Illuminate\Http;

use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class UploadedFile extends SymfonyUploadedFile
{
    use FileHelpers, Macroable;

    /**
     * Begin creating a new file fake.
     *
     * @return \Illuminate\Http\Testing\FileFactory
     */
    public static function fake()
    {
        return new FileFactory;
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param  string  $path
     * @param  array|string  $options
     * @return string|false
     */
    public function store($path = '', $options = [])
    {
        return $this->storeAs($path, $this->hashName(), $this->parseOptions($options));
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param  string  $path
     * @param  array|string  $options
     * @return string|false
     */
    public function storePublicly($path = '', $options = [])
    {
        $options = $this->parseOptions($options);

        $options['visibility'] = 'public';

        return $this->storeAs($path, $this->hashName(), $options);
    }

    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param  string  $path
     * @param  array|string|null  $name
     * @param  array|string  $options
     * @return string|false
     */
    public function storePubliclyAs($path, $name = null, $options = [])
    {
        if (is_null($name) || is_array($name)) {
            [$path, $name, $options] = ['', $path, $name ?? []];
        }

        $options = $this->parseOptions($options);

        $options['visibility'] = 'public';

        return $this->storeAs($path, $name, $options);
    }

    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param  string  $path
     * @param  array|string|null  $name
     * @param  array|string  $options
     * @return string|false
     */
    public function storeAs($path, $name = null, $options = [])
    {
        if (is_null($name) || is_array($name)) {
            [$path, $name, $options] = ['', $path, $name ?? []];
        }

        $options = $this->parseOptions($options);

        $disk = Arr::pull($options, 'disk');

        return Container::getInstance()->make(FilesystemFactory::class)->disk($disk)->putFileAs(
            $path, $this, $name, $options
        );
    }

    /**
     * Get the contents of the uploaded file.
     *
     * @return false|string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get()
    {
        if (! $this->isValid()) {
            throw new FileNotFoundException("File does not exist at path {$this->getPathname()}.");
        }

        return file_get_contents($this->getPathname());
    }

    /**
     * Get the file's extension supplied by the client.
     *
     * @return string
     */
    public function clientExtension()
    {
        return $this->guessClientExtension();
    }

    /**
     * Create a new file instance from a base instance.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $file
     * @param  bool  $test
     * @return static
     */
    public static function createFromBase(SymfonyUploadedFile $file, $test = false)
    {
        return $file instanceof static ? $file : new static(
            $file->getPathname(),
            $file->getClientOriginalPath(),
            $file->getClientMimeType(),
            $file->getError(),
            $test
        );
    }

    /**
     * Parse and format the given options.
     *
     * @param  array|string  $options
     * @return array
     */
    protected function parseOptions($options)
    {
        if (is_string($options)) {
            $options = ['disk' => $options];
        }

        return $options;
    }
}
