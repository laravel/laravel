<?php

namespace Illuminate\Filesystem;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use RuntimeException;

class LocalFilesystemAdapter extends FilesystemAdapter
{
    use Conditionable;

    /**
     * The name of the filesystem disk.
     *
     * @var string
     */
    protected $disk;

    /**
     * Indicates if signed URLs should serve corresponding files.
     *
     * @var bool
     */
    protected $shouldServeSignedUrls = false;

    /**
     * The Closure that should be used to resolve the URL generator.
     *
     * @var \Closure
     */
    protected $urlGeneratorResolver;

    /**
     * Determine if temporary URLs can be generated.
     *
     * @return bool
     */
    public function providesTemporaryUrls()
    {
        return $this->temporaryUrlCallback || (
            $this->shouldServeSignedUrls && $this->urlGeneratorResolver instanceof Closure
        );
    }

    /**
     * Determine if temporary upload URLs can be generated.
     *
     * @return bool
     */
    public function providesTemporaryUploadUrls()
    {
        return $this->temporaryUploadUrlCallback || (
            $this->shouldServeSignedUrls && $this->urlGeneratorResolver instanceof Closure
        );
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @param  array  $options
     * @return string
     */
    public function temporaryUrl($path, $expiration, array $options = [])
    {
        if ($this->temporaryUrlCallback) {
            return $this->temporaryUrlCallback->bindTo($this, static::class)(
                $path, $expiration, $options
            );
        }

        if (! $this->providesTemporaryUrls()) {
            throw new RuntimeException('This driver does not support creating temporary URLs.');
        }

        $url = call_user_func($this->urlGeneratorResolver);

        return $url->to($url->temporarySignedRoute(
            'storage.'.$this->disk,
            $expiration,
            ['path' => strtr(rawurlencode($path), ['%2F' => '/'])],
            absolute: false
        ));
    }

    /**
     * Get a temporary upload URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @param  array  $options
     * @return array
     */
    public function temporaryUploadUrl($path, $expiration, array $options = [])
    {
        if ($this->temporaryUploadUrlCallback) {
            return $this->temporaryUploadUrlCallback->bindTo($this, static::class)(
                $path, $expiration, $options
            );
        }

        if (! $this->providesTemporaryUploadUrls()) {
            throw new RuntimeException('This driver does not support creating temporary upload URLs.');
        }

        $url = call_user_func($this->urlGeneratorResolver);

        return [
            'url' => $url->to($url->temporarySignedRoute(
                'storage.'.$this->disk.'.upload',
                $expiration,
                ['path' => strtr(rawurlencode($path), ['%2F' => '/']), 'upload' => true],
                absolute: false
            )),
            'headers' => [],
        ];
    }

    /**
     * Specify the name of the disk the adapter is managing.
     *
     * @param  string  $disk
     * @return $this
     */
    public function diskName(string $disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Indicate that signed URLs should serve the corresponding files.
     *
     * @param  bool  $serve
     * @param  \Closure|null  $urlGeneratorResolver
     * @return $this
     */
    public function shouldServeSignedUrls(bool $serve = true, ?Closure $urlGeneratorResolver = null)
    {
        $this->shouldServeSignedUrls = $serve;
        $this->urlGeneratorResolver = $urlGeneratorResolver;

        return $this;
    }
}
