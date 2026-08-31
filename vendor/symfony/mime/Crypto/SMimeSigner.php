<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Crypto;

use Symfony\Component\Mime\Exception\RuntimeException;
use Symfony\Component\Mime\Message;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
final class SMimeSigner extends SMime
{
    private string $signCertificate;
    private string|array $signPrivateKey;
    private int $signOptions;
    private ?string $extraCerts;

    /**
     * @param string      $certificate          The path of the file containing the signing certificate (in PEM format)
     * @param string      $privateKey           The path of the file containing the private key (in PEM format)
     * @param string|null $privateKeyPassphrase A passphrase of the private key (if any)
     * @param string|null $extraCerts           The path of the file containing intermediate certificates (in PEM format) needed by the signing certificate
     * @param int|null    $signOptions          Bitwise operator options for openssl_pkcs7_sign() (@see https://secure.php.net/manual/en/openssl.pkcs7.flags.php)
     */
    public function __construct(string $certificate, string $privateKey, ?string $privateKeyPassphrase = null, ?string $extraCerts = null, ?int $signOptions = null)
    {
        if (!\extension_loaded('openssl')) {
            throw new \LogicException('PHP extension "openssl" is required to use SMime.');
        }

        $this->signCertificate = $this->normalizeFilePath($certificate);

        if (null !== $privateKeyPassphrase) {
            $this->signPrivateKey = [$this->normalizeFilePath($privateKey), $privateKeyPassphrase];
        } else {
            $this->signPrivateKey = $this->normalizeFilePath($privateKey);
        }

        $this->signOptions = $signOptions ?? \PKCS7_DETACHED;
        $this->extraCerts = $extraCerts ? realpath($extraCerts) : null;
    }

    public function sign(Message $message): Message
    {
        $bufferFile = tmpfile();
        $outputFile = tmpfile();

        $this->iteratorToFile($message->getBody()->toIterable(), $bufferFile);

        if (!@openssl_pkcs7_sign(stream_get_meta_data($bufferFile)['uri'], stream_get_meta_data($outputFile)['uri'], $this->signCertificate, $this->signPrivateKey, [], $this->signOptions, $this->extraCerts)) {
            throw new RuntimeException(\sprintf('Failed to sign S/Mime message. Error: "%s".', openssl_error_string()));
        }

        return new Message($message->getHeaders(), $this->convertMessageToSMimePart($outputFile, 'multipart', 'signed'));
    }
}
