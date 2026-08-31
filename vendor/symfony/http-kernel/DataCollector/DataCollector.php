<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\VarDumper\Caster\CutStub;
use Symfony\Component\VarDumper\Caster\ReflectionCaster;
use Symfony\Component\VarDumper\Cloner\ClonerInterface;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarDumper\Cloner\VarCloner;

/**
 * DataCollector.
 *
 * Children of this class must store the collected data in the data property.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bernhard Schussek <bschussek@symfony.com>
 */
abstract class DataCollector implements DataCollectorInterface
{
    protected array|Data $data = [];

    private ClonerInterface $cloner;

    /**
     * Converts the variable into a serializable Data instance.
     *
     * This array can be displayed in the template using
     * the VarDumper component.
     */
    protected function cloneVar(mixed $var): Data
    {
        if ($var instanceof Data) {
            return $var;
        }
        if (!isset($this->cloner)) {
            $this->cloner = new VarCloner();
            $this->cloner->setMaxItems(-1);
            $this->cloner->addCasters($this->getCasters());
        }

        return $this->cloner->cloneVar($var);
    }

    /**
     * @return callable[] The casters to add to the cloner
     */
    protected function getCasters(): array
    {
        return [
            '*' => static function ($v, array $a, Stub $s, $isNested) {
                if (!$v instanceof Stub) {
                    $b = $a;
                    foreach ($a as $k => $v) {
                        if (!\is_object($v) || $v instanceof \DateTimeInterface || $v instanceof Stub) {
                            continue;
                        }

                        try {
                            $a[$k] = $s = new CutStub($v);

                            if ($b[$k] === $s) {
                                // we've hit a non-typed reference
                                $a[$k] = $v;
                            }
                        } catch (\TypeError $e) {
                            // we've hit a typed reference
                        }
                    }
                }

                return $a;
            },
        ] + ReflectionCaster::UNSET_CLOSURE_FILE_INFO;
    }

    public function __serialize(): array
    {
        if (self::class === (new \ReflectionMethod($this, '__sleep'))->class || self::class !== (new \ReflectionMethod($this, '__serialize'))->class) {
            return ['data' => $this->data];
        }

        trigger_deprecation('symfony/http-kernel', '7.4', 'Implementing "%s::__sleep()" is deprecated, use "__serialize()" instead.', get_debug_type($this));

        $data = [];
        foreach ($this->__sleep() as $key) {
            try {
                if (($r = new \ReflectionProperty($this, $key))->isInitialized($this)) {
                    $data[$key] = $r->getValue($this);
                }
            } catch (\ReflectionException) {
                $data[$key] = $this->$key;
            }
        }

        return $data;
    }

    public function __unserialize(array $data): void
    {
        if ($wakeup = self::class !== (new \ReflectionMethod($this, '__wakeup'))->class && self::class === (new \ReflectionMethod($this, '__unserialize'))->class) {
            trigger_deprecation('symfony/http-kernel', '7.4', 'Implementing "%s::__wakeup()" is deprecated, use "__unserialize()" instead.', get_debug_type($this));
        }

        if (\in_array(array_keys($data), [['data'], ["\0*\0data"]], true)) {
            $this->data = $data['data'] ?? $data["\0*\0data"];

            if ($wakeup) {
                $this->__wakeup();
            }

            return;
        }

        trigger_deprecation('symfony/http-kernel', '7.4', 'Passing more than just key "data" to "%s::__unserialize()" is deprecated, populate properties in "%s::__unserialize()" instead.', self::class, get_debug_type($this));

        \Closure::bind(function ($data) use ($wakeup) {
            foreach ($data as $key => $value) {
                $this->{("\0" === $key[0] ?? '') ? substr($key, 1 + strrpos($key, "\0")) : $key} = $value;
            }

            if ($wakeup) {
                $this->__wakeup();
            }
        }, $this, static::class)($data);
    }

    /**
     * @deprecated since Symfony 7.4, will be replaced by `__serialize()` in 8.0
     */
    public function __sleep(): array
    {
        trigger_deprecation('symfony/http-kernel', '7.4', 'Calling "%s::__sleep()" is deprecated, use "__serialize()" instead.', get_debug_type($this));

        return ['data'];
    }

    /**
     * @deprecated since Symfony 7.4, will be replaced by `__unserialize()` in 8.0
     */
    public function __wakeup(): void
    {
        trigger_deprecation('symfony/http-kernel', '7.4', 'Calling "%s::__wakeup()" is deprecated, use "__unserialize()" instead.', get_debug_type($this));
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->data = [];
    }
}
