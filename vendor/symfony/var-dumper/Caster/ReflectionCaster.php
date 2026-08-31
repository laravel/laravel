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
 * Casts Reflector related classes to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 *
 * @internal since Symfony 7.3
 */
class ReflectionCaster
{
    public const UNSET_CLOSURE_FILE_INFO = ['Closure' => __CLASS__.'::unsetClosureFileInfo'];

    private const EXTRA_MAP = [
        'docComment' => 'getDocComment',
        'extension' => 'getExtensionName',
        'isDisabled' => 'isDisabled',
        'isDeprecated' => 'isDeprecated',
        'isInternal' => 'isInternal',
        'isUserDefined' => 'isUserDefined',
        'isGenerator' => 'isGenerator',
        'isVariadic' => 'isVariadic',
    ];

    public static function castClosure(\Closure $c, array $a, Stub $stub, bool $isNested, int $filter = 0): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;
        $c = new \ReflectionFunction($c);

        $a = static::castFunctionAbstract($c, $a, $stub, $isNested, $filter);

        if (!$c->isAnonymous()) {
            $stub->class = isset($a[$prefix.'class']) ? $a[$prefix.'class']->value.'::'.$c->name : $c->name;
            unset($a[$prefix.'class']);
        }
        unset($a[$prefix.'extra']);

        $stub->class .= self::getSignature($a);

        if ($f = $c->getFileName()) {
            $stub->attr['file'] = $f;
            $stub->attr['line'] = $c->getStartLine();
        }

        unset($a[$prefix.'parameters']);

        if ($filter & Caster::EXCLUDE_VERBOSE) {
            $stub->cut += ($c->getFileName() ? 2 : 0) + \count($a);

            return [];
        }

        if ($f) {
            $a[$prefix.'file'] = new LinkStub($f, $c->getStartLine());
            $a[$prefix.'line'] = $c->getStartLine().' to '.$c->getEndLine();
        }

