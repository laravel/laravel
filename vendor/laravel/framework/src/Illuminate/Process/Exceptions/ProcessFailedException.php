<?php

namespace Illuminate\Process\Exceptions;

use Illuminate\Contracts\Process\ProcessResult;
use RuntimeException;

class ProcessFailedException extends RuntimeException
{
    /**
     * The process result instance.
     *
     * @var \Illuminate\Contracts\Process\ProcessResult
     */
    public $result;

    /**
     * Create a new exception instance.
     *
     * @param  \Illuminate\Contracts\Process\ProcessResult  $result
     */
    public function __construct(ProcessResult $result)
    {
        $this->result = $result;

        $error = sprintf('The command "%s" failed.'."\n\nExit Code: %s",
            $result->command(),
            $result->exitCode(),
        );

        if (! empty($result->output())) {
            $error .= sprintf("\n\nOutput:\n================\n%s", $result->output());
        }

        if (! empty($result->errorOutput())) {
            $error .= sprintf("\n\nError Output:\n================\n%s", $result->errorOutput());
        }

        parent::__construct($error, $result->exitCode() ?? 1);
    }
}
