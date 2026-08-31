<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command\ListCommand;

use Psy\Formatter\SignatureFormatter;
use Psy\Input\FilterOptions;
use Psy\Util\Mirror;
use Psy\VarDumper\Presenter;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Abstract Enumerator class.
 */
abstract class Enumerator
{
    // Output styles
    const IS_PUBLIC = 'public';
    const IS_PROTECTED = 'protected';
    const IS_PRIVATE = 'private';
    const IS_GLOBAL = 'global';
    const IS_CONSTANT = 'const';
    const IS_CLASS = 'class';
    const IS_FUNCTION = 'function';
    const IS_VIRTUAL = 'virtual';

    private FilterOptions $filter;
    private Presenter $presenter;

    /**
     * Enumerator constructor.
     *
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter)
    {
        $this->filter = new FilterOptions();
        $this->presenter = $presenter;
    }

    /**
     * Return a list of categorized things with the given input options and target.
     *
     * @param InputInterface  $input
     * @param \Reflector|null $reflector
     * @param mixed           $target
     *
     * @return array
     */
    public function enumerate(InputInterface $input, ?\Reflector $reflector = null, $target = null): array
    {
        $this->filter->bind($input);

        return $this->listItems($input, $reflector, $target);
    }

    /**
     * Enumerate specific items with the given input options and target.
     *
     * Implementing classes should return an array of arrays:
     *
     *     [
     *         'Constants' => [
     *             'FOO' => [
     *                 'name'  => 'FOO',
     *                 'style' => 'public',
     *                 'value' => '123',
     *             ],
     *         ],
     *     ]
     *
     * @param InputInterface  $input
     * @param \Reflector|null $reflector
     * @param mixed           $target
     *
     * @return array
     */
    abstract protected function listItems(InputInterface $input, ?\Reflector $reflector = null, $target = null): array;

    protected function showItem($name)
    {
        return $this->filter->match($name);
    }

    protected function presentRef($value)
    {
        // Symfony VarDumper 5.4 trips over NAN/INF on PHP 8.5 in PHAR builds,
        // so format non-finite floats directly instead of cloning them.
        if (\is_float($value) && !\is_finite($value)) {
            return OutputFormatter::escape(\sprintf('<float>%s</float>', \var_export($value, true)));
        }

        return $this->presenter->presentRef($value);
    }

    protected function presentSignature($target)
    {
        // This might get weird if the signature is actually for a reflector. Hrm.
        if (!$target instanceof \Reflector) {
            $target = Mirror::get($target);
        }

        return SignatureFormatter::format($target);
    }
}