        return $a;
    }

    public static function unsetClosureFileInfo(\Closure $c, array $a): array
    {
        unset($a[Caster::PREFIX_VIRTUAL.'file'], $a[Caster::PREFIX_VIRTUAL.'line']);

        return $a;
    }

    public static function castGenerator(\Generator $c, array $a, Stub $stub, bool $isNested): array
    {
        // Cannot create ReflectionGenerator based on a terminated Generator
        try {
            $reflectionGenerator = new \ReflectionGenerator($c);

            return self::castReflectionGenerator($reflectionGenerator, $a, $stub, $isNested);
        } catch (\Exception) {
            $a[Caster::PREFIX_VIRTUAL.'closed'] = true;

            return $a;
        }
    }

    public static function castType(\ReflectionType $c, array $a, Stub $stub, bool $isNested): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;

        if ($c instanceof \ReflectionNamedType) {
            $a += [
                $prefix.'name' => $c->getName(),
                $prefix.'allowsNull' => $c->allowsNull(),
                $prefix.'isBuiltin' => $c->isBuiltin(),
            ];
        } elseif ($c instanceof \ReflectionUnionType || $c instanceof \ReflectionIntersectionType) {
            $a[$prefix.'allowsNull'] = $c->allowsNull();
            self::addMap($a, $c, [
                'types' => 'getTypes',
            ]);
        } else {
            $a[$prefix.'allowsNull'] = $c->allowsNull();
        }

        return $a;
    }

    public static function castAttribute(\ReflectionAttribute $c, array $a, Stub $stub, bool $isNested): array
    {
        $map = [
            'name' => 'getName',
            'arguments' => 'getArguments',
        ];

        if (\PHP_VERSION_ID >= 80400) {
            unset($map['name']);
        }

        self::addMap($a, $c, $map);

        return $a;
    }

    public static function castReflectionGenerator(\ReflectionGenerator $c, array $a, Stub $stub, bool $isNested): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;

        if ($c->getThis()) {
            $a[$prefix.'this'] = new CutStub($c->getThis());
        }
        $function = $c->getFunction();
        $frame = [
            'class' => $function->class ?? null,
            'type' => isset($function->class) ? ($function->isStatic() ? '::' : '->') : null,
            'function' => $function->name,
            'file' => $c->getExecutingFile(),
            'line' => $c->getExecutingLine(),
        ];
        if ($trace = $c->getTrace(\DEBUG_BACKTRACE_IGNORE_ARGS)) {
            $function = new \ReflectionGenerator($c->getExecutingGenerator());
            array_unshift($trace, [
                'function' => 'yield',
                'file' => $function->getExecutingFile(),
                'line' => $function->getExecutingLine(),
            ]);
            $trace[] = $frame;
            $a[$prefix.'trace'] = new TraceStub($trace, false, 0, -1, -1);
        } else {
            $function = new FrameStub($frame, false, true);
            $function = ExceptionCaster::castFrameStub($function, [], $function, true);
            $a[$prefix.'executing'] = $function[$prefix.'src'];
        }

        $a[Caster::PREFIX_VIRTUAL.'closed'] = false;

        return $a;
    }

    public static function castClass(\ReflectionClass $c, array $a, Stub $stub, bool $isNested, int $filter = 0): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;

        if ($n = \Reflection::getModifierNames($c->getModifiers())) {
            $a[$prefix.'modifiers'] = implode(' ', $n);
        }

        self::addMap($a, $c, [
            'extends' => 'getParentClass',
            'implements' => 'getInterfaceNames',
            'constants' => 'getReflectionConstants',
        ]);

        foreach ($c->getProperties() as $n) {
            $a[$prefix.'properties'][$n->name] = $n;
        }

        foreach ($c->getMethods() as $n) {
            $a[$prefix.'methods'][$n->name] = $n;
        }

        self::addAttributes($a, $c, $prefix);

        if (!($filter & Caster::EXCLUDE_VERBOSE) && !$isNested) {
            self::addExtra($a, $c);
        }

        return $a;
    }

    public static function castFunctionAbstract(\ReflectionFunctionAbstract $c, array $a, Stub $stub, bool $isNested, int $filter = 0): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;

        self::addMap($a, $c, [
            'returnsReference' => 'returnsReference',
            'returnType' => 'getReturnType',
            'class' => 'getClosureCalledClass',
            'this' => 'getClosureThis',
        ]);

        if (isset($a[$prefix.'returnType'])) {
            $v = $a[$prefix.'returnType'];
            $v = $v instanceof \ReflectionNamedType ? $v->getName() : (string) $v;
            $a[$prefix.'returnType'] = new ClassStub($a[$prefix.'returnType'] instanceof \ReflectionNamedType && $a[$prefix.'returnType']->allowsNull() && !\in_array($v, ['mixed', 'null'], true) ? '?'.$v : $v, [class_exists($v, false) || interface_exists($v, false) || trait_exists($v, false) ? $v : '', '']);
        }
        if (isset($a[$prefix.'class'])) {
            $a[$prefix.'class'] = new ClassStub($a[$prefix.'class']);
        }
        if (isset($a[$prefix.'this'])) {
            $a[$prefix.'this'] = new CutStub($a[$prefix.'this']);
        }

        foreach ($c->getParameters() as $v) {
            $k = '$'.$v->name;
            if ($v->isVariadic()) {
                $k = '...'.$k;
            }
            if ($v->isPassedByReference()) {
                $k = '&'.$k;
            }
            $a[$prefix.'parameters'][$k] = $v;
        }
        if (isset($a[$prefix.'parameters'])) {
            $a[$prefix.'parameters'] = new EnumStub($a[$prefix.'parameters']);
        }

        self::addAttributes($a, $c, $prefix);

        if (!($filter & Caster::EXCLUDE_VERBOSE) && $v = $c->getStaticVariables()) {
            foreach ($v as $k => &$v) {
                if (\is_object($v)) {
                    $a[$prefix.'use']['$'.$k] = new CutStub($v);
                } else {
                    $a[$prefix.'use']['$'.$k] = &$v;
                }
            }
            unset($v);
            $a[$prefix.'use'] = new EnumStub($a[$prefix.'use']);
        }

        if (!($filter & Caster::EXCLUDE_VERBOSE) && !$isNested) {
            self::addExtra($a, $c);
        }

        return $a;
    }

    public static function castClassConstant(\ReflectionClassConstant $c, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'modifiers'] = implode(' ', \Reflection::getModifierNames($c->getModifiers()));
        $a[Caster::PREFIX_VIRTUAL.'value'] = $c->getValue();

        self::addAttributes($a, $c);

        return $a;
    }

    public static function castMethod(\ReflectionMethod $c, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'modifiers'] = implode(' ', \Reflection::getModifierNames($c->getModifiers()));

        return $a;
    }

    public static function castParameter(\ReflectionParameter $c, array $a, Stub $stub, bool $isNested): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;

        self::addMap($a, $c, [
            'position' => 'getPosition',
            'isVariadic' => 'isVariadic',
            'byReference' => 'isPassedByReference',
            'allowsNull' => 'allowsNull',
        ]);

        self::addAttributes($a, $c, $prefix);

        if ($v = $c->getType()) {
            $a[$prefix.'typeHint'] = $v instanceof \ReflectionNamedType ? $v->getName() : (string) $v;
        }

        if (isset($a[$prefix.'typeHint'])) {
            $v = $a[$prefix.'typeHint'];
            $a[$prefix.'typeHint'] = new ClassStub($v, [class_exists($v, false) || interface_exists($v, false) || trait_exists($v, false) ? $v : '', '']);
        } else {
            unset($a[$prefix.'allowsNull']);
        }

        if ($c->isOptional()) {
            try {
                $a[$prefix.'default'] = $v = $c->getDefaultValue();
                if ($c->isDefaultValueConstant() && !\is_object($v)) {
                    $a[$prefix.'default'] = new ConstStub($c->getDefaultValueConstantName(), $v);
                }
                if (null === $v) {
                    unset($a[$prefix.'allowsNull']);
                }
            } catch (\ReflectionException) {
            }
        }

        return $a;
    }

    public static function castProperty(\ReflectionProperty $c, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'modifiers'] = implode(' ', \Reflection::getModifierNames($c->getModifiers()));

        self::addAttributes($a, $c);
        self::addExtra($a, $c);

        return $a;
    }

    public static function castReference(\ReflectionReference $c, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'id'] = $c->getId();

        return $a;
    }

    public static function castExtension(\ReflectionExtension $c, array $a, Stub $stub, bool $isNested): array
    {
        self::addMap($a, $c, [
            'version' => 'getVersion',
            'dependencies' => 'getDependencies',
            'iniEntries' => 'getIniEntries',
            'isPersistent' => 'isPersistent',
            'isTemporary' => 'isTemporary',
            'constants' => 'getConstants',
            'functions' => 'getFunctions',
            'classes' => 'getClasses',
        ]);

        return $a;
    }

    public static function castZendExtension(\ReflectionZendExtension $c, array $a, Stub $stub, bool $isNested): array
    {
        self::addMap($a, $c, [
            'version' => 'getVersion',
            'author' => 'getAuthor',
            'copyright' => 'getCopyright',
            'url' => 'getURL',
        ]);

        return $a;
    }

    public static function getSignature(array $a): string
    {
        $prefix = Caster::PREFIX_VIRTUAL;
        $signature = '';

        if (isset($a[$prefix.'parameters'])) {
            foreach ($a[$prefix.'parameters']->value as $k => $param) {
                $signature .= ', ';
                if ($type = $param->getType()) {
                    if (!$type instanceof \ReflectionNamedType) {
                        $signature .= $type.' ';
                    } else {
                        if ($param->allowsNull() && !\in_array($type->getName(), ['mixed', 'null'], true)) {
                            $signature .= '?';
                        }
                        $signature .= substr(strrchr('\\'.$type->getName(), '\\'), 1).' ';
                    }
                }
                $signature .= $k;

                if (!$param->isDefaultValueAvailable()) {
                    continue;
                }
                $v = $param->getDefaultValue();
                $signature .= ' = ';

                if ($param->isDefaultValueConstant()) {
                    $signature .= substr(strrchr('\\'.$param->getDefaultValueConstantName(), '\\'), 1);
                } elseif (null === $v) {
                    $signature .= 'null';
                } elseif (\is_array($v)) {
                    $signature .= $v ? '[…'.\count($v).']' : '[]';
                } elseif (\is_string($v)) {
                    $signature .= 10 > \strlen($v) && !str_contains($v, '\\') ? "'{$v}'" : "'…".\strlen($v)."'";
                } elseif (\is_bool($v)) {
                    $signature .= $v ? 'true' : 'false';
                } elseif (\is_object($v)) {
                    $signature .= 'new '.substr(strrchr('\\'.get_debug_type($v), '\\'), 1);
                } else {
                    $signature .= $v;
                }
            }
        }
        $signature = (empty($a[$prefix.'returnsReference']) ? '' : '&').'('.substr($signature, 2).')';

        if (isset($a[$prefix.'returnType'])) {
            $signature .= ': '.substr(strrchr('\\'.$a[$prefix.'returnType'], '\\'), 1);
        }

        return $signature;
    }

    private static function addExtra(array &$a, \Reflector $c): void
    {
        $x = isset($a[Caster::PREFIX_VIRTUAL.'extra']) ? $a[Caster::PREFIX_VIRTUAL.'extra']->value : [];

        if (method_exists($c, 'getFileName') && $m = $c->getFileName()) {
            $x['file'] = new LinkStub($m, $c->getStartLine());
            $x['line'] = $c->getStartLine().' to '.$c->getEndLine();
        }

        self::addMap($x, $c, self::EXTRA_MAP, '');

        if ($x) {
            $a[Caster::PREFIX_VIRTUAL.'extra'] = new EnumStub($x);
        }
    }

    private static function addMap(array &$a, object $c, array $map, string $prefix = Caster::PREFIX_VIRTUAL): void
    {
        foreach ($map as $k => $m) {
            if ('isDisabled' === $k) {
                continue;
            }

            if (method_exists($c, $m) && false !== ($m = $c->$m()) && null !== $m) {
                $a[$prefix.$k] = $m instanceof \Reflector ? $m->name : $m;
            }
        }
    }

    private static function addAttributes(array &$a, \Reflector $c, string $prefix = Caster::PREFIX_VIRTUAL): void
    {
        foreach ($c->getAttributes() as $n) {
            $a[$prefix.'attributes'][] = $n;
        }
    }
}
