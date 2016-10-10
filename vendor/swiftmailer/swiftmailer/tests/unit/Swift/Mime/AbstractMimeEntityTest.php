<?php

require_once dirname(dirname(dirname(__DIR__))).'/fixtures/MimeEntityFixture.php';

abstract class Swift_Mime_AbstractMimeEntityTest extends \SwiftMailerTestCase
{
    public function testGetHeadersReturnsHeaderSet()
    {
        $headers = $this->_createHeaderSet();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $this->assertSame($headers, $entity->getHeaders());
    }

    public function testContentTypeIsReturnedFromHeader()
    {
        $ctype = $this->_createHeader('Content-Type', 'image/jpeg-test');
        $headers = $this->_createHeaderSet(array('Content-Type' => $ctype));
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $this->assertEquals('image/jpeg-test', $entity->getContentType());
    }

    public function testContentTypeIsSetInHeader()
    {
        $ctype = $this->_createHeader('Content-Type', 'text/plain', array(), false);
        $headers = $this->_createHeaderSet(array('Content-Type' => $ctype));

        $ctype->shouldReceive('setFieldBodyModel')
              ->once()
              ->with('image/jpeg');
        $ctype->shouldReceive('setFieldBodyModel')
              ->zeroOrMoreTimes()
              ->with(\Mockery::not('image/jpeg'));

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setContentType('image/jpeg');
    }

