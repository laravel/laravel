<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Alexandre Daubois <alex.daubois@gmail.com>
 *
 * @internal
 */
final class OpenSSLCaster
{
    public static function castOpensslX509(\OpenSSLCertificate $h, array $a, Stub $stub, bool $isNested): array
    {
        $stub->cut = -1;
        $info = openssl_x509_parse($h, false);

        $pin = openssl_pkey_get_public($h);
        $pin = openssl_pkey_get_details($pin)['key'];
        $pin = \array_slice(explode("\n", $pin), 1, -2);
        $pin = base64_decode(implode('', $pin));
        $pin = base64_encode(hash('sha256', $pin, true));

        $a += [
            Caster::PREFIX_VIRTUAL.'subject' => new EnumStub(array_intersect_key($info['subject'], ['organizationName' => true, 'commonName' => true])),
            Caster::PREFIX_VIRTUAL.'issuer' => new EnumStub(array_intersect_key($info['issuer'], ['organizationName' => true, 'commonName' => true])),
            Caster::PREFIX_VIRTUAL.'expiry' => new ConstStub(date(\DateTimeInterface::ISO8601, $info['validTo_time_t']), $info['validTo_time_t']),
            Caster::PREFIX_VIRTUAL.'fingerprint' => new EnumStub([
                'md5' => new ConstStub(wordwrap(strtoupper(openssl_x509_fingerprint($h, 'md5')), 2, ':', true)),
                'sha1' => new ConstStub(wordwrap(strtoupper(openssl_x509_fingerprint($h, 'sha1')), 2, ':', true)),
                'sha256' => new ConstStub(wordwrap(strtoupper(openssl_x509_fingerprint($h, 'sha256')), 2, ':', true)),
                'pin-sha256' => new ConstStub($pin),
            ]),
        ];

        return $a;
    }

    public static function castOpensslAsymmetricKey(\OpenSSLAsymmetricKey $key, array $a, Stub $stub, bool $isNested): array
    {
        foreach (openssl_pkey_get_details($key) as $k => $v) {
            $a[Caster::PREFIX_VIRTUAL.$k] = $v;
        }

        unset($a[Caster::PREFIX_VIRTUAL.'rsa']); // binary data

        return $a;
    }

    public static function castOpensslCsr(\OpenSSLCertificateSigningRequest $csr, array $a, Stub $stub, bool $isNested): array
    {
        foreach (openssl_csr_get_subject($csr, false) as $k => $v) {
            $a[Caster::PREFIX_VIRTUAL.$k] = $v;
        }

        return $a;
    }
}
