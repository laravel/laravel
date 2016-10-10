<?php

class Swift_Plugins_ReporterPluginTest extends \SwiftMailerTestCase
{
    public function testReportingPasses()
    {
        $message = $this->_createMessage();
        $evt = $this->_createSendEvent();
        $reporter = $this->_createReporter();

        $message->shouldReceive('getTo')->zeroOrMoreTimes()->andReturn(array('foo@bar.tld' => 'Foo'));
        $evt->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn($message);
        $evt->shouldReceive('getFailedRecipients')->zeroOrMoreTimes()->andReturn(array());
        $reporter->shouldReceive('notify')->once()->with($message, 'foo@bar.tld', Swift_Plugins_Reporter::RESULT_PASS);

        $plugin = new Swift_Plugins_ReporterPlugin($reporter);
        $plugin->sendPerformed($evt);
    }

    public function testReportingFailedTo()
    {
        $message = $this->_createMessage();
        $evt = $this->_createSendEvent();
        $reporter = $this->_createReporter();

        $message->shouldReceive('getTo')->zeroOrMoreTimes()->andReturn(array('foo@bar.tld' => 'Foo', 'zip@button' => 'Zip'));
        $evt->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn($message);
        $evt->shouldReceive('getFailedRecipients')->zeroOrMoreTimes()->andReturn(array('zip@button'));
        $reporter->shouldReceive('notify')->once()->with($message, 'foo@bar.tld', Swift_Plugins_Reporter::RESULT_PASS);
        $reporter->shouldReceive('notify')->once()->with($message, 'zip@button', Swift_Plugins_Reporter::RESULT_FAIL);

        $plugin = new Swift_Plugins_ReporterPlugin($reporter);
        $plugin->sendPerformed($evt);
    }

    public function testReportingFailedCc()
    {
        $message = $this->_createMessage();
        $evt = $this->_createSendEvent();
        $reporter = $this->_createReporter();

        $message->shouldReceive('getTo')->zeroOrMoreTimes()->andReturn(array('foo@bar.tld' => 'Foo'));
        $message->shouldReceive('getCc')->zeroOrMoreTimes()->andReturn(array('zip@button' => 'Zip', 'test@test.com' => 'Test'));
        $evt->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn($message);
        $evt->shouldReceive('getFailedRecipients')->zeroOrMoreTimes()->andReturn(array('zip@button'));
        $reporter->shouldReceive('notify')->once()->with($message, 'foo@bar.tld', Swift_Plugins_Reporter::RESULT_PASS);
        $reporter->shouldReceive('notify')->once()->with($message, 'zip@button', Swift_Plugins_Reporter::RESULT_FAIL);
        $reporter->shouldReceive('notify')->once()->with($message, 'test@test.com', Swift_Plugins_Reporter::RESULT_PASS);

        $plugin = new Swift_Plugins_ReporterPlugin($reporter);
        $plugin->sendPerformed($evt);
    }

    public function testReportingFailedBcc()
    {
        $message = $this->_createMessage();
        $evt = $this->_createSendEvent();
        $reporter = $this->_createReporter();

        $message->shouldReceive('getTo')->zeroOrMoreTimes()->andReturn(array('foo@bar.tld' => 'Foo'));
        $message->shouldReceive('getBcc')->zeroOrMoreTimes()->andReturn(array('zip@button' => 'Zip', 'test@test.com' => 'Test'));
        $evt->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn($message);
        $evt->shouldReceive('getFailedRecipients')->zeroOrMoreTimes()->andReturn(array('zip@button'));
        $reporter->shouldReceive('notify')->once()->with($message, 'foo@bar.tld', Swift_Plugins_Reporter::RESULT_PASS);
        $reporter->shouldReceive('notify')->once()->with($message, 'zip@button', Swift_Plugins_Reporter::RESULT_FAIL);
        $reporter->shouldReceive('notify')->once()->with($message, 'test@test.com', Swift_Plugins_Reporter::RESULT_PASS);

        $plugin = new Swift_Plugins_ReporterPlugin($reporter);
        $plugin->sendPerformed($evt);
    }

    // -- Creation Methods

    private function _createMessage()
    {
        return $this->getMockery('Swift_Mime_Message')->shouldIgnoreMissing();
    }

    private function _createSendEvent()
    {
        return $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
    }

    private function _createReporter()
    {
        return $this->getMockery('Swift_Plugins_Reporter')->shouldIgnoreMissing();
    }
}
