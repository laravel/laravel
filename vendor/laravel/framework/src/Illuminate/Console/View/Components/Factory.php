<?php

namespace Illuminate\Console\View\Components;

use InvalidArgumentException;

/**
 * @method void alert(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method mixed ask(string $question, string $default = null, bool $multiline = false)
 * @method mixed askWithCompletion(string $question, array|callable $choices, string $default = null)
 * @method void bulletList(array $elements, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method mixed choice(string $question, array $choices, $default = null, int $attempts = null, bool $multiple = false)
 * @method bool confirm(string $question, bool $default = false)
 * @method void info(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void success(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void error(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void line(string $style, string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void secret(string $question, bool $fallback = true)
 * @method void task(string $description, ?callable $task = null, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void twoColumnDetail(string $first, ?string $second = null, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void warn(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 */
class Factory
{
    /**
     * The output interface implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    /**
     * Creates a new factory instance.
     *
     * @param  \Illuminate\Console\OutputStyle  $output
     */
    public function __construct($output)
    {
        $this->output = $output;
    }

    /**
     * Dynamically handle calls into the component instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __call($method, $parameters)
    {
        $component = '\Illuminate\Console\View\Components\\'.ucfirst($method);

        throw_unless(class_exists($component), new InvalidArgumentException(sprintf(
            'Console component [%s] not found.', $method
        )));

        return (new $component($this->output))->render(...$parameters);
    }
}
