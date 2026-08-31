<?php

namespace Illuminate\Console\Contracts;

interface NewLineAware
{
    /**
     * How many trailing newlines were written.
     *
     * @return int
     */
    public function newLinesWritten();

    /**
     * Whether a newline has already been written.
     *
     * @return bool
     *
     * @deprecated use newLinesWritten
     */
    public function newLineWritten();
}
