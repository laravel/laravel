<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests\Iterator;

class MockSplFileInfo extends \SplFileInfo
{
    const   TYPE_DIRECTORY = 1;
    const   TYPE_FILE      = 2;
    const   TYPE_UNKNOWN   = 3;

    private $contents         = null;
    private $mode             = null;
    private $type             = null;
    private $relativePath     = null;
    private $relativePathname = null;

    public function __construct($param)
    {
        if (is_string($param)) {
            parent::__construct($param);
        } elseif (is_array($param)) {
            $defaults = array(
              'name'             => 'file.txt',
              'contents'         => null,
              'mode'             => null,
              'type'             => null,
              'relativePath'     => null,
              'relativePathname' => null,
            );
            $defaults = array_merge($defaults, $param);
            parent::__construct($defaults['name']);
            $this->setContents($defaults['contents']);
            $this->setMode($defaults['mode']);
            $this->setType($defaults['type']);
            $this->setRelativePath($defaults['relativePath']);
            $this->setRelativePathname($defaults['relativePathname']);
        } else {
            throw new \RuntimeException(sprintf('Incorrect parameter "%s"', $param));
        }
    }

    public function isFile()
    {
        if (null === $this->type) {
            return preg_match('/file/', $this->getFilename());
        };

        return self::TYPE_FILE === $this->type;
    }

    public function isDir()
    {
        if (null === $this->type) {
            return preg_match('/directory/', $this->getFilename());
        }

        return self::TYPE_DIRECTORY === $this->type;
    }

    public function isReadable()
    {
        if (null === $this->mode) {
            return preg_match('/r\+/', $this->getFilename());
        }

        return preg_match('/r\+/', $this->mode);
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function setType($type)
    {
        if (is_string($type)) {
            switch ($type) {
                case 'directory':
                    $this->type = self::TYPE_DIRECTORY;
                case 'd':
                    $this->type = self::TYPE_DIRECTORY;
                    break;
                case 'file':
                    $this->type = self::TYPE_FILE;
                case 'f':
                    $this->type = self::TYPE_FILE;
                    break;
                default:
                    $this->type = self::TYPE_UNKNOWN;
            }
        } else {
            $this->type = $type;
        }
    }

    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;
    }

    public function setRelativePathname($relativePathname)
    {
        $this->relativePathname = $relativePathname;
    }

    public function getRelativePath()
    {
        return $this->relativePath;
    }

    public function getRelativePathname()
    {
        return $this->relativePathname;
    }
}
