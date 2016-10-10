<?php

class Swift_Plugins_Reporters_HtmlReporterTest extends \PHPUnit_Framework_TestCase
{
    private $_html;
    private $_message;

    public function setUp()
    {
        $this->_html = new Swift_Plugins_Reporters_HtmlReporter();
        $this->_message = $this->getMock('Swift_Mime_Message');
    }

    public function testReportingPass()
    {
        ob_start();
        $this->_html->notify($this->_message, 'foo@bar.tld',
            Swift_Plugins_Reporter::RESULT_PASS
            );
        $html = ob_get_clean();

        $this->assertRegExp('~ok|pass~i', $html, '%s: Reporter should indicate pass');
        $this->assertRegExp('~foo@bar\.tld~', $html, '%s: Reporter should show address');
    }

    public function testReportingFail()
    {
        ob_start();
        $this->_html->notify($this->_message, 'zip@button',
            Swift_Plugins_Reporter::RESULT_FAIL
            );
        $html = ob_get_clean();

        $this->assertRegExp('~fail~i', $html, '%s: Reporter should indicate fail');
        $this->assertRegExp('~zip@button~', $html, '%s: Reporter should show address');
    }

    public function testMultipleReports()
    {
        ob_start();
        $this->_html->notify($this->_message, 'foo@bar.tld',
            Swift_Plugins_Reporter::RESULT_PASS
            );
        $this->_html->notify($this->_message, 'zip@button',
            Swift_Plugins_Reporter::RESULT_FAIL
            );
        $html = ob_get_clean();

        $this->assertRegExp('~ok|pass~i', $html, '%s: Reporter should indicate pass');
        $this->assertRegExp('~foo@bar\.tld~', $html, '%s: Reporter should show address');
        $this->assertRegExp('~fail~i', $html, '%s: Reporter should indicate fail');
        $this->assertRegExp('~zip@button~', $html, '%s: Reporter should show address');
    }
}
