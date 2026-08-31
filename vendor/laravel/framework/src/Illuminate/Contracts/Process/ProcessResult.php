<?php

namespace Illuminate\Contracts\Process;

interface ProcessResult
{
    /**
     * Get the original command executed by the process.
     *
     * @return string
     */
    public function command();

    /**
     * Determine if the process was successful.
     *
     * @return bool
     */
    public function successful();

    /**
     * Determine if the process failed.
     *
     * @return bool
     */
    public function failed();

    /**
     * Get the exit code of the process.
     *
     * @return int|null
     */
    public function exitCode();

    /**
     * Get the standard output of the process.
     *
     * @return string
     */
    public function output();

    /**
     * Determine if the output contains the given string.
     *
     * @param  string  $output
     * @return bool
     */
    public function seeInOutput(string $output);

    /**
     * Get the error output of the process.
     *
     * @return string
     */
    public function errorOutput();

    /**
     * Determine if the error output contains the given string.
     *
     * @param  string  $output
     * @return bool
     */
    public function seeInErrorOutput(string $output);

    /**
     * Throw an exception if the process failed.
     *
     * @param  callable|null  $callback
     * @return $this
     */
    public function throw(?callable $callback = null);

    /**
     * Throw an exception if the process failed and the given condition is true.
     *
     * @param  bool  $condition
     * @param  callable|null  $callback
     * @return $this
     */
    public function throwIf(bool $condition, ?callable $callback = null);
}
