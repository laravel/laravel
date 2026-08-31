<?php

namespace Illuminate\Contracts\Filesystem;

interface Filesystem
{
    /**
     * The public visibility setting.
     *
     * @var string
     */
    const VISIBILITY_PUBLIC = 'public';

    /**
     * The private visibility setting.
     *
     * @var string
     */
    const VISIBILITY_PRIVATE = 'private';

    /**
     * Get the full path to the file that exists at the given relative path.
     *
     * @param  string  $path
     * @return string
     */
    public function path($path);

    /**
     * Determine if a file exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path);

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @return string|null
     */
    public function get($path);

    /**
     * Get a resource to read the file.
     *
     * @param  string  $path
     * @return resource|null The path resource or null on failure.
     */
    public function readStream($path);

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  \Psr\Http\Message\StreamInterface|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|resource  $contents
     * @param  mixed  $options
     * @return bool
     */
    public function put($path, $contents, $options = []);

    /**
     * Store the uploaded file on the disk.
     *
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string  $path
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|array|null  $file
     * @param  mixed  $options
     * @return string|false
     */
    public function putFile($path, $file = null, $options = []);

    /**
     * Store the uploaded file on the disk with a given name.
     *
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string  $path
     * @param  \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string|array|null  $file
     * @param  string|array|null  $name
     * @param  mixed  $options
     * @return string|false
     */
    public function putFileAs($path, $file, $name = null, $options = []);

    /**
     * Write a new file using a stream.
     *
     * @param  string  $path
     * @param  resource  $resource
     * @param  array  $options
     * @return bool
     */
    public function writeStream($path, $resource, array $options = []);

    /**
     * Get the visibility for the given path.
     *
     * @param  string  $path
     * @return string
     */
    public function getVisibility($path);

    /**
     * Set the visibility for the given path.
     *
     * @param  string  $path
     * @param  string  $visibility
     * @return bool
     */
    public function setVisibility($path, $visibility);

    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return bool
     */
    public function prepend($path, $data);

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return bool
     */
    public function append($path, $data);

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     * @return bool
     */
    public function delete($paths);

    /**
     * Copy a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function copy($from, $to);

    /**
     * Move a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function move($from, $to);

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public function size($path);

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path);

    /**
     * Get an array of all files in a directory.
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     * @return array<string>
     */
    public function files($directory = null, $recursive = false);

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param  string|null  $directory
     * @return array<string>
     */
    public function allFiles($directory = null);

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     * @return array<string>
     */
    public function directories($directory = null, $recursive = false);

    /**
     * Get all (recursive) of the directories within a given directory.
     *
     * @param  string|null  $directory
     * @return array<string>
     */
    public function allDirectories($directory = null);

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @return bool
     */
    public function makeDirectory($path);

    /**
     * Recursively delete a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function deleteDirectory($directory);
}
