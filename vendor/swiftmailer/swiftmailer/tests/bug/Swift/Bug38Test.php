<?php

class Swift_Bug38Test extends \PHPUnit_Framework_TestCase
{
    private $_attFile;
    private $_attFileName;
    private $_attFileType;

    public function setUp()
    {
        $this->_attFileName = 'data.txt';
        $this->_attFileType = 'text/plain';
        $this->_attFile = __DIR__.'/../../_samples/files/data.txt';
        Swift_Preferences::getInstance()->setCharset('utf-8');
    }

    public function testWritingMessageToByteStreamProducesCorrectStructure()
    {
        $message = new Swift_Message();
        $message->setSubject('test subject');
        $message->setTo('user@domain.tld');
        $message->setCc('other@domain.tld');
        $message->setFrom('user@domain.tld');

        $image = new Swift_Image('<data>', 'image.gif', 'image/gif');

        $cid = $message->embed($image);
        $message->setBody('HTML part', 'text/html');

        $id = $message->getId();
        $date = preg_quote(date('r', $message->getDate()), '~');
        $boundary = $message->getBoundary();
        $imgId = $image->getId();

        $stream = new Swift_ByteStream_ArrayByteStream();

        $message->toByteStream($stream);

        $this->assertPatternInStream(
            '~^'.
            'Message-ID: <'.$id.'>'."\r\n".
            'Date: '.$date."\r\n".
            'Subject: test subject'."\r\n".
            'From: user@domain.tld'."\r\n".
            'To: user@domain.tld'."\r\n".
            'Cc: other@domain.tld'."\r\n".
            'MIME-Version: 1.0'."\r\n".
            'Content-Type: multipart/related;'."\r\n".
            ' boundary="'.$boundary.'"'."\r\n".
            "\r\n\r\n".
            '--'.$boundary."\r\n".
            'Content-Type: text/html; charset=utf-8'."\r\n".
            'Content-Transfer-Encoding: quoted-printable'."\r\n".
            "\r\n".
            'HTML part'.
            "\r\n\r\n".
            '--'.$boundary."\r\n".
            'Content-Type: image/gif; name=image.gif'."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-Disposition: inline; filename=image.gif'."\r\n".
            'Content-ID: <'.preg_quote($imgId, '~').'>'."\r\n".
            "\r\n".
            preg_quote(base64_encode('<data>'), '~').
            "\r\n\r\n".
            '--'.$boundary.'--'."\r\n".
            '$~D',
            $stream
        );
    }

    public function testWritingMessageToByteStreamTwiceProducesCorrectStructure()
    {
        $message = new Swift_Message();
        $message->setSubject('test subject');
        $message->setTo('user@domain.tld');
        $message->setCc('other@domain.tld');
        $message->setFrom('user@domain.tld');

        $image = new Swift_Image('<data>', 'image.gif', 'image/gif');

        $cid = $message->embed($image);
        $message->setBody('HTML part', 'text/html');

        $id = $message->getId();
        $date = preg_quote(date('r', $message->getDate()), '~');
        $boundary = $message->getBoundary();
        $imgId = $image->getId();

        $pattern = '~^'.
        'Message-ID: <'.$id.'>'."\r\n".
        'Date: '.$date."\r\n".
        'Subject: test subject'."\r\n".
        'From: user@domain.tld'."\r\n".
        'To: user@domain.tld'."\r\n".
        'Cc: other@domain.tld'."\r\n".
        'MIME-Version: 1.0'."\r\n".
        'Content-Type: multipart/related;'."\r\n".
        ' boundary="'.$boundary.'"'."\r\n".
        "\r\n\r\n".
        '--'.$boundary."\r\n".
        'Content-Type: text/html; charset=utf-8'."\r\n".
        'Content-Transfer-Encoding: quoted-printable'."\r\n".
        "\r\n".
        'HTML part'.
        "\r\n\r\n".
        '--'.$boundary."\r\n".
        'Content-Type: image/gif; name=image.gif'."\r\n".
        'Content-Transfer-Encoding: base64'."\r\n".
        'Content-Disposition: inline; filename=image.gif'."\r\n".
        'Content-ID: <'.preg_quote($imgId, '~').'>'."\r\n".
        "\r\n".
        preg_quote(base64_encode('<data>'), '~').
        "\r\n\r\n".
        '--'.$boundary.'--'."\r\n".
        '$~D'
        ;

        $streamA = new Swift_ByteStream_ArrayByteStream();
        $streamB = new Swift_ByteStream_ArrayByteStream();

        $message->toByteStream($streamA);
        $message->toByteStream($streamB);

        $this->assertPatternInStream($pattern, $streamA);
        $this->assertPatternInStream($pattern, $streamB);
    }

    public function testWritingMessageToByteStreamTwiceUsingAFileAttachment()
    {
        $message = new Swift_Message();
        $message->setSubject('test subject');
        $message->setTo('user@domain.tld');
        $message->setCc('other@domain.tld');
        $message->setFrom('user@domain.tld');

        $attachment = Swift_Attachment::fromPath($this->_attFile);

        $message->attach($attachment);

        $message->setBody('HTML part', 'text/html');

        $id = $message->getId();
        $date = preg_quote(date('r', $message->getDate()), '~');
        $boundary = $message->getBoundary();

        $streamA = new Swift_ByteStream_ArrayByteStream();
        $streamB = new Swift_ByteStream_ArrayByteStream();

        $pattern = '~^'.
            'Message-ID: <'.$id.'>'."\r\n".
            'Date: '.$date."\r\n".
            'Subject: test subject'."\r\n".
            'From: user@domain.tld'."\r\n".
            'To: user@domain.tld'."\r\n".
            'Cc: other@domain.tld'."\r\n".
            'MIME-Version: 1.0'."\r\n".
            'Content-Type: multipart/mixed;'."\r\n".
            ' boundary="'.$boundary.'"'."\r\n".
            "\r\n\r\n".
            '--'.$boundary."\r\n".
            'Content-Type: text/html; charset=utf-8'."\r\n".
            'Content-Transfer-Encoding: quoted-printable'."\r\n".
            "\r\n".
            'HTML part'.
            "\r\n\r\n".
            '--'.$boundary."\r\n".
            'Content-Type: '.$this->_attFileType.'; name='.$this->_attFileName."\r\n".
            'Content-Transfer-Encoding: base64'."\r\n".
            'Content-Disposition: attachment; filename='.$this->_attFileName."\r\n".
            "\r\n".
            preg_quote(base64_encode(file_get_contents($this->_attFile)), '~').
            "\r\n\r\n".
            '--'.$boundary.'--'."\r\n".
            '$~D'
            ;

        $message->toByteStream($streamA);
        $message->toByteStream($streamB);

        $this->assertPatternInStream($pattern, $streamA);
        $this->assertPatternInStream($pattern, $streamB);
    }

    // -- Helpers

    public function assertPatternInStream($pattern, $stream, $message = '%s')
    {
        $string = '';
        while (false !== $bytes = $stream->read(8192)) {
            $string .= $bytes;
        }
        $this->assertRegExp($pattern, $string, $message);
    }
}
