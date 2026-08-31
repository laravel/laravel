<?php

namespace staabm\SideEffectsDetector;

/**
 * @api
 */
final class SideEffect {
    /**
     * die, exit, throw.
     */
    const PROCESS_EXIT = 'process_exit';

    /**
     * class definition, func definition, include, require, global var, unset, goto
     */
    const SCOPE_POLLUTION = 'scope_pollution';

    /**
     * fwrite, unlink...
     */
    const INPUT_OUTPUT = 'input_output';

    /**
     * echo, print.
     */
    const STANDARD_OUTPUT = 'standard_output';

    /**
     * code for sure has side-effects, we don't have enough information to classify it.
     */
    const UNKNOWN_CLASS = 'unknown_class';

    /**
     * code might have side-effects, but we can't tell for sure.
     */
    const MAYBE = 'maybe_has_side_effects';

    private function __construct() {
        // nothing todo
    }
}