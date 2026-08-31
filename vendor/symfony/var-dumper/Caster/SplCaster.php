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
 * Casts SPL related classes to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 *
 * @internal since Symfony 7.3
 */
class SplCaster
{
    private const SPL_FILE_OBJECT_FLAGS = [
        \SplFileObject::DROP_NEW_LINE => 'DROP_NEW_LINE',
        \SplFileObject::READ_AHEAD => 'READ_AHEAD',
        \SplFileObject::SKIP_EMPTY => 'SKIP_EMPTY',
        \SplFileObject::READ_CSV => 'READ_CSV',
    ];

    public static function castArrayObject(\ArrayObject $c, array $a, Stub $stub, bool $isNested): array
    {
        return self::castSplArray($c, $a, $stub, $isNested);
    }

    public static function castArrayIterator(\ArrayIterator $c, array $a, Stub $stub, bool $isNested): array
    {
        return self::castSplArray($c, $a, $stub, $isNested);
    }

    public static function castHeap(\Iterator $c, array $a, Stub $stub, bool $isNested): array
    {
        $a += [
            Caster::PREFIX_VIRTUAL.'heap' => iterator_to_array(clone $c),
        ];

        return $a;
    }

    public static function castDoublyLinkedList(\SplDoublyLinkedList $c, array $a, Stub $stub, bool $isNested): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;
        $mode = $c->getIteratorMode();
        $c->setIteratorMode(\SplDoublyLinkedList::IT_MODE_KEEP | $mode & ~\SplDoublyLinkedList::IT_MODE_DELETE);

        $a += [
            $prefix.'mode' => new ConstStub((($mode & \SplDoublyLinkedList::IT_MODE_LIFO) ? 'IT_MODE_LIFO' : 'IT_MODE_FIFO').' | '.(($mode & \SplDoublyLinkedList::IT_MODE_DELETE) ? 'IT_MODE_DELETE' : 'IT_MODE_KEEP'), $mode),
            $prefix.'dllist' => iterator_to_array($c),
        ];
        $c->setIteratorMode($mode);

