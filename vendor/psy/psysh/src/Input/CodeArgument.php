<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Input;

use Symfony\Component\Console\Input\InputArgument;

/**
 * An input argument for code.
 *
 * A CodeArgument must be the final argument of the command. Once all options
 * and other arguments are used, any remaining input until the end of the string
 * is considered part of a single CodeArgument, regardless of spaces, quoting,
 * escaping, etc.
 *
 * This means commands can support crazy input like
 *
 *     parse function() { return "wheee\n"; }
 *
 * ... without having to put the code in a quoted string and escape everything.
 */
class CodeArgument extends InputArgument
{
    /**
     * Constructor.
     *
     * @param string   $name        The argument name
     * @param int|null $mode        The argument mode: self::REQUIRED or self::OPTIONAL
     * @param string   $description A description text
     * @param mixed    $default     The default value (for self::OPTIONAL mode only)
     *
     * @throws \InvalidArgumentException When argument mode is not valid
     */
    public function __construct(string $name, ?int $mode = null, string $description = '', $default = null)
    {
        if ($mode & InputArgument::IS_ARRAY) {
            throw new \InvalidArgumentException('Argument mode IS_ARRAY is not valid');
        }

        parent::__construct($name, $mode, $description, $default);
    }
}
