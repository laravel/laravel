<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler\Curl;

use CurlHandle;

/**
 * This class is marked as internal and it is not under the BC promise of the package.
 *
 * @internal
 */
final class Util
{
    /** @var array<int> */
    private static array $retriableErrorCodes = [
        CURLE_COULDNT_RESOLVE_HOST,
        CURLE_COULDNT_CONNECT,
        CURLE_HTTP_NOT_FOUND,
        CURLE_READ_ERROR,
        CURLE_OPERATION_TIMEOUTED,
        CURLE_HTTP_POST_ERROR,
        CURLE_SSL_CONNECT_ERROR,
    ];

    /**
     * Executes a CURL request with optional retries and exception on failure
     *
     * @param  CurlHandle  $ch curl handler
     * @return bool|string @see curl_exec
     */
    public static function execute(CurlHandle $ch, int $retries = 5): bool|string
    {
        while ($retries > 0) {
            $retries--;
            $curlResponse = curl_exec($ch);
            if ($curlResponse === false) {
                $curlErrno = curl_errno($ch);

                if (false === \in_array($curlErrno, self::$retriableErrorCodes, true) || $retries === 0) {
                    $curlError = curl_error($ch);

                    throw new \RuntimeException(sprintf('Curl error (code %d): %s', $curlErrno, $curlError));
                }
                continue;
            }

            return $curlResponse;
        }
        return false;
    }
}
