<?php
/**
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2014 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once 'File/X509.php';

class Unit_File_X509_CSRTest extends PhpseclibTestCase
{
    public function testLoadCSR()
    {
        $test = '-----BEGIN CERTIFICATE REQUEST-----
MIIBWzCBxQIBADAeMRwwGgYDVQQKDBNwaHBzZWNsaWIgZGVtbyBjZXJ0MIGdMAsG
CSqGSIb3DQEBAQOBjQAwgYkCgYEAtHDb4zoUyiRYsJ5PZrF/IJKAF9ZoHRpTxMA8
a7iyFdsl/vvZLNPsNnFTXXnGdvsyFDEsF7AubaIXw8UKFPYqQRTzSVsvnNgIoVYj
tTAXlB4oHipr7Kxcn4CXfmR0TYogyLvVZSZJYxh+CAuG4V9XM4HqkeE5gyBOsKGy
5FUU8zMCAwEAAaAAMA0GCSqGSIb3DQEBBQUAA4GBAJjdaA9K9DN5xvSiOlCmmV1E
npzHkI1Trraveu0gtRjT/EzHoqjCBI0ekCZ9+fhrex8Sm6Nsq9IgHYyrqnE+PQko
4Nf2w2U3DWxU26D5E9DlI+bLyOCq4jqATLjHyyAsOZY/2+U73AZ82MJM/mGdh5fQ
v5RwaQHmQEzHofTzF7I+
-----END CERTIFICATE REQUEST-----';

        $x509 = new File_X509();

        $spkac = $x509->loadCSR($test);

        $this->assertInternalType('array', $spkac);
    }
}
