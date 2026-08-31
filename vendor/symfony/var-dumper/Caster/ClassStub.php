<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * Represents a PHP class identifier.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ClassStub extends ConstStub
{
    /**
     * @param string   $identifier A PHP identifier, e.g. a class, method, interface, etc. name
     * @param callable $callable   The callable targeted by the identifier when it is ambiguous or not a real PHP identifier
     */
    public function __construct(string $identifier, callable|array|string|null $callable = null)
    {
        $this->value = $identifier;

        try {
            if (null !== $callable) {
                if ($callable instanceof \Closure) {
                    $r = new \ReflectionFunction($callable);
                } elseif (\is_object($callable)) {
                    $r = [$callable, '__invoke'];
                } elseif (\is_array($callable)) {
                    $r = $callable;
                } elseif (false !== $i = strpos($callable, '::')) {
                    $r = [substr($callable, 0, $i), substr($callable, 2 + $i)];
                } else {
                    $r = new \ReflectionFunction($callable);
                }
            } elseif (0 < $i = strpos($identifier, '::') ?: strpos($identifier, '->')) {
                $r = [substr($identifier, 0, $i), substr($identifier, 2 + $i)];
            } else {
                $r = new \ReflectionClass($identifier);
            }

            if (\is_array($r)) {
                try {
                    $r = new \ReflectionMethod($r[0], $r[1]);
                } catch (\ReflectionException) {
                    $r = new \ReflectionClass($r[0]);
                }
            }

            if (str_contains($identifier, "@anonymous\0")) {
                $this->value = $identifier = preg_replace_callback('/[a-zA-Z_\x7f-\xff][\\\\a-zA-Z0-9_\x7f-\xff]*+@anonymous\x00.*?\.php(?:0x?|:[0-9]++\$)?[0-9a-fA-F]++/', static fn ($m) => class_exists($m[0], false) ? (get_parent_class($m[0]) ?: key(class_implements($m[0])) ?: 'class').'@anonymous' : $m[0], $identifier);
            }

            if (null !== $callable && $r instanceof \ReflectionFunctionAbstract) {
                $s = ReflectionCaster::castFunctionAbstract($r, [], new Stub(), true, Caster::EXCLUDE_VERBOSE);
                $s = ReflectionCaster::getSignature($s);

                if (str_ends_with($identifier, '()')) {
                    $this->value = substr_replace($identifier, $s, -2);
                } else {
                    $this->value .= $s;
                }
            }
        } catch (\ReflectionException) {
            return;
        } finally {
            if (0 < $i = strrpos($this->value, '\\')) {
                $this->attr['ellipsis'] = \strlen($this->value) - $i;
                $this->attr['ellipsis-type'] = 'class';
                $this->attr['ellipsis-tail'] = 1;
            }
        }

        if ($f = $r->getFileName()) {
            $this->attr['file'] = $f;
            $this->attr['line'] = $r->getStartLine();
        }
    }

    public static function wrapCallable(mixed $callable): mixed
    {
        if (\is_object($callable) || !\is_callable($callable)) {
            return $callable;
        }

        if (!\is_array($callable)) {
            $callable = new static($callable, $callable);
        } elseif (\is_string($callable[0])) {
            $callable[0] = new static($callable[0], $callable);
        } else {
            $callable[1] = new static($callable[1], $callable);
        }

        return $callable;
    }
}
