<?php

namespace Illuminate\Filesystem;

use ErrorException;
use FilesystemIterator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Mime\MimeTypes;

class Filesystem
{
    use Conditionable, Macroable;

    /**
     * Determine if a file or directory exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Determine if a file or directory is missing.
     *
     * @param  string  $path
     * @return bool
     */
    public function missing($path)
    {
        return ! $this->exists($path);
    }

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @param  bool  $lock
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($path, $lock = false)
    {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }

        throw new FileNotFoundException("File does not exist at path {$path}.");
    }

    /**
     * Get the contents of a file as decoded JSON.
     *
     * @param  string  $path
     * @param  int  $flags
     * @param  bool  $lock
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function json($path, $flags = 0, $lock = false)
    {
        return json_decode($this->get($path, $lock), true, 512, $flags);
    }

    /**
     * Get contents of a file with shared access.
     *
     * @param  string  $path
     * @return string
     */
    public function sharedGet($path)
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = stream_get_contents($handle);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * Get the returned value of a file.
     *
     * @param  string  $path
     * @param  array  $data
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getRequire($path, array $data = [])
    {
        if ($this->isFile($path)) {
            $__path = $path;
            $__data = $data;

            return (static function () use ($__path, $__data) {
                extract($__data, EXTR_SKIP);

                return require $__path;
            })();
        }

        throw new FileNotFoundException("File does not exist at path {$path}.");
    }

    /**
     * Require the given file once.
     *
     * @param  string  $path
     * @param  array  $data
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function requireOnce($path, array $data = [])
    {
        if ($this->isFile($path)) {
            $__path = $path;
            $__data = $data;

            return (static function () use ($__path, $__data) {
                extract($__data, EXTR_SKIP);

                return require_once $__path;
            })();
        }

        throw new FileNotFoundException("File does not exist at path {$path}.");
    }

    /**
     * Get the contents of a file one line at a time.
     *
     * @param  string  $path
     * @return \Illuminate\Support\LazyCollection
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function lines($path)
    {
        if (! $this->isFile($path)) {
            throw new FileNotFoundException(
                "File does not exist at path {$path}."
            );
        }

        return new LazyCollection(function () use ($path) {
            $file = new SplFileObject($path);

            $file->setFlags(SplFileObject::DROP_NEW_LINE);

            while (! $file->eof()) {
                yield $file->fgets();
            }
        });
    }

    /**
     * Get the hash of the file at the given path.
     *
     * @param  string  $path
     * @param  string  $algorithm
     * @return string|false
     */
    public function hash($path, $algorithm = 'md5')
    {
        return hash_file($algorithm, $path);
    }

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  bool  $lock
     * @return int|bool
     */
    public function put($path, $contents, $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Write the contents of a file, replacing it atomically if it already exists.
     *
     * @param  string  $path
     * @param  string  $content
     * @param  int|null  $mode
     * @return void
     */
    public function replace($path, $content, $mode = null)
    {
        // If the path already exists and is a symlink, get the real path...
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;

        $tempPath = tempnam(dirname($path), basename($path));

        // Fix permissions of tempPath because `tempnam()` creates it with permissions set to 0600...
        if (! is_null($mode)) {
            @chmod($tempPath, $mode);
        } else {
            @chmod($tempPath, 0777 - umask());
        }

        file_put_contents($tempPath, $content);

        rename($tempPath, $path);
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  array|string  $search
     * @param  array|string  $replace
     * @param  string  $path
     * @return void
     */
    public function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public function prepend($path, $data)
    {
        if ($this->exists($path)) {
            return $this->put($path, $data.$this->get($path));
        }

        return $this->put($path, $data);
    }

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @param  bool  $lock
     * @return int
     */
    public function append($path, $data, $lock = false)
    {
        return file_put_contents($path, $data, FILE_APPEND | ($lock ? LOCK_EX : 0));
    }

    /**
     * Get or set UNIX mode of a file or directory.
     *
     * @param  string  $path
     * @param  int|null  $mode
     * @return mixed
     */
    public function chmod($path, $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }

        return substr(sprintf('%o', fileperms($path)), -4);
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     * @return bool
     */
    public function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (@unlink($path)) {
                    clearstatcache(false, $path);
                } else {
                    $success = false;
                }
            } catch (ErrorException) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Move a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public function move($path, $target)
    {
        return rename($path, $target);
    }

    /**
     * Copy a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public function copy($path, $target)
    {
        return copy($path, $target);
    }

    /**
     * Create a symlink to the target file or directory. On Windows, a hard link is created if the target is a file.
     *
     * @param  string  $target
     * @param  string  $link
     * @return bool|null
     */
    public function link($target, $link)
    {
        if (! windows_os()) {
            if (function_exists('symlink')) {
                return symlink($target, $link);
            } else {
                return exec('ln -s '.escapeshellarg($target).' '.escapeshellarg($link)) !== false;
            }
        }

        $mode = $this->isDirectory($target) ? 'J' : 'H';

        exec("mklink /{$mode} ".escapeshellarg($link).' '.escapeshellarg($target));
    }

    /**
     * Create a relative symlink to the target file or directory.
     *
     * @param  string  $target
     * @param  string  $link
     * @return void
     *
     * @throws \RuntimeException
     */
    public function relativeLink($target, $link)
    {
        if (! class_exists(SymfonyFilesystem::class)) {
            throw new RuntimeException(
                'To enable support for relative links, please install the symfony/filesystem package.'
            );
        }

        $relativeTarget = (new SymfonyFilesystem)->makePathRelative($target, dirname($link));

        $this->link($this->isFile($target) ? rtrim($relativeTarget, '/') : $relativeTarget, $link);
    }

    /**
     * Extract the file name from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function name($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Extract the trailing name component from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function basename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Extract the parent directory from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Guess the file extension from the MIME type of a given file.
     *
     * @param  string  $path
     * @return string|null
     *
     * @throws \RuntimeException
     */
    public function guessExtension($path)
    {
        if (! class_exists(MimeTypes::class)) {
            throw new RuntimeException(
                'To enable support for guessing extensions, please install the symfony/mime package.'
            );
        }

        return (new MimeTypes)->getExtensions($this->mimeType($path))[0] ?? null;
    }

    /**
     * Get the file type of a given file.
     *
     * @param  string  $path
     * @return string|false
     */
    public function type($path)
    {
        return filetype($path);
    }

    /**
     * Get the MIME type of a given file.
     *
     * @param  string  $path
     * @return string|false
     */
    public function mimeType($path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public function size($path)
    {
        return filesize($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path)
    {
        return filemtime($path);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function isDirectory($directory)
    {
        return is_dir($directory);
    }

    /**
     * Determine if the given path is a directory that does not contain any other files or directories.
     *
     * @param  string  $directory
     * @param  bool  $ignoreDotFiles
     * @return bool
     */
    public function isEmptyDirectory($directory, $ignoreDotFiles = false)
    {
        return ! Finder::create()->ignoreDotFiles($ignoreDotFiles)->in($directory)->depth(0)->hasResults();
    }

    /**
     * Determine if the given path is readable.
     *
     * @param  string  $path
     * @return bool
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param  string  $path
     * @return bool
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * Determine if two files are the same by comparing their hashes.
     *
     * @param  string  $firstFile
     * @param  string  $secondFile
     * @return bool
     */
    public function hasSameHash($firstFile, $secondFile)
    {
        $hash = @hash_file('xxh128', $firstFile);

        return $hash && hash_equals($hash, (string) @hash_file('xxh128', $secondFile));
    }

    /**
     * Determine if the given path is a file.
     *
     * @param  string  $file
     * @return bool
     */
    public function isFile($file)
    {
        return is_file($file);
    }

    /**
     * Find path names matching a given pattern.
     *
     * @param  string  $pattern
     * @param  int  $flags
     * @return array
     */
    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param  string  $directory
     * @param  bool  $hidden
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function files($directory, $hidden = false, array|string|int $depth = 0)
    {
        return iterator_to_array(
            Finder::create()->files()->ignoreDotFiles(! $hidden)->in($directory)->depth($depth)->sortByName(),
            false
        );
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param  string  $directory
     * @param  bool  $hidden
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function allFiles($directory, $hidden = false)
    {
        return $this->files($directory, $hidden, []);
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string  $directory
     * @return array
     */
    public function directories($directory, array|string|int $depth = 0)
    {
        $directories = [];

        foreach (Finder::create()->in($directory)->directories()->depth($depth)->sortByName() as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }

    /**
     * Get all the directories within a given directory (recursive).
     *
     * @return array
     */
    public function allDirectories(string $directory): array
    {
        return $this->directories($directory, []);
    }

    /**
     * Ensure a directory exists.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @return void
     */
    public function ensureDirectoryExists($path, $mode = 0755, $recursive = true)
    {
        if (! $this->isDirectory($path)) {
            $this->makeDirectory($path, $mode, $recursive);
        }
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @param  bool  $force
     * @return bool
     */
    public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }

        return mkdir($path, $mode, $recursive);
    }

    /**
     * Move a directory.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  bool  $overwrite
     * @return bool
     */
    public function moveDirectory($from, $to, $overwrite = false)
    {
        if ($overwrite && $this->isDirectory($to) && ! $this->deleteDirectory($to)) {
            return false;
        }

        return @rename($from, $to) === true;
    }

    /**
     * Copy a directory from one location to another.
     *
     * @param  string  $directory
     * @param  string  $destination
     * @param  int|null  $options
     * @return bool
     */
    public function copyDirectory($directory, $destination, $options = null)
    {
        if (! $this->isDirectory($directory)) {
            return false;
        }

        $options = $options ?: FilesystemIterator::SKIP_DOTS;

        // If the destination directory does not actually exist, we will go ahead and
        // create it recursively, which just gets the destination prepared to copy
        // the files over. Once we make the directory we'll proceed the copying.
        $this->ensureDirectoryExists($destination, 0777);

        $items = new FilesystemIterator($directory, $options);

        foreach ($items as $item) {
            // As we spin through items, we will check to see if the current file is actually
            // a directory or a file. When it is actually a directory we will need to call
            // back into this function recursively to keep copying these nested folders.
            $target = $destination.'/'.$item->getBasename();

            if ($item->isDir()) {
                $path = $item->getPathname();

                if (! $this->copyDirectory($path, $target, $options)) {
                    return false;
                }
            }

            // If the current items is just a regular file, we will just copy this to the new
            // location and keep looping. If for some reason the copy fails we'll bail out
            // and return false, so the developer is aware that the copy process failed.
            elseif (! $this->copy($item->getPathname(), $target)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Recursively delete a directory.
     *
     * The directory itself may be optionally preserved.
     *
     * @param  string  $directory
     * @param  bool  $preserve
     * @return bool
     */
    public function deleteDirectory($directory, $preserve = false)
    {
        if (! $this->isDirectory($directory)) {
            return false;
        }

        $items = new FilesystemIterator($directory);

        foreach ($items as $item) {
            // If the item is a directory, we can just recurse into the function and
            // delete that sub-directory otherwise we'll just delete the file and
            // keep iterating through each file until the directory is cleaned.
            if ($item->isDir() && ! $item->isLink()) {
                $this->deleteDirectory($item->getPathname());
            }

            // If the item is just a file, we can go ahead and delete it since we're
            // just looping through and waxing all of the files in this directory
            // and calling directories recursively, so we delete the real path.
            else {
                $this->delete($item->getPathname());
            }
        }

        unset($items);

        if (! $preserve) {
            @rmdir($directory);
        }

        return true;
    }

    /**
     * Remove all of the directories within a given directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function deleteDirectories($directory)
    {
        $allDirectories = $this->directories($directory);

        if (! empty($allDirectories)) {
            foreach ($allDirectories as $directoryName) {
                $this->deleteDirectory($directoryName);
            }

            return true;
        }

        return false;
    }

    /**
     * Empty the specified directory of all files and folders.
     *
     * @param  string  $directory
     * @return bool
     */
    public function cleanDirectory($directory)
    {
        return $this->deleteDirectory($directory, true);
    }
}
