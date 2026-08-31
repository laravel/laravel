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
 * Casts XmlReader class to array representation.
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 *
 * @final
 *
 * @internal since Symfony 7.3
 */
class XmlReaderCaster
{
    private const NODE_TYPES = [
        \XMLReader::NONE => 'NONE',
        \XMLReader::ELEMENT => 'ELEMENT',
        \XMLReader::ATTRIBUTE => 'ATTRIBUTE',
        \XMLReader::TEXT => 'TEXT',
        \XMLReader::CDATA => 'CDATA',
        \XMLReader::ENTITY_REF => 'ENTITY_REF',
        \XMLReader::ENTITY => 'ENTITY',
        \XMLReader::PI => 'PI (Processing Instruction)',
        \XMLReader::COMMENT => 'COMMENT',
        \XMLReader::DOC => 'DOC',
        \XMLReader::DOC_TYPE => 'DOC_TYPE',
        \XMLReader::DOC_FRAGMENT => 'DOC_FRAGMENT',
        \XMLReader::NOTATION => 'NOTATION',
        \XMLReader::WHITESPACE => 'WHITESPACE',
        \XMLReader::SIGNIFICANT_WHITESPACE => 'SIGNIFICANT_WHITESPACE',
        \XMLReader::END_ELEMENT => 'END_ELEMENT',
        \XMLReader::END_ENTITY => 'END_ENTITY',
        \XMLReader::XML_DECLARATION => 'XML_DECLARATION',
    ];

    public static function castXmlReader(\XMLReader $reader, array $a, Stub $stub, bool $isNested): array
    {
        try {
            $properties = [
                'LOADDTD' => @$reader->getParserProperty(\XMLReader::LOADDTD),
                'DEFAULTATTRS' => @$reader->getParserProperty(\XMLReader::DEFAULTATTRS),
                'VALIDATE' => @$reader->getParserProperty(\XMLReader::VALIDATE),
                'SUBST_ENTITIES' => @$reader->getParserProperty(\XMLReader::SUBST_ENTITIES),
            ];
        } catch (\Error) {
            $properties = [
                'LOADDTD' => false,
                'DEFAULTATTRS' => false,
                'VALIDATE' => false,
                'SUBST_ENTITIES' => false,
            ];
        }

        $props = Caster::PREFIX_VIRTUAL.'parserProperties';
        $info = [
            'localName' => $reader->localName,
            'prefix' => $reader->prefix,
            'nodeType' => new ConstStub(self::NODE_TYPES[$reader->nodeType], $reader->nodeType),
            'depth' => $reader->depth,
            'isDefault' => $reader->isDefault,
            'isEmptyElement' => \XMLReader::NONE === $reader->nodeType ? null : $reader->isEmptyElement,
            'xmlLang' => $reader->xmlLang,
            'attributeCount' => $reader->attributeCount,
            'value' => $reader->value,
            'namespaceURI' => $reader->namespaceURI,
            'baseURI' => $reader->baseURI ? new LinkStub($reader->baseURI) : $reader->baseURI,
            $props => $properties,
        ];

        if ($info[$props] = Caster::filter($info[$props], Caster::EXCLUDE_EMPTY, [], $count)) {
            $info[$props] = new EnumStub($info[$props]);
            $info[$props]->cut = $count;
        }

        $a = Caster::filter($a, Caster::EXCLUDE_UNINITIALIZED, [], $count);
        $info = Caster::filter($info, Caster::EXCLUDE_EMPTY, [], $count);
        // +2 because hasValue and hasAttributes are always filtered
        $stub->cut += $count + 2;

        return $a + $info;
    }
}
