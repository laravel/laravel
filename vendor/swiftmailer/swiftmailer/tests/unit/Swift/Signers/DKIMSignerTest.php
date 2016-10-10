<?php

class Swift_Signers_DKIMSignerTest extends \SwiftMailerTestCase
{
    public function setUp()
    {
        if (version_compare(phpversion(), '5.4', '<') && !defined('OPENSSL_ALGO_SHA256')) {
            $this->markTestSkipped(
                'skipping because of https://bugs.php.net/bug.php?id=61421'
             );
        }
    }

    public function testBasicSigningHeaderManipulation()
    {
        $headers = $this->_createHeaders();
        $messageContent = 'Hello World';
        $signer = new Swift_Signers_DKIMSigner(file_get_contents(dirname(dirname(dirname(__DIR__))).'/_samples/dkim/dkim.test.priv'), 'dummy.nxdomain.be', 'dummySelector');
        /* @var $signer Swift_Signers_HeaderSigner */
        $altered = $signer->getAlteredHeaders();
        $signer->reset();
        // Headers
        $signer->setHeaders($headers);
        // Body
        $signer->startBody();
        $signer->write($messageContent);
        $signer->endBody();
        // Signing
        $signer->addSignature($headers);
    }

    // Default Signing
    public function testSigningDefaults()
    {
        $headerSet = $this->_createHeaderSet();
        $messageContent = 'Hello World';
        $signer = new Swift_Signers_DKIMSigner(file_get_contents(dirname(dirname(dirname(__DIR__))).'/_samples/dkim/dkim.test.priv'), 'dummy.nxdomain.be', 'dummySelector');
        $signer->setSignatureTimestamp('1299879181');
        $altered = $signer->getAlteredHeaders();
        $this->assertEquals(array('DKIM-Signature'), $altered);
        $signer->reset();
        $signer->setHeaders($headerSet);
        $this->assertFalse($headerSet->has('DKIM-Signature'));
        $signer->startBody();
        $signer->write($messageContent);
        $signer->endBody();
        $signer->addSignature($headerSet);
        $this->assertTrue($headerSet->has('DKIM-Signature'));
        $dkim = $headerSet->getAll('DKIM-Signature');
        $sig = reset($dkim);
        $this->assertEquals($sig->getValue(), 'v=1; a=rsa-sha1; bh=wlbYcY9O9OPInGJ4D0E/rGsvMLE=; d=dummy.nxdomain.be; h=; i=@dummy.nxdomain.be; s=dummySelector; t=1299879181; b=RMSNelzM2O5MAAnMjT3G3/VF36S3DGJXoPCXR001F1WDReu0prGphWjuzK/m6V1pwqQL8cCNg Hi74mTx2bvyAvmkjvQtJf1VMUOCc9WHGcm1Yec66I3ZWoNMGSWZ1EKAm2CtTzyG0IFw4ml9DI wSkyAFxlgicckDD6FibhqwX4w=');
    }

    // SHA256 Signing
    public function testSigning256()
    {
        $headerSet = $this->_createHeaderSet();
        $messageContent = 'Hello World';
        $signer = new Swift_Signers_DKIMSigner(file_get_contents(dirname(dirname(dirname(__DIR__))).'/_samples/dkim/dkim.test.priv'), 'dummy.nxdomain.be', 'dummySelector');
        $signer->setHashAlgorithm('rsa-sha256');
        $signer->setSignatureTimestamp('1299879181');
        $altered = $signer->getAlteredHeaders();
        $this->assertEquals(array('DKIM-Signature'), $altered);
        $signer->reset();
        $signer->setHeaders($headerSet);
        $this->assertFalse($headerSet->has('DKIM-Signature'));
        $signer->startBody();
        $signer->write($messageContent);
        $signer->endBody();
        $signer->addSignature($headerSet);
        $this->assertTrue($headerSet->has('DKIM-Signature'));
        $dkim = $headerSet->getAll('DKIM-Signature');
        $sig = reset($dkim);
        $this->assertEquals($sig->getValue(), 'v=1; a=rsa-sha256; bh=f+W+hu8dIhf2VAni89o8lF6WKTXi7nViA4RrMdpD5/U=; d=dummy.nxdomain.be; h=; i=@dummy.nxdomain.be; s=dummySelector; t=1299879181; b=jqPmieHzF5vR9F4mXCAkowuphpO4iJ8IAVuioh1BFZ3VITXZj5jlOFxULJMBiiApm2keJirnh u4mzogj444QkpT3lJg8/TBGAYQPdcvkG3KC0jdyN6QpSgpITBJG2BwWa+keXsv2bkQgLRAzNx qRhP45vpHCKun0Tg9LrwW/KCg=');
    }