    public function testContentTypeHeaderIsAddedIfNoneSet()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('addParameterizedHeader')
                ->once()
                ->with('Content-Type', 'image/jpeg');
        $headers->shouldReceive('addParameterizedHeader')
                ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setContentType('image/jpeg');
    }

    public function testContentTypeCanBeSetViaSetBody()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('addParameterizedHeader')
                ->once()
                ->with('Content-Type', 'text/html');
        $headers->shouldReceive('addParameterizedHeader')
                ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setBody('<b>foo</b>', 'text/html');
    }

    public function testGetEncoderFromConstructor()
    {
        $encoder = $this->_createEncoder('base64');
        $entity = $this->_createEntity($this->_createHeaderSet(), $encoder,
            $this->_createCache()
            );
        $this->assertSame($encoder, $entity->getEncoder());
    }

    public function testSetAndGetEncoder()
    {
        $encoder = $this->_createEncoder('base64');
        $headers = $this->_createHeaderSet();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setEncoder($encoder);
        $this->assertSame($encoder, $entity->getEncoder());
    }

    public function testSettingEncoderUpdatesTransferEncoding()
    {
        $encoder = $this->_createEncoder('base64');
        $encoding = $this->_createHeader(
            'Content-Transfer-Encoding', '8bit', array(), false
            );
        $headers = $this->_createHeaderSet(array(
            'Content-Transfer-Encoding' => $encoding,
            ));
        $encoding->shouldReceive('setFieldBodyModel')
                 ->once()
                 ->with('base64');
        $encoding->shouldReceive('setFieldBodyModel')
                 ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setEncoder($encoder);
    }

    public function testSettingEncoderAddsEncodingHeaderIfNonePresent()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('addTextHeader')
                ->once()
                ->with('Content-Transfer-Encoding', 'something');
        $headers->shouldReceive('addTextHeader')
                ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setEncoder($this->_createEncoder('something'));
    }

    public function testIdIsReturnedFromHeader()
    {
        /* -- RFC 2045, 7.
        In constructing a high-level user agent, it may be desirable to allow
        one body to make reference to another.  Accordingly, bodies may be
        labelled using the "Content-ID" header field, which is syntactically
        identical to the "Message-ID" header field
        */

        $cid = $this->_createHeader('Content-ID', 'zip@button');
        $headers = $this->_createHeaderSet(array('Content-ID' => $cid));
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $this->assertEquals('zip@button', $entity->getId());
    }

    public function testIdIsSetInHeader()
    {
        $cid = $this->_createHeader('Content-ID', 'zip@button', array(), false);
        $headers = $this->_createHeaderSet(array('Content-ID' => $cid));

        $cid->shouldReceive('setFieldBodyModel')
            ->once()
            ->with('foo@bar');
        $cid->shouldReceive('setFieldBodyModel')
            ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setId('foo@bar');
    }

    public function testIdIsAutoGenerated()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $this->assertRegExp('/^.*?@.*?$/D', $entity->getId());
    }

    public function testGenerateIdCreatesNewId()
    {
        $headers = $this->_createHeaderSet();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $id1 = $entity->generateId();
        $id2 = $entity->generateId();
        $this->assertNotEquals($id1, $id2);
    }

    public function testGenerateIdSetsNewId()
    {
        $headers = $this->_createHeaderSet();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $id = $entity->generateId();
        $this->assertEquals($id, $entity->getId());
    }

    public function testDescriptionIsReadFromHeader()
    {
        /* -- RFC 2045, 8.
        The ability to associate some descriptive information with a given
        body is often desirable.  For example, it may be useful to mark an
        "image" body as "a picture of the Space Shuttle Endeavor."  Such text
        may be placed in the Content-Description header field.  This header
        field is always optional.
        */

        $desc = $this->_createHeader('Content-Description', 'something');
        $headers = $this->_createHeaderSet(array('Content-Description' => $desc));
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $this->assertEquals('something', $entity->getDescription());
    }

    public function testDescriptionIsSetInHeader()
    {
        $desc = $this->_createHeader('Content-Description', '', array(), false);
        $desc->shouldReceive('setFieldBodyModel')->once()->with('whatever');

        $headers = $this->_createHeaderSet(array('Content-Description' => $desc));

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setDescription('whatever');
    }

    public function testDescriptionHeaderIsAddedIfNotPresent()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('addTextHeader')
                ->once()
                ->with('Content-Description', 'whatever');
        $headers->shouldReceive('addTextHeader')
                ->zeroOrMoreTimes();

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setDescription('whatever');
    }

    public function testSetAndGetMaxLineLength()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $entity->setMaxLineLength(60);
        $this->assertEquals(60, $entity->getMaxLineLength());
    }

    public function testEncoderIsUsedForStringGeneration()
    {
        $encoder = $this->_createEncoder('base64', false);
        $encoder->expects($this->once())
                ->method('encodeString')
                ->with('blah');

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $encoder, $this->_createCache()
            );
        $entity->setBody('blah');
        $entity->toString();
    }

    public function testMaxLineLengthIsProvidedWhenEncoding()
    {
        $encoder = $this->_createEncoder('base64', false);
        $encoder->expects($this->once())
                ->method('encodeString')
                ->with('blah', 0, 65);

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $encoder, $this->_createCache()
            );
        $entity->setBody('blah');
        $entity->setMaxLineLength(65);
        $entity->toString();
    }

    public function testHeadersAppearInString()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('toString')
                ->once()
                ->andReturn(
                    "Content-Type: text/plain; charset=utf-8\r\n".
                    "X-MyHeader: foobar\r\n"
                );

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $this->assertEquals(
            "Content-Type: text/plain; charset=utf-8\r\n".
            "X-MyHeader: foobar\r\n",
            $entity->toString()
            );
    }

    public function testSetAndGetBody()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $entity->setBody("blah\r\nblah!");
        $this->assertEquals("blah\r\nblah!", $entity->getBody());
    }

    public function testBodyIsAppended()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('toString')
                ->once()
                ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setBody("blah\r\nblah!");
        $this->assertEquals(
            "Content-Type: text/plain; charset=utf-8\r\n".
            "\r\n".
            "blah\r\nblah!",
            $entity->toString()
            );
    }

    public function testGetBodyReturnsStringFromByteStream()
    {
        $os = $this->_createOutputStream('byte stream string');
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $entity->setBody($os);
        $this->assertEquals('byte stream string', $entity->getBody());
    }

    public function testByteStreamBodyIsAppended()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $os = $this->_createOutputStream('streamed');
        $headers->shouldReceive('toString')
                ->once()
                ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setBody($os);
        $this->assertEquals(
            "Content-Type: text/plain; charset=utf-8\r\n".
            "\r\n".
            'streamed',
            $entity->toString()
            );
    }

    public function testBoundaryCanBeRetrieved()
    {
        /* -- RFC 2046, 5.1.1.
     boundary := 0*69<bchars> bcharsnospace

     bchars := bcharsnospace / " "

     bcharsnospace := DIGIT / ALPHA / "'" / "(" / ")" /
                                            "+" / "_" / "," / "-" / "." /
                                            "/" / ":" / "=" / "?"
        */

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $this->assertRegExp(
            '/^[a-zA-Z0-9\'\(\)\+_\-,\.\/:=\?\ ]{0,69}[a-zA-Z0-9\'\(\)\+_\-,\.\/:=\?]$/D',
            $entity->getBoundary()
            );
    }

    public function testBoundaryNeverChanges()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $firstBoundary = $entity->getBoundary();
        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals($firstBoundary, $entity->getBoundary());
        }
    }

    public function testBoundaryCanBeSet()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $entity->setBoundary('foobar');
        $this->assertEquals('foobar', $entity->getBoundary());
    }

    public function testAddingChildrenGeneratesBoundaryInHeaders()
    {
        $child = $this->_createChild();
        $cType = $this->_createHeader('Content-Type', 'text/plain', array(), false);
        $cType->shouldReceive('setParameter')
              ->once()
              ->with('boundary', \Mockery::any());
        $cType->shouldReceive('setParameter')
              ->zeroOrMoreTimes();

        $entity = $this->_createEntity($this->_createHeaderSet(array(
            'Content-Type' => $cType,
            )),
            $this->_createEncoder(), $this->_createCache()
            );
        $entity->setChildren(array($child));
    }

    public function testChildrenOfLevelAttachmentAndLessCauseMultipartMixed()
    {
        for ($level = Swift_Mime_MimeEntity::LEVEL_MIXED;
            $level > Swift_Mime_MimeEntity::LEVEL_TOP; $level /= 2) {
            $child = $this->_createChild($level);
            $cType = $this->_createHeader(
                'Content-Type', 'text/plain', array(), false
                );
            $cType->shouldReceive('setFieldBodyModel')
                  ->once()
                  ->with('multipart/mixed');
            $cType->shouldReceive('setFieldBodyModel')
                  ->zeroOrMoreTimes();

            $entity = $this->_createEntity($this->_createHeaderSet(array(
                'Content-Type' => $cType, )),
                $this->_createEncoder(), $this->_createCache()
                );
            $entity->setChildren(array($child));
        }
    }

    public function testChildrenOfLevelAlternativeAndLessCauseMultipartAlternative()
    {
        for ($level = Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE;
            $level > Swift_Mime_MimeEntity::LEVEL_MIXED; $level /= 2) {
            $child = $this->_createChild($level);
            $cType = $this->_createHeader(
                'Content-Type', 'text/plain', array(), false
                );
            $cType->shouldReceive('setFieldBodyModel')
                  ->once()
                  ->with('multipart/alternative');
            $cType->shouldReceive('setFieldBodyModel')
                  ->zeroOrMoreTimes();

            $entity = $this->_createEntity($this->_createHeaderSet(array(
                'Content-Type' => $cType, )),
                $this->_createEncoder(), $this->_createCache()
                );
            $entity->setChildren(array($child));
        }
    }

    public function testChildrenOfLevelRelatedAndLessCauseMultipartRelated()
    {
        for ($level = Swift_Mime_MimeEntity::LEVEL_RELATED;
            $level > Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE; $level /= 2) {
            $child = $this->_createChild($level);
            $cType = $this->_createHeader(
                'Content-Type', 'text/plain', array(), false
                );
            $cType->shouldReceive('setFieldBodyModel')
                  ->once()
                  ->with('multipart/related');
            $cType->shouldReceive('setFieldBodyModel')
                  ->zeroOrMoreTimes();

            $entity = $this->_createEntity($this->_createHeaderSet(array(
                'Content-Type' => $cType, )),
                $this->_createEncoder(), $this->_createCache()
                );
            $entity->setChildren(array($child));
        }
    }

    public function testHighestLevelChildDeterminesContentType()
    {
        $combinations = array(
            array('levels' => array(Swift_Mime_MimeEntity::LEVEL_MIXED,
                Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
                Swift_Mime_MimeEntity::LEVEL_RELATED,
                ),
                'type' => 'multipart/mixed',
                ),
            array('levels' => array(Swift_Mime_MimeEntity::LEVEL_MIXED,
                Swift_Mime_MimeEntity::LEVEL_RELATED,
                ),
                'type' => 'multipart/mixed',
                ),
            array('levels' => array(Swift_Mime_MimeEntity::LEVEL_MIXED,
                Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
                ),
                'type' => 'multipart/mixed',
                ),
            array('levels' => array(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
                Swift_Mime_MimeEntity::LEVEL_RELATED,
                ),
                'type' => 'multipart/alternative',
                ),
            );

        foreach ($combinations as $combination) {
            $children = array();
            foreach ($combination['levels'] as $level) {
                $children[] = $this->_createChild($level);
            }

            $cType = $this->_createHeader(
                'Content-Type', 'text/plain', array(), false
                );
            $cType->shouldReceive('setFieldBodyModel')
                  ->once()
                  ->with($combination['type']);

            $headerSet = $this->_createHeaderSet(array('Content-Type' => $cType));
            $headerSet->shouldReceive('newInstance')
                      ->zeroOrMoreTimes()
                      ->andReturnUsing(function () use ($headerSet) {
                          return $headerSet;
                      });
            $entity = $this->_createEntity($headerSet,
                $this->_createEncoder(), $this->_createCache()
                );
            $entity->setChildren($children);
        }
    }

    public function testChildrenAppearNestedInString()
    {
        /* -- RFC 2046, 5.1.1.
     (excerpt too verbose to paste here)
     */

        $headers = $this->_createHeaderSet(array(), false);

        $child1 = new MimeEntityFixture(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/plain\r\n".
            "\r\n".
            'foobar'
            );

        $child2 = new MimeEntityFixture(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/html\r\n".
            "\r\n".
            '<b>foobar</b>'
            );

        $headers->shouldReceive('toString')
              ->zeroOrMoreTimes()
              ->andReturn("Content-Type: multipart/alternative; boundary=\"xxx\"\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setBoundary('xxx');
        $entity->setChildren(array($child1, $child2));

        $this->assertEquals(
            "Content-Type: multipart/alternative; boundary=\"xxx\"\r\n".
            "\r\n".
            "\r\n--xxx\r\n".
            "Content-Type: text/plain\r\n".
            "\r\n".
            "foobar\r\n".
            "\r\n--xxx\r\n".
            "Content-Type: text/html\r\n".
            "\r\n".
            "<b>foobar</b>\r\n".
            "\r\n--xxx--\r\n",
            $entity->toString()
            );
    }

    public function testMixingLevelsIsHierarchical()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $newHeaders = $this->_createHeaderSet(array(), false);

        $part = $this->_createChild(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/plain\r\n".
            "\r\n".
            'foobar'
            );

        $attachment = $this->_createChild(Swift_Mime_MimeEntity::LEVEL_MIXED,
            "Content-Type: application/octet-stream\r\n".
            "\r\n".
            'data'
            );

        $headers->shouldReceive('toString')
              ->zeroOrMoreTimes()
              ->andReturn("Content-Type: multipart/mixed; boundary=\"xxx\"\r\n");
        $headers->shouldReceive('newInstance')
              ->zeroOrMoreTimes()
              ->andReturn($newHeaders);
        $newHeaders->shouldReceive('toString')
              ->zeroOrMoreTimes()
              ->andReturn("Content-Type: multipart/alternative; boundary=\"yyy\"\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setBoundary('xxx');
        $entity->setChildren(array($part, $attachment));

        $this->assertRegExp(
            '~^'.
            "Content-Type: multipart/mixed; boundary=\"xxx\"\r\n".
            "\r\n\r\n--xxx\r\n".
            "Content-Type: multipart/alternative; boundary=\"yyy\"\r\n".
            "\r\n\r\n--(.*?)\r\n".
            "Content-Type: text/plain\r\n".
            "\r\n".
            'foobar'.
            "\r\n\r\n--\\1--\r\n".
            "\r\n\r\n--xxx\r\n".
            "Content-Type: application/octet-stream\r\n".
            "\r\n".
            'data'.
            "\r\n\r\n--xxx--\r\n".
            "\$~",
            $entity->toString()
            );
    }

    public function testSettingEncoderNotifiesChildren()
    {
        $child = $this->_createChild(0, '', false);
        $encoder = $this->_createEncoder('base64');

        $child->shouldReceive('encoderChanged')
              ->once()
              ->with($encoder);

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $entity->setChildren(array($child));
        $entity->setEncoder($encoder);
    }

    public function testReceiptOfEncoderChangeNotifiesChildren()
    {
        $child = $this->_createChild(0, '', false);
        $encoder = $this->_createEncoder('base64');

        $child->shouldReceive('encoderChanged')
              ->once()
              ->with($encoder);

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $entity->setChildren(array($child));
        $entity->encoderChanged($encoder);
    }

    public function testReceiptOfCharsetChangeNotifiesChildren()
    {
        $child = $this->_createChild(0, '', false);
        $child->shouldReceive('charsetChanged')
              ->once()
              ->with('windows-874');

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $entity->setChildren(array($child));
        $entity->charsetChanged('windows-874');
    }

    public function testEntityIsWrittenToByteStream()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $is = $this->_createInputStream(false);
        $is->expects($this->atLeastOnce())
           ->method('write');

        $entity->toByteStream($is);
    }

    public function testEntityHeadersAreComittedToByteStream()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );
        $is = $this->_createInputStream(false);
        $is->expects($this->atLeastOnce())
           ->method('write');
        $is->expects($this->atLeastOnce())
           ->method('commit');

        $entity->toByteStream($is);
    }

    public function testOrderingTextBeforeHtml()
    {
        $htmlChild = new MimeEntityFixture(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/html\r\n".
            "\r\n".
            'HTML PART',
            'text/html'
            );
        $textChild = new MimeEntityFixture(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE,
            "Content-Type: text/plain\r\n".
            "\r\n".
            'TEXT PART',
            'text/plain'
            );
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn("Content-Type: multipart/alternative; boundary=\"xxx\"\r\n");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $this->_createCache()
            );
        $entity->setBoundary('xxx');
        $entity->setChildren(array($htmlChild, $textChild));

        $this->assertEquals(
            "Content-Type: multipart/alternative; boundary=\"xxx\"\r\n".
            "\r\n\r\n--xxx\r\n".
            "Content-Type: text/plain\r\n".
            "\r\n".
            'TEXT PART'.
            "\r\n\r\n--xxx\r\n".
            "Content-Type: text/html\r\n".
            "\r\n".
            'HTML PART'.
            "\r\n\r\n--xxx--\r\n",
            $entity->toString()
            );
    }

    public function testUnsettingChildrenRestoresContentType()
    {
        $cType = $this->_createHeader('Content-Type', 'text/plain', array(), false);
        $child = $this->_createChild(Swift_Mime_MimeEntity::LEVEL_ALTERNATIVE);

        $cType->shouldReceive('setFieldBodyModel')
              ->twice()
              ->with('image/jpeg');
        $cType->shouldReceive('setFieldBodyModel')
              ->once()
              ->with('multipart/alternative');
        $cType->shouldReceive('setFieldBodyModel')
              ->zeroOrMoreTimes()
              ->with(\Mockery::not('multipart/alternative', 'image/jpeg'));

        $entity = $this->_createEntity($this->_createHeaderSet(array(
            'Content-Type' => $cType,
            )),
            $this->_createEncoder(), $this->_createCache()
            );

        $entity->setContentType('image/jpeg');
        $entity->setChildren(array($child));
        $entity->setChildren(array());
    }

    public function testBodyIsReadFromCacheWhenUsingToStringIfPresent()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $cache->shouldReceive('hasKey')
              ->once()
              ->with(\Mockery::any(), 'body')
              ->andReturn(true);
        $cache->shouldReceive('getString')
              ->once()
              ->with(\Mockery::any(), 'body')
              ->andReturn("\r\ncache\r\ncache!");

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
            );

        $entity->setBody("blah\r\nblah!");
        $this->assertEquals(
            "Content-Type: text/plain; charset=utf-8\r\n".
            "\r\n".
            "cache\r\ncache!",
            $entity->toString()
            );
    }

    public function testBodyIsAddedToCacheWhenUsingToString()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $cache->shouldReceive('hasKey')
              ->once()
              ->with(\Mockery::any(), 'body')
              ->andReturn(false);
        $cache->shouldReceive('setString')
              ->once()
              ->with(\Mockery::any(), 'body', "\r\nblah\r\nblah!", Swift_KeyCache::MODE_WRITE);

        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
            );

        $entity->setBody("blah\r\nblah!");
        $entity->toString();
    }

    public function testBodyIsClearedFromCacheIfNewBodySet()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
            );

        $entity->setBody("blah\r\nblah!");
        $entity->toString();

        // We set the expectation at this point because we only care what happens when calling setBody()
        $cache->shouldReceive('clearKey')
              ->once()
              ->with(\Mockery::any(), 'body');

        $entity->setBody("new\r\nnew!");
    }

    public function testBodyIsNotClearedFromCacheIfSameBodySet()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
            );

        $entity->setBody("blah\r\nblah!");
        $entity->toString();

        // We set the expectation at this point because we only care what happens when calling setBody()
        $cache->shouldReceive('clearKey')
              ->never();

        $entity->setBody("blah\r\nblah!");
    }

    public function testBodyIsClearedFromCacheIfNewEncoderSet()
    {
        $headers = $this->_createHeaderSet(array(), false);
        $headers->shouldReceive('toString')
                ->zeroOrMoreTimes()
                ->andReturn("Content-Type: text/plain; charset=utf-8\r\n");

        $cache = $this->_createCache(false);
        $otherEncoder = $this->_createEncoder();
        $entity = $this->_createEntity($headers, $this->_createEncoder(),
            $cache
            );

        $entity->setBody("blah\r\nblah!");
        $entity->toString();

        // We set the expectation at this point because we only care what happens when calling setEncoder()
        $cache->shouldReceive('clearKey')
              ->once()
              ->with(\Mockery::any(), 'body');

        $entity->setEncoder($otherEncoder);
    }

    public function testBodyIsReadFromCacheWhenUsingToByteStreamIfPresent()
    {
        $is = $this->_createInputStream();
        $cache = $this->_createCache(false);
        $cache->shouldReceive('hasKey')
              ->once()
              ->with(\Mockery::any(), 'body')
              ->andReturn(true);
        $cache->shouldReceive('exportToByteStream')
              ->once()
              ->with(\Mockery::any(), 'body', $is);

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $cache
            );
        $entity->setBody('foo');

        $entity->toByteStream($is);
    }

    public function testBodyIsAddedToCacheWhenUsingToByteStream()
    {
        $is = $this->_createInputStream();
        $cache = $this->_createCache(false);
        $cache->shouldReceive('hasKey')
              ->once()
              ->with(\Mockery::any(), 'body')
              ->andReturn(false);
        $cache->shouldReceive('getInputByteStream')
              ->once()
              ->with(\Mockery::any(), 'body');

        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $cache
            );
        $entity->setBody('foo');

        $entity->toByteStream($is);
    }

    public function testFluidInterface()
    {
        $entity = $this->_createEntity($this->_createHeaderSet(),
            $this->_createEncoder(), $this->_createCache()
            );

        $this->assertSame($entity,
            $entity
            ->setContentType('text/plain')
            ->setEncoder($this->_createEncoder())
            ->setId('foo@bar')
            ->setDescription('my description')
            ->setMaxLineLength(998)
            ->setBody('xx')
            ->setBoundary('xyz')
            ->setChildren(array())
            );
    }

    // -- Private helpers

    abstract protected function _createEntity($headers, $encoder, $cache);

    protected function _createChild($level = null, $string = '', $stub = true)
    {
        $child = $this->getMockery('Swift_Mime_MimeEntity')->shouldIgnoreMissing();
        if (isset($level)) {
            $child->shouldReceive('getNestingLevel')
                  ->zeroOrMoreTimes()
                  ->andReturn($level);
        }
        $child->shouldReceive('toString')
              ->zeroOrMoreTimes()
              ->andReturn($string);

        return $child;
    }

    protected function _createEncoder($name = 'quoted-printable', $stub = true)
    {
        $encoder = $this->getMock('Swift_Mime_ContentEncoder');
        $encoder->expects($this->any())
                ->method('getName')
                ->will($this->returnValue($name));
        $encoder->expects($this->any())
                ->method('encodeString')
                ->will($this->returnCallback(function () {
                    $args = func_get_args();

                    return array_shift($args);
                }));

        return $encoder;
    }

    protected function _createCache($stub = true)
    {
        return $this->getMockery('Swift_KeyCache')->shouldIgnoreMissing();
    }

    protected function _createHeaderSet($headers = array(), $stub = true)
    {
        $set = $this->getMockery('Swift_Mime_HeaderSet')->shouldIgnoreMissing();
        $set->shouldReceive('get')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($key) use ($headers) {
                return $headers[$key];
            });
        $set->shouldReceive('has')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($key) use ($headers) {
                return array_key_exists($key, $headers);
            });

        return $set;
    }

    protected function _createHeader($name, $model = null, $params = array(), $stub = true)
    {
        $header = $this->getMockery('Swift_Mime_ParameterizedHeader')->shouldIgnoreMissing();
        $header->shouldReceive('getFieldName')
               ->zeroOrMoreTimes()
               ->andReturn($name);
        $header->shouldReceive('getFieldBodyModel')
               ->zeroOrMoreTimes()
               ->andReturn($model);
        $header->shouldReceive('getParameter')
               ->zeroOrMoreTimes()
               ->andReturnUsing(function ($key) use ($params) {
                   return $params[$key];
               });

        return $header;
    }

    protected function _createOutputStream($data = null, $stub = true)
    {
        $os = $this->getMockery('Swift_OutputByteStream');
        if (isset($data)) {
            $os->shouldReceive('read')
               ->zeroOrMoreTimes()
               ->andReturnUsing(function () use ($data) {
                   static $first = true;
                   if (!$first) {
                       return false;
                   }

                   $first = false;

                   return $data;
               });
            $os->shouldReceive('setReadPointer')
              ->zeroOrMoreTimes();
        }

        return $os;
    }

    protected function _createInputStream($stub = true)
    {
        return $this->getMock('Swift_InputByteStream');
    }
}
