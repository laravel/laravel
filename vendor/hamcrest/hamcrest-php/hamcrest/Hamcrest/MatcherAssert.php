<?php
namespace Hamcrest;

/*
 Copyright (c) 2009 hamcrest.org
 */

class MatcherAssert
{

    /**
     * Number of assertions performed.
     *
     * @var int
     */
    private static $_count = 0;

    /**
     * Make an assertion and throw {@link Hamcrest\AssertionError} if it fails.
     *
     * The first parameter may optionally be a string identifying the assertion
     * to be included in the failure message.
     *
     * If the third parameter is not a matcher it is passed to
     * {@link Hamcrest\Core\IsEqual#equalTo} to create one.
     *
     * Example:
     * <pre>
     * // With an identifier
     * assertThat("apple flavour", $apple->flavour(), equalTo("tasty"));
     * // Without an identifier
     * assertThat($apple->flavour(), equalTo("tasty"));
     * // Evaluating a boolean expression
     * assertThat("some error", $a > $b);
     * assertThat($a > $b);
     * </pre>
     */
    public static function assertThat(/* $args ... */)
    {
        $args = func_get_args();
        switch (count($args)) {
            case 1:
                self::$_count++;
                if (!$args[0]) {
                    throw new AssertionError();
                }
                break;

            case 2:
                self::$_count++;
                if ($args[1] instanceof Matcher) {
                    self::doAssert('', $args[0], $args[1]);
                } elseif (!$args[1]) {
                    throw new AssertionError($args[0]);
                }
                break;

            case 3:
                self::$_count++;
                self::doAssert(
                    $args[0],
                    $args[1],
                    Util::wrapValueWithIsEqual($args[2])
                );
                break;

            default:
                throw new \InvalidArgumentException('assertThat() requires one to three arguments');
        }
    }

    /**
     * Returns the number of assertions performed.
     *
     * @return int
     */
    public static function getCount()
    {
        return self::$_count;
    }

    /**
     * Resets the number of assertions performed to zero.
     */
    public static function resetCount()
    {
        self::$_count = 0;
    }

    /**
     * Performs the actual assertion logic.
     *
     * If <code>$matcher</code> doesn't match <code>$actual</code>,
     * throws a {@link Hamcrest\AssertionError} with a description
     * of the failure along with the optional <code>$identifier</code>.
     *
     * @param string $identifier added to the message upon failure
     * @param mixed $actual value to compare against <code>$matcher</code>
     * @param \Hamcrest\Matcher $matcher applied to <code>$actual</code>
     * @throws AssertionError
     */
    private static function doAssert($identifier, $actual, Matcher $matcher)
    {
        if (!$matcher->matches($actual)) {
            $description = new StringDescription();
            if (!empty($identifier)) {
                $description->appendText($identifier . PHP_EOL);
            }
            $description->appendText('Expected: ')
                                    ->appendDescriptionOf($matcher)
                                    ->appendText(PHP_EOL . '     but: ');

            $matcher->describeMismatch($actual, $description);

            throw new AssertionError((string) $description);
        }
    }
}