    // Relaxed/Relaxed Hash Signing
    public function testSigningRelaxedRelaxed256()
    {
        $headerSet = $this->_createHeaderSet();
        $messageContent = 'Hello World';
        $signer = new Swift_Signers_DKIMSigner(file_get_contents(dirname(dirname(dirname(__DIR__))).'/_samples/dkim/dkim.test.priv'), 'dummy.nxdomain.be', 'dummySelector');
        $signer->setHashAlgorithm('rsa-sha256');
        $signer->setSignatureTimestamp('1299879181');
        $signer->setBodyCanon('relaxed');
        $signer->setHeaderCanon('relaxed');
        $altered = $signer->getAlteredHeaders();
        $this->assertEquals(array('DKIM-Signature'), $altered);
        $signer->reset();
        $signer->setHeaders($headerSet);
        $this->assertFalse($headerSet->has('DKIM-Signature'));
        $signer->startBody();
        $signer->write($messageContent);
        $signer->endBody();
        $signer->addSignature($headerSet);
        $this->assertTrue($headerSet->has('DKIM-Signature'));
        $dkim = $headerSet->getAll('DKIM-Signature');
        $sig = reset($dkim);
        $this->assertEquals($sig->getValue(), 'v=1; a=rsa-sha256; bh=f+W+hu8dIhf2VAni89o8lF6WKTXi7nViA4RrMdpD5/U=; d=dummy.nxdomain.be; h=; i=@dummy.nxdomain.be; s=dummySelector; c=relaxed/relaxed; t=1299879181; b=gzOI+PX6HpZKQFzwwmxzcVJsyirdLXOS+4pgfCpVHQIdqYusKLrhlLeFBTNoz75HrhNvGH6T0 Rt3w5aTqkrWfUuAEYt0Ns14GowLM7JojaFN+pZ4eYnRB3CBBgW6fee4NEMD5WPca3uS09tr1E 10RYh9ILlRtl+84sovhx5id3Y=');
    }

    // Relaxed/Simple Hash Signing
    public function testSigningRelaxedSimple256()
    {
        $headerSet = $this->_createHeaderSet();
        $messageContent = 'Hello World';
        $signer = new Swift_Signers_DKIMSigner(file_get_contents(dirname(dirname(dirname(__DIR__))).'/_samples/dkim/dkim.test.priv'), 'dummy.nxdomain.be', 'dummySelector');
        $signer->setHashAlgorithm('rsa-sha256');
        $signer->setSignatureTimestamp('1299879181');
        $signer->setHeaderCanon('relaxed');
        $altered = $signer->getAlteredHeaders();
        $this->assertEquals(array('DKIM-Signature'), $altered);
        $signer->reset();
        $signer->setHeaders($headerSet);
        $this->assertFalse($headerSet->has('DKIM-Signature'));
        $signer->startBody();
        $signer->write($messageContent);
        $signer->endBody();
        $signer->addSignature($headerSet);
        $this->assertTrue($headerSet->has('DKIM-Signature'));
        $dkim = $headerSet->getAll('DKIM-Signature');
        $sig = reset($dkim);
        $this->assertEquals($sig->getValue(), 'v=1; a=rsa-sha256; bh=f+W+hu8dIhf2VAni89o8lF6WKTXi7nViA4RrMdpD5/U=; d=dummy.nxdomain.be; h=; i=@dummy.nxdomain.be; s=dummySelector; c=relaxed; t=1299879181; b=dLPJNec5v81oelyzGOY0qPqTlGnQeNfUNBOrV/JKbStr3NqWGI9jH4JAe2YvO2V32lfPNoby1 4MMzZ6EPkaZkZDDSPa+53YbCPQAlqiD9QZZIUe2UNM33HN8yAMgiWEF5aP7MbQnxeVZMfVLEl 9S8qOImu+K5JZqhQQTL0dgLwA=');
    }

