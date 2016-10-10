<?php

/**
 * A binary safe string comparison.
 * @package Swift
 * @subpackage Tests
 * @author Chris Corbyn
 */
class Swift_Tests_IdenticalBinaryExpectation extends SimpleExpectation
{
    /**
     * The subject to compare with.
     * @var string
     * @access private
     */
    private $_left;

    /**
     * Creates a new IdenticalBinaryExpectation comparing with $left.
     * @param string $left    hand side of comparison
     * @param string $message for expecation
     */
    public function __construct($left, $message = '%s')
    {
        parent::__construct($message);
        $this->_left = $left;
    }

    /**
     * Get the given string of bytes as a stirng of Hexadecimal sequences.
     * @param  string $binary
     * @return string
     */
    public function asHexString($binary)
    {
        $hex = '';

        $bytes = unpack('H*', $binary);

        foreach ($bytes as &$byte) {
            $byte = strtoupper($byte);
        }

        return implode('', $bytes);
    }

    /**
     * Test that the passed subject ($right) is identical to $left.
     * @param  string  $right, subject
     * @return boolean
     */
    public function test($right)
    {
        $aHex = $this->asHexString($this->_left);
        $bHex = $this->asHexString($right);

        return $aHex === $bHex;
    }

    /**
     * Get the message depending upon whether this expectation is satisfied.
     * @param $right subject to compare against
     * @return string
     */
    public function testMessage($right)
    {
        if ($this->test($right)) {
            return 'Identical binary expectation [' . $this->asHexString($right) . ']';
        } else {
            $this->_dumper=new SimpleDumper();

            return 'Identical binary expectation fails ' .
                $this->_dumper->describeDifference(
                    $this->asHexString($this->_left),
                    $this->asHexString($right)
                    );
        }
    }
}
