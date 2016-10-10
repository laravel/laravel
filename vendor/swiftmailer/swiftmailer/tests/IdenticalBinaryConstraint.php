<?php

/**
 * A binary safe string comparison.
 *
 * @author Chris Corbyn
 */
class IdenticalBinaryConstraint extends \PHPUnit_Framework_Constraint
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     *
     * @return bool
     */
    public function matches($other)
    {
        $aHex = $this->asHexString($this->value);
        $bHex = $this->asHexString($other);

        return $aHex === $bHex;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return 'indentical binary';
    }

    /**
     * Get the given string of bytes as a stirng of Hexadecimal sequences.
     *
     * @param string $binary
     *
     * @return string
     */
    private function asHexString($binary)
    {
        $hex = '';

        $bytes = unpack('H*', $binary);

        foreach ($bytes as &$byte) {
            $byte = strtoupper($byte);
        }

        return implode('', $bytes);
    }
}
