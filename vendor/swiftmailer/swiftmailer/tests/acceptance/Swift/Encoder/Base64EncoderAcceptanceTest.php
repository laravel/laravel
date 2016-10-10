<?php

class Swift_Encoder_Base64EncoderAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    private $_samplesDir;
    private $_encoder;

    public function setUp()
    {
        $this->_samplesDir = realpath(__DIR__.'/../../../_samples/charsets');
        $this->_encoder = new Swift_Encoder_Base64Encoder();
    }

    public function testEncodingAndDecodingSamples()
    {
        $sampleFp = opendir($this->_samplesDir);
        while (false !== $encodingDir = readdir($sampleFp)) {
            if (substr($encodingDir, 0, 1) == '.') {
                continue;
            }

            $sampleDir = $this->_samplesDir.'/'.$encodingDir;

            if (is_dir($sampleDir)) {
                $fileFp = opendir($sampleDir);
                while (false !== $sampleFile = readdir($fileFp)) {
                    if (substr($sampleFile, 0, 1) == '.') {
                        continue;
                    }

                    $text = file_get_contents($sampleDir.'/'.$sampleFile);
                    $encodedText = $this->_encoder->encodeString($text);

                    $this->assertEquals(
                        base64_decode($encodedText), $text,
                        '%s: Encoded string should decode back to original string for sample '.
                        $sampleDir.'/'.$sampleFile
                        );
                }
                closedir($fileFp);
            }
        }
        closedir($sampleFp);
    }
}
