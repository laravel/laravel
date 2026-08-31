<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\TabCompletion\Matcher;

use Psy\Context;
use Psy\ContextAware;

/**
 * An abstract tab completion Matcher which implements ContextAware.
 *
 * The AutoCompleter service will inject a Context instance into all
 * ContextAware Matchers.
 *
 * @author Marc Garcia <markcial@gmail.com>
 */
abstract class AbstractContextAwareMatcher extends AbstractMatcher implements ContextAware
{
    /**
     * Context instance (for ContextAware interface).
     *
     * @var Context
     */
    protected $context;

    /**
     * ContextAware interface.
     *
     * @param Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Get a Context variable by name.
     *
     * @param string $var Variable name
     *
     * @return mixed
     */
    protected function getVariable(string $var)
    {
        return $this->context->get($var);
    }

    /**
     * Get all variables in the current Context.
     *
     * @return array
     */
    protected function getVariables(): array
    {
        return $this->context->getAll();
    }
}
