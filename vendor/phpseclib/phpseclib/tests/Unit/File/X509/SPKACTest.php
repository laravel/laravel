<?php
/**
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2014 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once 'File/X509.php';
require_once 'Crypt/RSA.php';

class Unit_File_X509_SPKACTest extends PhpseclibTestCase
{
    public function testLoadSPKAC()
    {
        $test = 'MIICQDCCASgwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQChgo9mWzQm3TSwGgpZnIc54' .
                'TZ8gYpfAO/AI0etvyWDqnFfdNCUQsqxTdSi6/rtrJdLGBsszRGrRIc/0JqmjM+jCHGYutLeo4xwgr' .
                'a3HAZrWDypL5IlRWnLmLA4U/qGXCXNSk+9NrJl39X3IDA8o/aOJyr9iMUJMvswcWjVjPom3NhAgmJ' .
                'ZwW0vUEMw9zszExpiRnGSO5XXntQW2qvfzo+J3NzS3BBbKxEmTsfOLHextcXeFQUaBQHXB/WOtweW' .
                'Y/Bd4iZ8ETmhal28g1HWVcTFPD+V+KPRFeARlVEW6JmcJucW2WdJlBGKXXXPEfdHrDS3OgD/eDWfM' .
                'JE4mChZ/icxAgMBAAEWADANBgkqhkiG9w0BAQQFAAOCAQEAUMvIKhlSgEgbC081b/FJwh6mbuVgYN' .
                'ZV37Ts2WjrHoDFlabu9WXU8xzgaXct3sO51vJM5I36rY4UPyc6w3y9dLaamEwKUoWnpHG8mlXs2JG' .
                'GEUOvxh5z9yfk/2ZmdCVBlKnU1LDB+ZDyNyNh5B0YULrJKw9e0jV+ymP7srwUSBcdUfZh1KEKGVIN' .
                'Uv4J3GuL8V63E2unWCHGRPw4EmFVTbWpgMx96XR7p/pMavu6/pVKgYQqWLOmEeOK+dmT/QVon28d5' .
                'dmeL7aWrpP+3x3L0A9cATksracQX676XogdAEXJ59fcr/S5AGw1TFErbyBbfyeAWvzDZIXeMXpb9h' .
                'yNtA==';

        $x509 = new File_X509();

        $spkac = $x509->loadSPKAC($test);

        $this->assertInternalType('array', $spkac);

        $spkac = $x509->loadSPKAC('SPKAC=' . $test);

        $this->assertInternalType('array', $spkac);

        $this->assertTrue(
            $x509->validateSignature(),
            'Failed asserting that the signature is valid'
        );

        $pubKey = $x509->getPublicKey();

        $this->assertInternalType('string', "$pubKey");
    }

    public function testSaveSPKAC()
    {
        $privKey = new Crypt_RSA();
        extract($privKey->createKey());
        $privKey->loadKey($privatekey);

        $x509 = new File_X509();
        $x509->setPrivateKey($privKey);
        $x509->setChallenge('...');

        $spkac = $x509->signSPKAC();
        $this->assertInternalType('array', $spkac);

        $this->assertInternalType('string', $x509->saveSPKAC($spkac));

        $x509 = new File_X509();
        $x509->setPrivateKey($privKey);

        $spkac = $x509->signSPKAC();
        $this->assertInternalType('array', $spkac);

        $this->assertInternalType('string', $x509->saveSPKAC($spkac));
    }

    public function testBadSignatureSPKAC()
    {
        $test = 'MIICQDCCASgwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQChgo9mWzQm3TSwGgpZnIc54' .
                'TZ8gYpfAO/AI0etvyWDqnFfdNCUQsqxTdSi6/rtrJdLGBsszRGrRIc/0JqmjM+jCHGYutLeo4xwgr' .
                'a3HAZrWDypL5IlRWnLmLA4U/qGXCXNSk+9NrJl39X3IDA8o/aOJyr9iMUJMvswcWjVjPom3NhAgmJ' .
                'ZwW0vUEMw9zszExpiRnGSO5XXntQW2qvfzo+J3NzS3BBbKxEmTsfOLHextcXeFQUaBQHXB/WOtweW' .
                'Y/Bd4iZ8ETmhal28g1HWVcTFPD+V+KPRFeARlVEW6JmcJucW2WdJlBGKXXXPEfdHrDS3OgD/eDWfM' .
                'JE4mChZ/icxAgMBAAEWADANBgkqhkiG9w0BAQQFAAOCAQEAUMvIKhlSgEgbC081b/FJwh6mbuVgYN' .
                'ZV37Ts2WjrHoDFlabu9WXU8xzgaXct3sO51vJM5I36rY4UPyc6w3y9dLaamEwKUoWnpHG8mlXs2JG' .
                'GEUOvxh5z9yfk/2ZmdCVBlKnU1LDB+ZDyNyNh5B0YULrJKw9e0jV+ymP7srwUSBcdUfZh1KEKGVIN' .
                'Uv4J3GuL8V63E2unWCHGRPw4EmFVTbWpgMx96XR7p/pMavu6/pVKgYQqWLOmEeOK+dmT/QVon28d5' .
                'dmeL7aWrpP+3x3L0A9cATksracQX676XogdAEXJ59fcr/S5AGw1TFErbyBbfyeAWvzDZIXeMXpb9h' .
                'yNtA==';

        $x509 = new File_X509();

        $spkac = $x509->loadSPKAC($test);

        $spkac['publicKeyAndChallenge']['challenge'] = 'zzzz';

        $x509->loadSPKAC($x509->saveSPKAC($spkac));

        $this->assertFalse(
            $x509->validateSignature(),
            'Failed asserting that the signature is invalid'
        );

    }
}
