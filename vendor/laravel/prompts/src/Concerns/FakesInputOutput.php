<?php

namespace Laravel\Prompts\Concerns;

use Laravel\Prompts\Output\BufferedConsoleOutput;
use Laravel\Prompts\Terminal;
use PHPUnit\Framework\Assert;
use RuntimeException;

trait FakesInputOutput
{
    /**
     * Fake the terminal and queue key presses to be simulated.
     *
     * @param  array<string>  $keys
     */
    public static function fake(array $keys = []): void
    {
        // Force interactive mode when testing because we will be mocking the terminal.
        static::interactive();

        $mock = \Mockery::mock(Terminal::class);

        $mock->shouldReceive('write')->byDefault();
        $mock->shouldReceive('exit')->byDefault();
        $mock->shouldReceive('setTty')->byDefault();
        $mock->shouldReceive('restoreTty')->byDefault();
        $mock->shouldReceive('cols')->byDefault()->andReturn(80);
        $mock->shouldReceive('lines')->byDefault()->andReturn(24);
        $mock->shouldReceive('initDimensions')->byDefault();
        $mock->shouldReceive('supportsTrueColor')->byDefault()->andReturn(false);

        static::fakeKeyPresses($keys, function (string $key) use ($mock): void {
            $mock->shouldReceive('read')->once()->andReturn($key);
        });

        static::$terminal = $mock;

        self::setOutput(new BufferedConsoleOutput);
    }

    /**
     * Implementation of the looping mechanism for simulating key presses.
     *
     * By ignoring the `$callable` parameter which contains the default logic
     * for simulating fake key presses, we can use a custom implementation
     * to emit key presses instead, allowing us to use different inputs.
     *
     * @param  array<string>  $keys
     * @param  callable(string $key): void  $callable
     */
    public static function fakeKeyPresses(array $keys, callable $callable): void
    {
        foreach ($keys as $key) {
            $callable($key);
        }
    }

    /**
     * Assert that the output contains the given string.
     */
    public static function assertOutputContains(string $string): void
    {
        Assert::assertStringContainsString($string, static::content());
    }

    /**
     * Assert that the output doesn't contain the given string.
     */
    public static function assertOutputDoesntContain(string $string): void
    {
        Assert::assertStringNotContainsString($string, static::content());
    }

    /**
     * Assert that the stripped output contains the given string.
     */
    public static function assertStrippedOutputContains(string $string): void
    {
        Assert::assertStringContainsString($string, static::strippedContent());
    }

    /**
     * Assert that the stripped output doesn't contain the given string.
     */
    public static function assertStrippedOutputDoesntContain(string $string): void
    {
        Assert::assertStringNotContainsString($string, static::strippedContent());
    }

    /**
     * Get the buffered console output.
     */
    public static function content(): string
    {
        if (! static::output() instanceof BufferedConsoleOutput) {
            throw new RuntimeException('Prompt must be faked before accessing content.');
        }

        return static::output()->content();
    }

    /**
     * Get the buffered console output, stripped of escape sequences.
     */
    public static function strippedContent(): string
    {
        return preg_replace("/\e\[[0-9;?]*[A-Za-z]/", '', static::content());
    }
}