        return $a;
    }

    public static function castFileInfo(\SplFileInfo $c, array $a, Stub $stub, bool $isNested): array
    {
        static $map = [
            'path' => 'getPath',
            'filename' => 'getFilename',
            'basename' => 'getBasename',
            'pathname' => 'getPathname',
            'extension' => 'getExtension',
            'realPath' => 'getRealPath',
            'aTime' => 'getATime',
            'mTime' => 'getMTime',
            'cTime' => 'getCTime',
            'inode' => 'getInode',
            'size' => 'getSize',
            'perms' => 'getPerms',
            'owner' => 'getOwner',
            'group' => 'getGroup',
            'type' => 'getType',
            'writable' => 'isWritable',
            'readable' => 'isReadable',
            'executable' => 'isExecutable',
            'file' => 'isFile',
            'dir' => 'isDir',
            'link' => 'isLink',
            'linkTarget' => 'getLinkTarget',
        ];

        $prefix = Caster::PREFIX_VIRTUAL;
        unset($a["\0SplFileInfo\0fileName"]);
        unset($a["\0SplFileInfo\0pathName"]);

        try {
            $c->isReadable();
        } catch (\RuntimeException $e) {
            if ('Object not initialized' !== $e->getMessage()) {
                throw $e;
            }

            $a[$prefix.'⚠'] = 'The parent constructor was not called: the object is in an invalid state';

            return $a;
        } catch (\Error $e) {
            if ('Object not initialized' !== $e->getMessage()) {
                throw $e;
            }

            $a[$prefix.'⚠'] = 'The parent constructor was not called: the object is in an invalid state';

            return $a;
        }

        foreach ($map as $key => $accessor) {
            try {
                $a[$prefix.$key] = $c->$accessor();
            } catch (\Exception) {
            }
        }

        if ($a[$prefix.'realPath'] ?? false) {
            $a[$prefix.'realPath'] = new LinkStub($a[$prefix.'realPath']);
        }

        if (isset($a[$prefix.'perms'])) {
            $a[$prefix.'perms'] = new ConstStub(\sprintf('0%o', $a[$prefix.'perms']), $a[$prefix.'perms']);
        }

        static $mapDate = ['aTime', 'mTime', 'cTime'];
        foreach ($mapDate as $key) {
            if (isset($a[$prefix.$key])) {
                $a[$prefix.$key] = new ConstStub(date('Y-m-d H:i:s', $a[$prefix.$key]), $a[$prefix.$key]);
            }
        }

        return $a;
    }

    public static function castFileObject(\SplFileObject $c, array $a, Stub $stub, bool $isNested): array
    {
        static $map = [
            'csvControl' => 'getCsvControl',
            'flags' => 'getFlags',
            'maxLineLen' => 'getMaxLineLen',
            'fstat' => 'fstat',
            'eof' => 'eof',
            'key' => 'key',
        ];

        $prefix = Caster::PREFIX_VIRTUAL;

        foreach ($map as $key => $accessor) {
            try {
                $a[$prefix.$key] = $c->$accessor();
            } catch (\Exception) {
            }
        }

        if (isset($a[$prefix.'flags'])) {
            $flagsArray = [];
            foreach (self::SPL_FILE_OBJECT_FLAGS as $value => $name) {
                if ($a[$prefix.'flags'] & $value) {
                    $flagsArray[] = $name;
                }
            }
            $a[$prefix.'flags'] = new ConstStub(implode('|', $flagsArray), $a[$prefix.'flags']);
        }

        if (isset($a[$prefix.'fstat'])) {
            $a[$prefix.'fstat'] = new CutArrayStub($a[$prefix.'fstat'], ['dev', 'ino', 'nlink', 'rdev', 'blksize', 'blocks']);
        }

        return $a;
    }

    public static function castObjectStorage(\SplObjectStorage $c, array $a, Stub $stub, bool $isNested): array
    {
        $storage = [];
        unset($a[Caster::PREFIX_DYNAMIC."\0gcdata"]); // Don't hit https://bugs.php.net/65967
        unset($a["\0SplObjectStorage\0storage"]);

        $clone = clone $c;
        foreach ($clone as $obj) {
            $storage[] = new EnumStub([
                'object' => $obj,
                'info' => $clone->getInfo(),
            ]);
        }

        $a += [
            Caster::PREFIX_VIRTUAL.'storage' => $storage,
        ];

        return $a;
    }

    public static function castOuterIterator(\OuterIterator $c, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'innerIterator'] = $c->getInnerIterator();

        return $a;
    }

    public static function castWeakReference(\WeakReference $c, array $a, Stub $stub, bool $isNested): array
    {
        $a[Caster::PREFIX_VIRTUAL.'object'] = $c->get();

        return $a;
    }

    public static function castWeakMap(\WeakMap $c, array $a, Stub $stub, bool $isNested): array
    {
        $map = [];

        foreach (clone $c as $obj => $data) {
            $map[] = new EnumStub([
                'object' => $obj,
                'data' => $data,
            ]);
        }

        $a += [
            Caster::PREFIX_VIRTUAL.'map' => $map,
        ];

        return $a;
    }

    private static function castSplArray(\ArrayObject|\ArrayIterator $c, array $a, Stub $stub, bool $isNested): array
    {
        $prefix = Caster::PREFIX_VIRTUAL;
        $flags = $c->getFlags();

        if (!($flags & \ArrayObject::STD_PROP_LIST)) {
            $c->setFlags(\ArrayObject::STD_PROP_LIST);
            $a = Caster::castObject($c, $c::class, method_exists($c, '__debugInfo'), $stub->class);
            $c->setFlags($flags);
        }

        unset($a["\0ArrayObject\0storage"], $a["\0ArrayIterator\0storage"]);

        $a += [
            $prefix.'storage' => $c->getArrayCopy(),
            $prefix.'flag::STD_PROP_LIST' => (bool) ($flags & \ArrayObject::STD_PROP_LIST),
            $prefix.'flag::ARRAY_AS_PROPS' => (bool) ($flags & \ArrayObject::ARRAY_AS_PROPS),
        ];
        if ($c instanceof \ArrayObject) {
            $a[$prefix.'iteratorClass'] = new ClassStub($c->getIteratorClass());
        }

        return $a;
    }
}
