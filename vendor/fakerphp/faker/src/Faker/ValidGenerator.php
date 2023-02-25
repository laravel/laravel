<?php

namespace Faker;

/**
 * Proxy for other generators, to return only valid values. Works with
 * Faker\Generator\Base->valid()
 *
 * @mixin Generator
 */
class ValidGenerator
{
    protected $generator;
    protected $validator;
    protected $maxRetries;

    /**
     * @param callable|null $validator
     * @param int           $maxRetries
     */
    public function __construct(Generator $generator, $validator = null, $maxRetries = 10000)
    {
        if (null === $validator) {
            $validator = static function () {
                return true;
            };
        } elseif (!is_callable($validator)) {
            throw new \InvalidArgumentException('valid() only accepts callables as first argument');
        }
        $this->generator = $generator;
        $this->validator = $validator;
        $this->maxRetries = $maxRetries;
    }

    /**
     * Catch and proxy all generator calls but return only valid values
     *
     * @param string $attribute
     *
     * @deprecated Use a method instead.
     */
    public function __get($attribute)
    {
        trigger_deprecation('fakerphp/faker', '1.14', 'Accessing property "%s" is deprecated, use "%s()" instead.', $attribute, $attribute);

        return $this->__call($attribute, []);
    }

    /**
     * Catch and proxy all generator calls with arguments but return only valid values
     *
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, $arguments)
    {
        $i = 0;

        do {
            $res = call_user_func_array([$this->generator, $name], $arguments);
            ++$i;

            if ($i > $this->maxRetries) {
                throw new \OverflowException(sprintf('Maximum retries of %d reached without finding a valid value', $this->maxRetries));
            }
        } while (!call_user_func($this->validator, $res));

        return $res;
    }
}
