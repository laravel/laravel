<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * FileBag is a container for HTTP headers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * @api
 */
class FileBag extends ParameterBag
{
    static private $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');

    /**
     * Constructor.
     *
     * @param array $parameters An array of HTTP files
     *
     * @api
     */
    public function __construct(array $parameters = array())
    {
        $this->replace($parameters);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\HttpFoundation\ParameterBag::replace()
     *
     * @api
     */
    public function replace(array $files = array())
    {
        $this->parameters = array();
        $this->add($files);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\HttpFoundation\ParameterBag::set()
     *
     * @api
     */
    public function set($key, $value)
    {
        if (is_array($value) || $value instanceof UploadedFile) {
            parent::set($key, $this->convertFileInformation($value));
        } else {
            throw new \InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\HttpFoundation\ParameterBag::add()
     *
     * @api
     */
    public function add(array $files = array())
    {
        foreach ($files as $key => $file) {
            $this->set($key, $file);
        }
    }

    /**
     * Converts uploaded files to UploadedFile instances.
     *
     * @param  array|UploadedFile $file A (multi-dimensional) array of uploaded file information
     *
     * @return array A (multi-dimensional) array of UploadedFile instances
     */
    protected function convertFileInformation($file)
    {
        if ($file instanceof UploadedFile) {
            return $file;
        }

        $file = $this->fixPhpFilesArray($file);
        if (is_array($file)) {
            $keys = array_keys($file);
            sort($keys);

            if ($keys == self::$fileKeys) {
                if (UPLOAD_ERR_NO_FILE == $file['error']) {
                    $file = null;
                } else {
                    $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
                }
            } else {
                $file = array_map(array($this, 'convertFileInformation'), $file);
            }
        }

        return $file;
    }

    /**
     * Fixes a malformed PHP $_FILES array.
     *
     * PHP has a bug that the format of the $_FILES array differs, depending on
     * whether the uploaded file fields had normal field names or array-like
     * field names ("normal" vs. "parent[child]").
     *
     * This method fixes the array to look like the "normal" $_FILES array.
     *
     * It's safe to pass an already converted array, in which case this method
     * just returns the original array unmodified.
     *
     * @param  array $data
     *
     * @return array
     */
    protected function fixPhpFilesArray($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $keys = array_keys($data);
        sort($keys);

        if (self::$fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
            return $data;
        }

        $files = $data;
        foreach (self::$fileKeys as $k) {
            unset($files[$k]);
        }

        foreach (array_keys($data['name']) as $key) {
            $files[$key] = $this->fixPhpFilesArray(array(
                'error'    => $data['error'][$key],
                'name'     => $data['name'][$key],
                'type'     => $data['type'][$key],
                'tmp_name' => $data['tmp_name'][$key],
                'size'     => $data['size'][$key]
            ));
        }

        return $files;
    }
}
