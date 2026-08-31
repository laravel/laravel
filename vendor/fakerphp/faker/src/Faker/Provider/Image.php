<?php

namespace Faker\Provider;

/**
 * Depends on image generation from http://lorempixel.com/
 */
class Image extends Base
{
    /**
     * @var string
     */
    public const BASE_URL = 'https://via.placeholder.com';

    public const FORMAT_JPG = 'jpg';
    public const FORMAT_JPEG = 'jpeg';
    public const FORMAT_PNG = 'png';

    /**
     * @var array
     *
     * @deprecated Categories are no longer used as a list in the placeholder API but referenced as string instead
     */
    protected static $categories = [
        'abstract', 'animals', 'business', 'cats', 'city', 'food', 'nightlife',
        'fashion', 'people', 'nature', 'sports', 'technics', 'transport',
    ];

    /**
     * Generate the URL that will return a random image
     *
     * Set randomize to false to remove the random GET parameter at the end of the url.
     *
     * @example 'http://via.placeholder.com/640x480.png/CCCCCC?text=well+hi+there'
     *
     * @param int         $width
     * @param int         $height
     * @param string|null $category
     * @param bool        $randomize
     * @param string|null $word
     * @param bool        $gray
     * @param string      $format
     *
     * @return string
     */
    public static function imageUrl(
        $width = 640,
        $height = 480,
        $category = null,
        $randomize = true,
        $word = null,
        $gray = false,
        $format = 'png'
    ) {
        trigger_deprecation(
            'fakerphp/faker',
            '1.20',
            'Provider is deprecated and will no longer be available in Faker 2. Please use a custom provider instead',
        );

        // Validate image format
        $imageFormats = static::getFormats();

        if (!in_array(strtolower($format), $imageFormats, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid image format "%s". Allowable formats are: %s',
                $format,
                implode(', ', $imageFormats),
            ));
        }

        $size = sprintf('%dx%d.%s', $width, $height, $format);

        $imageParts = [];

        if ($category !== null) {
            $imageParts[] = $category;
        }

        if ($word !== null) {
            $imageParts[] = $word;
        }

        if ($randomize === true) {
            $imageParts[] = Lorem::word();
        }

        $backgroundColor = $gray === true ? 'CCCCCC' : str_replace('#', '', Color::safeHexColor());

        return sprintf(
            '%s/%s/%s%s',
            self::BASE_URL,
            $size,
            $backgroundColor,
            count($imageParts) > 0 ? '?text=' . urlencode(implode(' ', $imageParts)) : '',
        );
    }

    /**
     * Download a remote random image to disk and return its location
     *
     * Requires curl, or allow_url_fopen to be on in php.ini.
     *
     * @example '/path/to/dir/13b73edae8443990be1aa8f1a483bc27.png'
     *
     * @return bool|string
     */
    public static function image(
        $dir = null,
        $width = 640,
        $height = 480,
        $category = null,
        $fullPath = true,
        $randomize = true,
        $word = null,
        $gray = false,
        $format = 'png'
    ) {
        trigger_deprecation(
            'fakerphp/faker',
            '1.20',
            'Provider is deprecated and will no longer be available in Faker 2. Please use a custom provider instead',
        );

        $dir = null === $dir ? sys_get_temp_dir() : $dir; // GNU/Linux / OS X / Windows compatible

        // Validate directory path
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('Cannot write to directory "%s"', $dir));
        }

        // Generate a random filename. Use the server address so that a file
        // generated at the same time on a different server won't have a collision.
        $name = md5(uniqid(empty($_SERVER['SERVER_ADDR']) ? '' : $_SERVER['SERVER_ADDR'], true));
        $filename = sprintf('%s.%s', $name, $format);
        $filepath = $dir . DIRECTORY_SEPARATOR . $filename;

        $url = static::imageUrl($width, $height, $category, $randomize, $word, $gray, $format);

        // save file
        if (function_exists('curl_exec')) {
            // use cURL
            $fp = fopen($filepath, 'w');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            $success = curl_exec($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            fclose($fp);
            curl_close($ch);

            if (!$success) {
                unlink($filepath);

                // could not contact the distant URL or HTTP error - fail silently.
                return false;
            }
        } elseif (ini_get('allow_url_fopen')) {
            // use remote fopen() via copy()
            $success = copy($url, $filepath);

            if (!$success) {
                // could not contact the distant URL or HTTP error - fail silently.
                return false;
            }
        } else {
            return new \RuntimeException('The image formatter downloads an image from a remote HTTP server. Therefore, it requires that PHP can request remote hosts, either via cURL or fopen()');
        }

        return $fullPath ? $filepath : $filename;
    }

    public static function getFormats(): array
    {
        trigger_deprecation(
            'fakerphp/faker',
            '1.20',
            'Provider is deprecated and will no longer be available in Faker 2. Please use a custom provider instead',
        );

        return array_keys(static::getFormatConstants());
    }

    public static function getFormatConstants(): array
    {
        trigger_deprecation(
            'fakerphp/faker',
            '1.20',
            'Provider is deprecated and will no longer be available in Faker 2. Please use a custom provider instead',
        );

        return [
            static::FORMAT_JPG => constant('IMAGETYPE_JPEG'),
            static::FORMAT_JPEG => constant('IMAGETYPE_JPEG'),
            static::FORMAT_PNG => constant('IMAGETYPE_PNG'),
        ];
    }
}
