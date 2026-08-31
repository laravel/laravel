<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

use League\CommonMark\Exception\UnexpectedEncodingException;

/**
 * @psalm-immutable
 */
final class UrlEncoder
{
    private const ENCODE_CACHE = ['%00', '%01', '%02', '%03', '%04', '%05', '%06', '%07', '%08', '%09', '%0A', '%0B', '%0C', '%0D', '%0E', '%0F', '%10', '%11', '%12', '%13', '%14', '%15', '%16', '%17', '%18', '%19', '%1A', '%1B', '%1C', '%1D', '%1E', '%1F', '%20', '!', '%22', '#', '$', '%25', '&', "'", '(', ')', '*', '+', ',', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '%3C', '=', '%3E', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '%5B', '%5C', '%5D', '%5E', '_', '%60', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '%7B', '%7C', '%7D', '~', '%7F'];

    /**
     * @throws UnexpectedEncodingException if a non-UTF-8-compatible encoding is used
     *
     * @psalm-pure
     */
    public static function unescapeAndEncode(string $uri): string
    {
        // Optimization: if the URL only includes characters we know will be kept as-is, then just return the URL as-is.
        if (\preg_match('/^[A-Za-z0-9~!@#$&*()\-_=+;:,.\/?]+$/', $uri)) {
            return $uri;
        }

        if (! \mb_check_encoding($uri, 'UTF-8')) {
            throw new UnexpectedEncodingException('Unexpected encoding - UTF-8 or ASCII was expected');
        }

        $result = '';

        $chars = \mb_str_split($uri, 1, 'UTF-8');

        $l = \count($chars);
        for ($i = 0; $i < $l; $i++) {
            $code = $chars[$i];
            if ($code === '%' && $i + 2 < $l) {
                if (\preg_match('/^[0-9a-f]{2}$/i', $chars[$i + 1] . $chars[$i + 2]) === 1) {
                    $result .= '%' . $chars[$i + 1] . $chars[$i + 2];
                    $i      += 2;
                    continue;
                }
            }

            if (\strlen($code) === 1 && \ord($code) < 128) {
                $result .= self::ENCODE_CACHE[\ord($code)];
                continue;
            }

            $result .= \rawurlencode($code);
        }

        return $result;
    }
}
