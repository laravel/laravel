<?php
/**
 * Whoops - php errors for cool kids
 */

namespace Whoops\Handler;
use Whoops\TestCase;
use Whoops\Handler\XmlResponseHandler;
use RuntimeException;

class XmlResponseHandlerTest extends TestCase
{
    public function testSimpleValid()
    {
        $handler = new XmlResponseHandler;

        $run = $this->getRunInstance();
        $run->pushHandler($handler);
        $run->register();

        ob_start();
        $run->handleException($this->getException());
        $data = ob_get_clean();

        $this->assertTrue($this->isValidXml($data));

        return simplexml_load_string($data);
    }

    /**
     * @depends testSimpleValid
     */
    public function testSimpleValidFile(\SimpleXMLElement $xml)
    {
        $this->checkField($xml, 'file', $this->getException()->getFile());
    }

    /**
     * @depends testSimpleValid
     */
    public function testSimpleValidLine(\SimpleXMLElement $xml)
    {
        $this->checkField($xml, 'line', (string) $this->getException()->getLine());
    }

    /**
     * @depends testSimpleValid
     */
    public function testSimpleValidType(\SimpleXMLElement $xml)
    {
        $this->checkField($xml, 'type', get_class($this->getException()));
    }


    /**
     * Helper for testSimpleValid*
     */
    private function checkField(\SimpleXMLElement $xml, $field, $value)
    {
        $list = $xml->xpath('/root/error/'.$field);
        $this->assertArrayHasKey(0, $list);
        $this->assertSame($value, (string) $list[0]);
    }

    private function getException()
    {
        return new RuntimeException;
    }

    /**
     * See if passed string is a valid XML document
     * @param string $data
     * @return boolean
     */
    private function isValidXml($data)
    {
        $prev = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($data);
        libxml_use_internal_errors($prev);
        return $xml !== false;
    }
}
