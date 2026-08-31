<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Loader;

use Symfony\Component\Translation\Exception\InvalidResourceException;

/**
 * @copyright Copyright (c) 2010, Union of RAD http://union-of-rad.org (http://lithify.me/)
 */
class MoFileLoader extends FileLoader
{
    /**
     * Magic used for validating the format of an MO file as well as
     * detecting if the machine used to create that file was little endian.
     */
    public const MO_LITTLE_ENDIAN_MAGIC = 0x950412DE;

    /**
     * Magic used for validating the format of an MO file as well as
     * detecting if the machine used to create that file was big endian.
     */
    public const MO_BIG_ENDIAN_MAGIC = 0xDE120495;

    /**
     * The size of the header of an MO file in bytes.
     */
    public const MO_HEADER_SIZE = 28;

    /**
     * Parses machine object (MO) format, independent of the machine's endian it
     * was created on. Both 32bit and 64bit systems are supported.
     */
    protected function loadResource(string $resource): array
    {
        $stream = fopen($resource, 'r');

        $stat = fstat($stream);

        if ($stat['size'] < self::MO_HEADER_SIZE) {
            throw new InvalidResourceException('MO stream content has an invalid format.');
        }
        $magic = unpack('V1', fread($stream, 4));
        $magic = hexdec(substr(dechex(current($magic)), -8));

        if (self::MO_LITTLE_ENDIAN_MAGIC == $magic) {
            $isBigEndian = false;
        } elseif (self::MO_BIG_ENDIAN_MAGIC == $magic) {
            $isBigEndian = true;
        } else {
            throw new InvalidResourceException('MO stream content has an invalid format.');
        }

        // formatRevision
        $this->readLong($stream, $isBigEndian);
        $count = $this->readLong($stream, $isBigEndian);
        $offsetId = $this->readLong($stream, $isBigEndian);
        $offsetTranslated = $this->readLong($stream, $isBigEndian);
        // sizeHashes
        $this->readLong($stream, $isBigEndian);
        // offsetHashes
        $this->readLong($stream, $isBigEndian);

        $messages = [];

        for ($i = 0; $i < $count; ++$i) {
            $pluralId = null;
            $translated = null;

            fseek($stream, $offsetId + $i * 8);

            $length = $this->readLong($stream, $isBigEndian);
            $offset = $this->readLong($stream, $isBigEndian);

            if ($length < 1) {
                continue;
            }

            fseek($stream, $offset);
            $singularId = fread($stream, $length);

            if (str_contains($singularId, "\000")) {
                [$singularId, $pluralId] = explode("\000", $singularId);
            }

            fseek($stream, $offsetTranslated + $i * 8);
            $length = $this->readLong($stream, $isBigEndian);
            $offset = $this->readLong($stream, $isBigEndian);

            if ($length < 1) {
                continue;
            }

            fseek($stream, $offset);
            $translated = fread($stream, $length);

            if (str_contains($translated, "\000")) {
                $translated = explode("\000", $translated);
            }

            $ids = ['singular' => $singularId, 'plural' => $pluralId];
            $item = compact('ids', 'translated');

            if (!empty($item['ids']['singular'])) {
                $id = $item['ids']['singular'];
                if (isset($item['ids']['plural'])) {
                    $id .= '|'.$item['ids']['plural'];
                }
                $messages[$id] = stripcslashes(implode('|', (array) $item['translated']));
            }
        }

        fclose($stream);

        return array_filter($messages);
    }

    /**
     * Reads an unsigned long from stream respecting endianness.
     *
     * @param resource $stream
     */
    private function readLong($stream, bool $isBigEndian): int
    {
        $result = unpack($isBigEndian ? 'N1' : 'V1', fread($stream, 4));
        $result = current($result);

        return (int) substr($result, -8);
    }
}
