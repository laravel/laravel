<?php

namespace Illuminate\Http\Testing;

use LogicException;

class FileFactory
{
    /**
     * Create a new fake file.
     *
     * @param  string  $name
     * @param  string|int  $kilobytes
     * @param  string|null  $mimeType
     * @return \Illuminate\Http\Testing\File
     */
    public function create($name, $kilobytes = 0, $mimeType = null)
    {
        if (is_string($kilobytes)) {
            return $this->createWithContent($name, $kilobytes);
        }

        return tap(new File($name, tmpfile()), function ($file) use ($kilobytes, $mimeType) {
            $file->sizeToReport = $kilobytes * 1024;
            $file->mimeTypeToReport = $mimeType;
        });
    }

    /**
     * Create a new fake file with content.
     *
     * @param  string  $name
     * @param  string  $content
     * @return \Illuminate\Http\Testing\File
     */
    public function createWithContent($name, $content)
    {
        $tmpfile = tmpfile();

        fwrite($tmpfile, $content);

        return tap(new File($name, $tmpfile), function ($file) use ($tmpfile) {
            $file->sizeToReport = fstat($tmpfile)['size'];
        });
    }

    /**
     * Create a new fake image.
     *
     * @param  string  $name
     * @param  int  $width
     * @param  int  $height
     * @return \Illuminate\Http\Testing\File
     *
     * @throws \LogicException
     */
    public function image($name, $width = 10, $height = 10)
    {
        return new File($name, $this->generateImage(
            $width, $height, pathinfo($name, PATHINFO_EXTENSION)
        ));
    }

    /**
     * Generate a dummy image of the given width and height.
     *
     * @param  int  $width
     * @param  int  $height
     * @param  string  $extension
     * @return resource
     *
     * @throws \LogicException
     */
    protected function generateImage($width, $height, $extension)
    {
        if (! function_exists('imagecreatetruecolor')) {
            throw new LogicException('GD extension is not installed.');
        }

        return tap(tmpfile(), function ($temp) use ($width, $height, $extension) {
            ob_start();

            $extension = in_array($extension, ['jpeg', 'png', 'gif', 'webp', 'wbmp', 'bmp'])
                ? strtolower($extension)
                : 'jpeg';

            $image = imagecreatetruecolor($width, $height);

            if (! function_exists($functionName = "image{$extension}")) {
                ob_get_clean();

                throw new LogicException("{$functionName} function is not defined and image cannot be generated.");
            }

            call_user_func($functionName, $image);

            fwrite($temp, ob_get_clean());
        });
    }
}
