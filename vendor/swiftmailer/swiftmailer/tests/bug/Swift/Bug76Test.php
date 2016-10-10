<?php

class Swift_Bug76Test extends \PHPUnit_Framework_TestCase
{
    private $_inputFile;
    private $_outputFile;
    private $_encoder;

    public function setUp()
    {
        if (!defined('SWIFT_TMP_DIR') || !is_writable(SWIFT_TMP_DIR)) {
            $this->markTestSkipped(
                'Cannot run test without a writable directory to use ('.
                'define SWIFT_TMP_DIR in tests/config.php if you wish to run this test)'
             );
        }

        $this->_inputFile = SWIFT_TMP_DIR.'/in.bin';
        file_put_contents($this->_inputFile, '');

        $this->_outputFile = SWIFT_TMP_DIR.'/out.bin';
        file_put_contents($this->_outputFile, '');

        $this->_encoder = $this->_createEncoder();
    }

    public function tearDown()
    {
        unlink($this->_inputFile);
        unlink($this->_outputFile);
    }

    public function testBase64EncodedLineLengthNeverExceeds76CharactersEvenIfArgsDo()
    {
        $this->_fillFileWithRandomBytes(1000, $this->_inputFile);

        $os = $this->_createStream($this->_inputFile);
        $is = $this->_createStream($this->_outputFile);

        $this->_encoder->encodeByteStream($os, $is, 0, 80); //Exceeds 76

        $this->assertMaxLineLength(76, $this->_outputFile,
            '%s: Line length should not exceed 76 characters'
        );
    }

    // -- Custom Assertions

    public function assertMaxLineLength($length, $filePath, $message = '%s')
    {
        $lines = file($filePath);
        foreach ($lines as $line) {
            $this->assertTrue((strlen(trim($line)) <= 76), $message);
        }
    }

    // -- Creation Methods

    private function _fillFileWithRandomBytes($byteCount, $file)
    {
        // I was going to use dd with if=/dev/random but this way seems more
        // cross platform even if a hella expensive!!

        file_put_contents($file, '');
        $fp = fopen($file, 'wb');
        for ($i = 0; $i < $byteCount; ++$i) {
            $byteVal = rand(0, 255);
            fwrite($fp, pack('i', $byteVal));
        }
        fclose($fp);
    }

    private function _createEncoder()
    {
        return new Swift_Mime_ContentEncoder_Base64ContentEncoder();
    }

    private function _createStream($file)
    {
        return new Swift_ByteStream_FileByteStream($file, true);
    }
}
