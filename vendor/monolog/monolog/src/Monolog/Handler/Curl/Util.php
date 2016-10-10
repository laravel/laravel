<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler\Curl;

class Util
{
    private static $retriableErrorCodes = array(
        CURLE_COULDNT_RESOLVE_HOST,
        CURLE_COULDNT_CONNECT,
        CURLE_HTTP_NOT_FOUND,
        CURLE_READ_ERROR,
        CURLE_OPERATION_TIMEOUTED,
        CURLE_HTTP_POST_ERROR,
        CURLE_SSL_CONNECT_ERROR,
    );

    /**
     * Executes a CURL request with optional retries and exception on failure
     *
     * @param  resource          $ch curl handler
     * @throws \RuntimeException
     */
    public static function execute($ch, $retries = 5, $closeAfterDone = true)
    {
        while ($retries--) {
            if (curl_exec($ch) === false) {
                $curlErrno = curl_errno($ch);

                if (false === in_array($curlErrno, self::$retriableErrorCodes, true) || !$retries) {
                    $curlError = curl_error($ch);

                    if ($closeAfterDone) {
                        curl_close($ch);
                    }

                    throw new \RuntimeException(sprintf('Curl error (code %s): %s', $curlErrno, $curlError));
                }

                continue;
            }

            if ($closeAfterDone) {
                curl_close($ch);
            }
            break;
        }
    }
}
