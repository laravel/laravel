<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

class QuickDefinitionsConfiguration
{
    private const QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION = 'QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION';

    private const QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE = 'QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE';

    /**
     * Defines what a quick definition should produce.
     * Possible options are:
     * - self::QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION: in this case quick
     * definitions define a stub.
     * - self::QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE: in this case quick
     * definitions define a mock with an 'at least once' expectation.
     *
     * @var string
     */
    protected $_quickDefinitionsApplicationMode = self::QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION;

    /**
     * Returns true if quick definitions should setup a stub, returns false when
     * quick definitions should setup a mock with 'at least once' expectation.
     * When parameter $newValue is specified it sets the configuration with the
     * given value.
     */
    public function shouldBeCalledAtLeastOnce(?bool $newValue = null): bool
    {
        if ($newValue !== null) {
            $this->_quickDefinitionsApplicationMode = $newValue
                ? self::QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE
                : self::QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION;
        }

        return $this->_quickDefinitionsApplicationMode === self::QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE;
    }
}
