<?php

class Swift_CharacterReaderFactory_SimpleCharacterReaderFactoryAcceptanceTest extends \PHPUnit_Framework_TestCase
{
    private $_factory;
    private $_prefix = 'Swift_CharacterReader_';

    public function setUp()
    {
        $this->_factory = new Swift_CharacterReaderFactory_SimpleCharacterReaderFactory();
    }

    public function testCreatingUtf8Reader()
    {
        foreach (array('utf8', 'utf-8', 'UTF-8', 'UTF8') as $utf8) {
            $reader = $this->_factory->getReaderFor($utf8);
            $this->assertInstanceof($this->_prefix.'Utf8Reader', $reader);
        }
    }

    public function testCreatingIso8859XReaders()
    {
        $charsets = array();
        foreach (range(1, 16) as $number) {
            foreach (array('iso', 'iec') as $body) {
                $charsets[] = $body.'-8859-'.$number;
                $charsets[] = $body.'8859-'.$number;
                $charsets[] = strtoupper($body).'-8859-'.$number;
                $charsets[] = strtoupper($body).'8859-'.$number;
            }
        }

        foreach ($charsets as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingWindows125XReaders()
    {
        $charsets = array();
        foreach (range(0, 8) as $number) {
            $charsets[] = 'windows-125'.$number;
            $charsets[] = 'windows125'.$number;
            $charsets[] = 'WINDOWS-125'.$number;
            $charsets[] = 'WINDOWS125'.$number;
        }

        foreach ($charsets as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingCodePageReaders()
    {
        $charsets = array();
        foreach (range(0, 8) as $number) {
            $charsets[] = 'cp-125'.$number;
            $charsets[] = 'cp125'.$number;
            $charsets[] = 'CP-125'.$number;
            $charsets[] = 'CP125'.$number;
        }

        foreach (array(437, 737, 850, 855, 857, 858, 860,
            861, 863, 865, 866, 869, ) as $number) {
            $charsets[] = 'cp-'.$number;
            $charsets[] = 'cp'.$number;
            $charsets[] = 'CP-'.$number;
            $charsets[] = 'CP'.$number;
        }

        foreach ($charsets as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingAnsiReader()
    {
        foreach (array('ansi', 'ANSI') as $ansi) {
            $reader = $this->_factory->getReaderFor($ansi);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingMacintoshReader()
    {
        foreach (array('macintosh', 'MACINTOSH') as $mac) {
            $reader = $this->_factory->getReaderFor($mac);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingKOIReaders()
    {
        $charsets = array();
        foreach (array('7', '8-r', '8-u', '8u', '8r') as $end) {
            $charsets[] = 'koi-'.$end;
            $charsets[] = 'koi'.$end;
            $charsets[] = 'KOI-'.$end;
            $charsets[] = 'KOI'.$end;
        }

        foreach ($charsets as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingIsciiReaders()
    {
        foreach (array('iscii', 'ISCII', 'viscii', 'VISCII') as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingMIKReader()
    {
        foreach (array('mik', 'MIK') as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingCorkReader()
    {
        foreach (array('cork', 'CORK', 't1', 'T1') as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(1, $reader->getInitialByteSize());
        }
    }

    public function testCreatingUcs2Reader()
    {
        foreach (array('ucs-2', 'UCS-2', 'ucs2', 'UCS2') as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(2, $reader->getInitialByteSize());
        }
    }

    public function testCreatingUtf16Reader()
    {
        foreach (array('utf-16', 'UTF-16', 'utf16', 'UTF16') as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(2, $reader->getInitialByteSize());
        }
    }

    public function testCreatingUcs4Reader()
    {
        foreach (array('ucs-4', 'UCS-4', 'ucs4', 'UCS4') as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(4, $reader->getInitialByteSize());
        }
    }

    public function testCreatingUtf32Reader()
    {
        foreach (array('utf-32', 'UTF-32', 'utf32', 'UTF32') as $charset) {
            $reader = $this->_factory->getReaderFor($charset);
            $this->assertInstanceof($this->_prefix.'GenericFixedWidthReader', $reader);
            $this->assertEquals(4, $reader->getInitialByteSize());
        }
    }
}
