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

use FFI\CData;
use FFI\CType;
use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * Casts FFI extension classes to array representation.
 *
 * @author Nesmeyanov Kirill <nesk@xakep.ru>
 */
final class FFICaster
{
    /**
     * In case of "char*" contains a string, the length of which depends on
     * some other parameter, then during the generation of the string it is
     * possible to go beyond the allowable memory area.
     *
     * This restriction serves to ensure that processing does not take
     * up the entire allowable PHP memory limit.
     */
    private const MAX_STRING_LENGTH = 255;

    public static function castCTypeOrCData(CData|CType $data, array $args, Stub $stub): array
    {
        if ($data instanceof CType) {
            $type = $data;
            $data = null;
        } else {
            $type = \FFI::typeof($data);
        }

        $stub->class = \sprintf('%s<%s> size %d align %d', ($data ?? $type)::class, $type->getName(), $type->getSize(), $type->getAlignment());

        return match ($type->getKind()) {
            CType::TYPE_FLOAT,
            CType::TYPE_DOUBLE,
            \defined('\FFI\CType::TYPE_LONGDOUBLE') ? CType::TYPE_LONGDOUBLE : -1,
            CType::TYPE_UINT8,
            CType::TYPE_SINT8,
            CType::TYPE_UINT16,
            CType::TYPE_SINT16,
            CType::TYPE_UINT32,
            CType::TYPE_SINT32,
            CType::TYPE_UINT64,
            CType::TYPE_SINT64,
            CType::TYPE_BOOL,
            CType::TYPE_CHAR,
            CType::TYPE_ENUM => null !== $data ? [Caster::PREFIX_VIRTUAL.'cdata' => $data->cdata] : [],
            CType::TYPE_POINTER => self::castFFIPointer($stub, $type, $data),
            CType::TYPE_STRUCT => self::castFFIStructLike($type, $data),
            CType::TYPE_FUNC => self::castFFIFunction($stub, $type),
            default => $args,
        };
    }

    private static function castFFIFunction(Stub $stub, CType $type): array
    {
        $arguments = [];

        for ($i = 0, $count = $type->getFuncParameterCount(); $i < $count; ++$i) {
            $param = $type->getFuncParameterType($i);

            $arguments[] = $param->getName();
        }

        $abi = match ($type->getFuncABI()) {
            CType::ABI_DEFAULT,
            CType::ABI_CDECL => '[cdecl]',
            CType::ABI_FASTCALL => '[fastcall]',
            CType::ABI_THISCALL => '[thiscall]',
            CType::ABI_STDCALL => '[stdcall]',
            CType::ABI_PASCAL => '[pascal]',
            CType::ABI_REGISTER => '[register]',
            CType::ABI_MS => '[ms]',
            CType::ABI_SYSV => '[sysv]',
            CType::ABI_VECTORCALL => '[vectorcall]',
            default => '[unknown abi]',
        };

        $returnType = $type->getFuncReturnType();

        $stub->class = $abi.' callable('.implode(', ', $arguments).'): '
            .$returnType->getName();

        return [Caster::PREFIX_VIRTUAL.'returnType' => $returnType];
    }

    private static function castFFIPointer(Stub $stub, CType $type, ?CData $data = null): array
    {
        $ptr = $type->getPointerType();

        if (null === $data) {
            return [Caster::PREFIX_VIRTUAL.'0' => $ptr];
        }

        return match ($ptr->getKind()) {
            CType::TYPE_CHAR => [Caster::PREFIX_VIRTUAL.'cdata' => self::castFFIStringValue($data)],
            CType::TYPE_FUNC => self::castFFIFunction($stub, $ptr),
            default => [Caster::PREFIX_VIRTUAL.'cdata' => $data[0]],
        };
    }

    private static function castFFIStringValue(CData $data): string|CutStub
    {
        $result = [];
        $ffi = \FFI::cdef(<<<C
                size_t zend_get_page_size(void);
            C);

        $pageSize = $ffi->zend_get_page_size();

        // get cdata address
        $start = $ffi->cast('uintptr_t', $ffi->cast('char*', $data))->cdata;
        // accessing memory in the same page as $start is safe
        $max = min(self::MAX_STRING_LENGTH, ($start | ($pageSize - 1)) - $start);

        for ($i = 0; $i < $max; ++$i) {
            $result[$i] = $data[$i];

            if ("\0" === $data[$i]) {
                return implode('', $result);
            }
        }

        $string = implode('', $result);
        $stub = new CutStub($string);
        $stub->cut = -1;
        $stub->value = $string;

        return $stub;
    }

    private static function castFFIStructLike(CType $type, ?CData $data = null): array
    {
        $isUnion = ($type->getAttributes() & CType::ATTR_UNION) === CType::ATTR_UNION;

        $result = [];

        foreach ($type->getStructFieldNames() as $name) {
            $field = $type->getStructFieldType($name);

            // Retrieving the value of a field from a union containing
            // a pointer is not a safe operation, because may contain
            // incorrect data.
            $isUnsafe = $isUnion && CType::TYPE_POINTER === $field->getKind();

            if ($isUnsafe) {
                $result[Caster::PREFIX_VIRTUAL.$name.'?'] = $field;
            } elseif (null === $data) {
                $result[Caster::PREFIX_VIRTUAL.$name] = $field;
            } else {
                $fieldName = $data->{$name} instanceof CData ? '' : $field->getName().' ';
                $result[Caster::PREFIX_VIRTUAL.$fieldName.$name] = $data->{$name};
            }
        }

        return $result;
    }
}
