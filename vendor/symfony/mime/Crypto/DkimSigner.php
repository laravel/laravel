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

use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Exception\RuntimeException;
use Symfony\Component\Mime\Header\UnstructuredHeader;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\Part\AbstractPart;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * RFC 6376 and 8301
 */
final class DkimSigner
{
    public const CANON_SIMPLE = 'simple';
    public const CANON_RELAXED = 'relaxed';

    public const ALGO_SHA256 = 'rsa-sha256';
    public const ALGO_ED25519 = 'ed25519-sha256'; // RFC 8463

    private \OpenSSLAsymmetricKey $key;

    /**
     * @param string $pk         The private key as a string or the path to the file containing the private key, should be prefixed with file:// (in PEM format)
     * @param string $passphrase A passphrase of the private key (if any)
     */
    public function __construct(
        string $pk,
        private string $domainName,
        private string $selector,
        private array $defaultOptions = [],
        string $passphrase = '',
    ) {
        if (!\extension_loaded('openssl')) {
            throw new \LogicException('PHP extension "openssl" is required to use DKIM.');
        }
        $this->key = openssl_pkey_get_private($pk, $passphrase) ?: throw new InvalidArgumentException('Unable to load DKIM private key: '.openssl_error_string());
        $this->defaultOptions += [
            'algorithm' => self::ALGO_SHA256,
            'signature_expiration_delay' => 0,
            'body_max_length' => \PHP_INT_MAX,
            'body_show_length' => false,
            'header_canon' => self::CANON_RELAXED,
            'body_canon' => self::CANON_RELAXED,
            'headers_to_ignore' => [],
        ];
    }

    public function sign(Message $message, array $options = []): Message
    {
        $options += $this->defaultOptions;
        if (!\in_array($options['algorithm'], [self::ALGO_SHA256, self::ALGO_ED25519], true)) {
            throw new InvalidArgumentException(\sprintf('Invalid DKIM signing algorithm "%s".', $options['algorithm']));
        }
        $headersToIgnore['return-path'] = true;
        $headersToIgnore['x-transport'] = true;
        foreach ($options['headers_to_ignore'] as $name) {
            $headersToIgnore[strtolower($name)] = true;
        }
        unset($headersToIgnore['from']);
        $signedHeaderNames = [];
        $headerCanonData = '';
        $headers = $message->getPreparedHeaders();
        foreach ($headers->getNames() as $name) {
            foreach ($headers->all($name) as $header) {
                if (isset($headersToIgnore[strtolower($header->getName())])) {
                    continue;
                }

                if ('' !== $header->getBodyAsString()) {
                    $headerCanonData .= $this->canonicalizeHeader($header->toString(), $options['header_canon']);
                    $signedHeaderNames[] = $header->getName();
                }
            }
        }

        [$bodyHash, $bodyLength] = $this->hashBody($message->getBody(), $options['body_canon'], $options['body_max_length']);

        $params = [
            'v' => '1',
            'q' => 'dns/txt',
            'a' => $options['algorithm'],
            'bh' => base64_encode($bodyHash),
            'd' => $this->domainName,
            'h' => implode(': ', $signedHeaderNames),
            'i' => '@'.$this->domainName,
            's' => $this->selector,
            't' => time(),
            'c' => $options['header_canon'].'/'.$options['body_canon'],
        ];

        if ($options['body_show_length']) {
            $params['l'] = $bodyLength;
        }
        if ($options['signature_expiration_delay']) {
            $params['x'] = $params['t'] + $options['signature_expiration_delay'];
        }
        $value = '';
        foreach ($params as $k => $v) {
            $value .= $k.'='.$v.'; ';
        }
        $value = trim($value);
        $header = new UnstructuredHeader('DKIM-Signature', $value);
        $headerCanonData .= rtrim($this->canonicalizeHeader($header->toString()."\r\n b=", $options['header_canon']));
        if (self::ALGO_SHA256 === $options['algorithm']) {
            if (!openssl_sign($headerCanonData, $signature, $this->key, \OPENSSL_ALGO_SHA256)) {
                throw new RuntimeException('Unable to sign DKIM hash: '.openssl_error_string());
            }
        } else {
            throw new \RuntimeException(\sprintf('The "%s" DKIM signing algorithm is not supported yet.', self::ALGO_ED25519));
        }
        $header->setValue($value.' b='.trim(chunk_split(base64_encode($signature), 73, ' ')));
        $headers->add($header);

        return new Message($headers, $message->getBody());
    }

    private function canonicalizeHeader(string $header, string $headerCanon): string
    {
        if (self::CANON_RELAXED !== $headerCanon) {
            return $header."\r\n";
        }

        $exploded = explode(':', $header, 2);
        $name = strtolower(trim($exploded[0]));
        $value = str_replace("\r\n", '', $exploded[1]);
        $value = trim(preg_replace("/[ \t][ \t]+/", ' ', $value));

        return $name.':'.$value."\r\n";
    }

    private function hashBody(AbstractPart $body, string $bodyCanon, int $maxLength): array
    {
        $hash = hash_init('sha256');
        $relaxed = self::CANON_RELAXED === $bodyCanon;
        $currentLine = '';
        $emptyCounter = 0;
        $isSpaceSequence = false;
        $length = 0;
        foreach ($body->bodyToIterable() as $chunk) {
            $canon = '';
            for ($i = 0, $len = \strlen($chunk); $i < $len; ++$i) {
                switch ($chunk[$i]) {
                    case "\r":
                        break;
                    case "\n":
                        // previous char is always \r
                        if ($relaxed) {
                            $isSpaceSequence = false;
                        }
                        if ('' === $currentLine) {
                            ++$emptyCounter;
                        } else {
                            $currentLine = '';
                            $canon .= "\r\n";
                        }
                        break;
                    case ' ':
                    case "\t":
                        if ($relaxed) {
                            $isSpaceSequence = true;
                            break;
                        }
                        // no break
                    default:
                        if ($emptyCounter > 0) {
                            $canon .= str_repeat("\r\n", $emptyCounter);
                            $emptyCounter = 0;
                        }
                        if ($isSpaceSequence) {
                            $currentLine .= ' ';
                            $canon .= ' ';
                            $isSpaceSequence = false;
                        }
                        $currentLine .= $chunk[$i];
                        $canon .= $chunk[$i];
                }
            }

            if ($length + \strlen($canon) >= $maxLength) {
                $canon = substr($canon, 0, $maxLength - $length);
                $length += \strlen($canon);
                hash_update($hash, $canon);

                break;
            }

            $length += \strlen($canon);
            hash_update($hash, $canon);
        }

        // Add trailing Line return if last line is non empty
        if ('' !== $currentLine) {
            hash_update($hash, "\r\n");
            $length += \strlen("\r\n");
        }

        if (!$relaxed && 0 === $length) {
            hash_update($hash, "\r\n");
            $length = 2;
        }

        return [hash_final($hash, true), $length];
    }
}