    // Simple/Relaxed Hash Signing
    public function testSigningSimpleRelaxed256()
    {
        $headerSet = $this->_createHeaderSet();
        $messageContent = 'Hello World';
        $signer = new Swift_Signers_DKIMSigner(file_get_contents(dirname(dirname(dirname(__DIR__))).'/_samples/dkim/dkim.test.priv'), 'dummy.nxdomain.be', 'dummySelector');
        $signer->setHashAlgorithm('rsa-sha256');
        $signer->setSignatureTimestamp('1299879181');
        $signer->setBodyCanon('relaxed');
        $altered = $signer->getAlteredHeaders();
        $this->assertEquals(array('DKIM-Signature'), $altered);
        $signer->reset();
        $signer->setHeaders($headerSet);
        $this->assertFalse($headerSet->has('DKIM-Signature'));
        $signer->startBody();
        $signer->write($messageContent);
        $signer->endBody();
        $signer->addSignature($headerSet);
        $this->assertTrue($headerSet->has('DKIM-Signature'));
        $dkim = $headerSet->getAll('DKIM-Signature');
        $sig = reset($dkim);
        $this->assertEquals($sig->getValue(), 'v=1; a=rsa-sha256; bh=f+W+hu8dIhf2VAni89o8lF6WKTXi7nViA4RrMdpD5/U=; d=dummy.nxdomain.be; h=; i=@dummy.nxdomain.be; s=dummySelector; c=simple/relaxed; t=1299879181; b=M5eomH/zamyzix9kOes+6YLzQZxuJdBP4x3nP9zF2N26eMLG2/cBKbnNyqiOTDhJdYfWPbLIa 1CWnjST0j5p4CpeOkGYuiE+M4TWEZwhRmRWootlPO3Ii6XpbBJKFk1o9zviS7OmXblUUE4aqb yRSIMDhtLdCK5GlaCneFLN7RQ=');
    }

    // -- Creation Methods
    private function _createHeaderSet()
    {
        $cache = new Swift_KeyCache_ArrayKeyCache(new Swift_KeyCache_SimpleKeyCacheInputStream());
        $factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
        $contentEncoder = new Swift_Mime_ContentEncoder_Base64ContentEncoder();

        $headerEncoder = new Swift_Mime_HeaderEncoder_QpHeaderEncoder(new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8'));
        $paramEncoder = new Swift_Encoder_Rfc2231Encoder(new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8'));
        $grammar = new Swift_Mime_Grammar();
        $headers = new Swift_Mime_SimpleHeaderSet(new Swift_Mime_SimpleHeaderFactory($headerEncoder, $paramEncoder, $grammar));

        return $headers;
    }

    /**
     * @return Swift_Mime_Headers
     */
    private function _createHeaders()
    {
        $x = 0;
        $cache = new Swift_KeyCache_ArrayKeyCache(new Swift_KeyCache_SimpleKeyCacheInputStream());
        $factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
        $contentEncoder = new Swift_Mime_ContentEncoder_Base64ContentEncoder();

        $headerEncoder = new Swift_Mime_HeaderEncoder_QpHeaderEncoder(new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8'));
        $paramEncoder = new Swift_Encoder_Rfc2231Encoder(new Swift_CharacterStream_ArrayCharacterStream($factory, 'utf-8'));
        $grammar = new Swift_Mime_Grammar();
        $headerFactory = new Swift_Mime_SimpleHeaderFactory($headerEncoder, $paramEncoder, $grammar);
        $headers = $this->getMockery('Swift_Mime_HeaderSet');

        $headers->shouldReceive('listAll')
                ->zeroOrMoreTimes()
                ->andReturn(array('From', 'To', 'Date', 'Subject'));
        $headers->shouldReceive('has')
                ->zeroOrMoreTimes()
                ->with('From')
                ->andReturn(true);
        $headers->shouldReceive('getAll')
                ->zeroOrMoreTimes()
                ->with('From')
                ->andReturn(array($headerFactory->createMailboxHeader('From', 'test@test.test')));
        $headers->shouldReceive('has')
                ->zeroOrMoreTimes()
                ->with('To')
                ->andReturn(true);
        $headers->shouldReceive('getAll')
                ->zeroOrMoreTimes()
                ->with('To')
                ->andReturn(array($headerFactory->createMailboxHeader('To', 'test@test.test')));
        $headers->shouldReceive('has')
                ->zeroOrMoreTimes()
                ->with('Date')
                ->andReturn(true);
        $headers->shouldReceive('getAll')
                ->zeroOrMoreTimes()
                ->with('Date')
                ->andReturn(array($headerFactory->createTextHeader('Date', 'Fri, 11 Mar 2011 20:56:12 +0000 (GMT)')));
        $headers->shouldReceive('has')
                ->zeroOrMoreTimes()
                ->with('Subject')
                ->andReturn(true);
        $headers->shouldReceive('getAll')
                ->zeroOrMoreTimes()
                ->with('Subject')
                ->andReturn(array($headerFactory->createTextHeader('Subject', 'Foo Bar Text Message')));
        $headers->shouldReceive('addTextHeader')
                ->zeroOrMoreTimes()
                ->with('DKIM-Signature', \Mockery::any())
                ->andReturn(true);
        $headers->shouldReceive('getAll')
                ->zeroOrMoreTimes()
                ->with('DKIM-Signature')
                ->andReturn(array($headerFactory->createTextHeader('DKIM-Signature', 'Foo Bar Text Message')));

        return $headers;
    }
}
