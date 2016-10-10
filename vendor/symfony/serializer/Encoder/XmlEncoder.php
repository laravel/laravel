<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Encoder;

use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Encodes XML data.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author John Wards <jwards@whiteoctober.co.uk>
 * @author Fabian Vogler <fabian@equivalence.ch>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class XmlEncoder extends SerializerAwareEncoder implements EncoderInterface, DecoderInterface, NormalizationAwareInterface
{
    /**
     * @var \DOMDocument
     */
    private $dom;
    private $format;
    private $context;
    private $rootNodeName = 'response';
    private $loadOptions;

    /**
     * Construct new XmlEncoder and allow to change the root node element name.
     *
     * @param string   $rootNodeName
     * @param int|null $loadOptions  A bit field of LIBXML_* constants
     */
    public function __construct($rootNodeName = 'response', $loadOptions = null)
    {
        $this->rootNodeName = $rootNodeName;
        $this->loadOptions = null !== $loadOptions ? $loadOptions : LIBXML_NONET | LIBXML_NOBLANKS;
    }

    /**
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = array())
    {
        if ($data instanceof \DOMDocument) {
            return $data->saveXML();
        }

        $xmlRootNodeName = $this->resolveXmlRootName($context);

        $this->dom = $this->createDomDocument($context);
        $this->format = $format;
        $this->context = $context;

        if (null !== $data && !is_scalar($data)) {
            $root = $this->dom->createElement($xmlRootNodeName);
            $this->dom->appendChild($root);
            $this->buildXml($root, $data, $xmlRootNodeName);
        } else {
            $this->appendNode($this->dom, $data, $xmlRootNodeName);
        }

        return $this->dom->saveXML();
    }

    /**
     * {@inheritdoc}
     */
    public function decode($data, $format, array $context = array())
    {
        if ('' === trim($data)) {
            throw new UnexpectedValueException('Invalid XML data, it can not be empty.');
        }

        $internalErrors = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);
        libxml_clear_errors();

        $dom = new \DOMDocument();
        $dom->loadXML($data, $this->loadOptions);

        libxml_use_internal_errors($internalErrors);
        libxml_disable_entity_loader($disableEntities);

        if ($error = libxml_get_last_error()) {
            libxml_clear_errors();

            throw new UnexpectedValueException($error->message);
        }

        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                throw new UnexpectedValueException('Document types are not allowed.');
            }
        }

        $rootNode = $dom->firstChild;

        // todo: throw an exception if the root node name is not correctly configured (bc)

        if ($rootNode->hasChildNodes()) {
            $xpath = new \DOMXPath($dom);
            $data = array();
            foreach ($xpath->query('namespace::*', $dom->documentElement) as $nsNode) {
                $data['@'.$nsNode->nodeName] = $nsNode->nodeValue;
            }

            unset($data['@xmlns:xml']);

            if (empty($data)) {
                return $this->parseXml($rootNode);
            }

            return array_merge($data, (array) $this->parseXml($rootNode));
        }

        if (!$rootNode->hasAttributes()) {
            return $rootNode->nodeValue;
        }

        $data = array();

        foreach ($rootNode->attributes as $attrKey => $attr) {
            $data['@'.$attrKey] = $attr->nodeValue;
        }

        $data['#'] = $rootNode->nodeValue;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return 'xml' === $format;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDecoding($format)
    {
        return 'xml' === $format;
    }

    /**
     * Sets the root node name.
     *
     * @param string $name root node name
     */
    public function setRootNodeName($name)
    {
        $this->rootNodeName = $name;
    }

    /**
     * Returns the root node name.
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return $this->rootNodeName;
    }

    /**
     * @param \DOMNode $node
     * @param string   $val
     *
     * @return bool
     */
    final protected function appendXMLString(\DOMNode $node, $val)
    {
        if (strlen($val) > 0) {
            $frag = $this->dom->createDocumentFragment();
            $frag->appendXML($val);
            $node->appendChild($frag);

            return true;
        }

        return false;
    }

    /**
     * @param \DOMNode $node
     * @param string   $val
     *
     * @return bool
     */
    final protected function appendText(\DOMNode $node, $val)
    {
        $nodeText = $this->dom->createTextNode($val);
        $node->appendChild($nodeText);

        return true;
    }

    /**
     * @param \DOMNode $node
     * @param string   $val
     *
     * @return bool
     */
    final protected function appendCData(\DOMNode $node, $val)
    {
        $nodeText = $this->dom->createCDATASection($val);
        $node->appendChild($nodeText);

        return true;
    }

    /**
     * @param \DOMNode             $node
     * @param \DOMDocumentFragment $fragment
     *
     * @return bool
     */
    final protected function appendDocumentFragment(\DOMNode $node, $fragment)
    {
        if ($fragment instanceof \DOMDocumentFragment) {
            $node->appendChild($fragment);

            return true;
        }

        return false;
    }

    /**
     * Checks the name is a valid xml element name.
     *
     * @param string $name
     *
     * @return bool
     */
    final protected function isElementNameValid($name)
    {
        return $name &&
            false === strpos($name, ' ') &&
            preg_match('#^[\pL_][\pL0-9._:-]*$#ui', $name);
    }

    /**
     * Parse the input DOMNode into an array or a string.
     *
     * @param \DOMNode $node xml to parse
     *
     * @return array|string
     */
    private function parseXml(\DOMNode $node)
    {
        $data = $this->parseXmlAttributes($node);

        $value = $this->parseXmlValue($node);

        if (!count($data)) {
            return $value;
        }

        if (!is_array($value)) {
            $data['#'] = $value;

            return $data;
        }

        if (1 === count($value) && key($value)) {
            $data[key($value)] = current($value);

            return $data;
        }

        foreach ($value as $key => $val) {
            $data[$key] = $val;
        }

        return $data;
    }

    /**
     * Parse the input DOMNode attributes into an array.
     *
     * @param \DOMNode $node xml to parse
     *
     * @return array
     */
    private function parseXmlAttributes(\DOMNode $node)
    {
        if (!$node->hasAttributes()) {
            return array();
        }

        $data = array();

        foreach ($node->attributes as $attr) {
            if (ctype_digit($attr->nodeValue)) {
                $data['@'.$attr->nodeName] = (int) $attr->nodeValue;
            } else {
                $data['@'.$attr->nodeName] = $attr->nodeValue;
            }
        }

        return $data;
    }

    /**
     * Parse the input DOMNode value (content and children) into an array or a string.
     *
     * @param \DOMNode $node xml to parse
     *
     * @return array|string
     */
    private function parseXmlValue(\DOMNode $node)
    {
        if (!$node->hasChildNodes()) {
            return $node->nodeValue;
        }

        if (1 === $node->childNodes->length && in_array($node->firstChild->nodeType, array(XML_TEXT_NODE, XML_CDATA_SECTION_NODE))) {
            return $node->firstChild->nodeValue;
        }

        $value = array();

        foreach ($node->childNodes as $subnode) {
            $val = $this->parseXml($subnode);

            if ('item' === $subnode->nodeName && isset($val['@key'])) {
                if (isset($val['#'])) {
                    $value[$val['@key']] = $val['#'];
                } else {
                    $value[$val['@key']] = $val;
                }
            } else {
                $value[$subnode->nodeName][] = $val;
            }
        }

        foreach ($value as $key => $val) {
            if (is_array($val) && 1 === count($val)) {
                $value[$key] = current($val);
            }
        }

        return $value;
    }

    /**
     * Parse the data and convert it to DOMElements.
     *
     * @param \DOMNode     $parentNode
     * @param array|object $data
     * @param string|null  $xmlRootNodeName
     *
     * @return bool
     *
     * @throws UnexpectedValueException
     */
    private function buildXml(\DOMNode $parentNode, $data, $xmlRootNodeName = null)
    {
        $append = true;

        if (is_array($data) || ($data instanceof \Traversable && !$this->serializer->supportsNormalization($data, $this->format))) {
            foreach ($data as $key => $data) {
                //Ah this is the magic @ attribute types.
                if (0 === strpos($key, '@') && is_scalar($data) && $this->isElementNameValid($attributeName = substr($key, 1))) {
                    $parentNode->setAttribute($attributeName, $data);
                } elseif ($key === '#') {
                    $append = $this->selectNodeType($parentNode, $data);
                } elseif (is_array($data) && false === is_numeric($key)) {
                    // Is this array fully numeric keys?
                    if (ctype_digit(implode('', array_keys($data)))) {
                        /*
                         * Create nodes to append to $parentNode based on the $key of this array
                         * Produces <xml><item>0</item><item>1</item></xml>
                         * From array("item" => array(0,1));.
                         */
                        foreach ($data as $subData) {
                            $append = $this->appendNode($parentNode, $subData, $key);
                        }
                    } else {
                        $append = $this->appendNode($parentNode, $data, $key);
                    }
                } elseif (is_numeric($key) || !$this->isElementNameValid($key)) {
                    $append = $this->appendNode($parentNode, $data, 'item', $key);
                } else {
                    $append = $this->appendNode($parentNode, $data, $key);
                }
            }

            return $append;
        }

        if (is_object($data)) {
            $data = $this->serializer->normalize($data, $this->format, $this->context);
            if (null !== $data && !is_scalar($data)) {
                return $this->buildXml($parentNode, $data, $xmlRootNodeName);
            }

            // top level data object was normalized into a scalar
            if (!$parentNode->parentNode->parentNode) {
                $root = $parentNode->parentNode;
                $root->removeChild($parentNode);

                return $this->appendNode($root, $data, $xmlRootNodeName);
            }

            return $this->appendNode($parentNode, $data, 'data');
        }

        throw new UnexpectedValueException(sprintf('An unexpected value could not be serialized: %s', var_export($data, true)));
    }

    /**
     * Selects the type of node to create and appends it to the parent.
     *
     * @param \DOMNode     $parentNode
     * @param array|object $data
     * @param string       $nodeName
     * @param string       $key
     *
     * @return bool
     */
    private function appendNode(\DOMNode $parentNode, $data, $nodeName, $key = null)
    {
        $node = $this->dom->createElement($nodeName);
        if (null !== $key) {
            $node->setAttribute('key', $key);
        }
        $appendNode = $this->selectNodeType($node, $data);
        // we may have decided not to append this node, either in error or if its $nodeName is not valid
        if ($appendNode) {
            $parentNode->appendChild($node);
        }

        return $appendNode;
    }

    /**
     * Checks if a value contains any characters which would require CDATA wrapping.
     *
     * @param string $val
     *
     * @return bool
     */
    private function needsCdataWrapping($val)
    {
        return preg_match('/[<>&]/', $val);
    }

    /**
     * Tests the value being passed and decide what sort of element to create.
     *
     * @param \DOMNode $node
     * @param mixed    $val
     *
     * @return bool
     *
     * @throws UnexpectedValueException
     */
    private function selectNodeType(\DOMNode $node, $val)
    {
        if (is_array($val)) {
            return $this->buildXml($node, $val);
        } elseif ($val instanceof \SimpleXMLElement) {
            $child = $this->dom->importNode(dom_import_simplexml($val), true);
            $node->appendChild($child);
        } elseif ($val instanceof \Traversable) {
            $this->buildXml($node, $val);
        } elseif (is_object($val)) {
            return $this->buildXml($node, $this->serializer->normalize($val, $this->format, $this->context));
        } elseif (is_numeric($val)) {
            return $this->appendText($node, (string) $val);
        } elseif (is_string($val) && $this->needsCdataWrapping($val)) {
            return $this->appendCData($node, $val);
        } elseif (is_string($val)) {
            return $this->appendText($node, $val);
        } elseif (is_bool($val)) {
            return $this->appendText($node, (int) $val);
        } elseif ($val instanceof \DOMNode) {
            $child = $this->dom->importNode($val, true);
            $node->appendChild($child);
        }

        return true;
    }

    /**
     * Get real XML root node name, taking serializer options into account.
     *
     * @param array $context
     *
     * @return string
     */
    private function resolveXmlRootName(array $context = array())
    {
        return isset($context['xml_root_node_name'])
            ? $context['xml_root_node_name']
            : $this->rootNodeName;
    }

    /**
     * Create a DOM document, taking serializer options into account.
     *
     * @param array $context options that the encoder has access to.
     *
     * @return \DOMDocument
     */
    private function createDomDocument(array $context)
    {
        $document = new \DOMDocument();

        // Set an attribute on the DOM document specifying, as part of the XML declaration,
        $xmlOptions = array(
            // nicely formats output with indentation and extra space
            'xml_format_output' => 'formatOutput',
            // the version number of the document
            'xml_version' => 'xmlVersion',
            // the encoding of the document
            'xml_encoding' => 'encoding',
            // whether the document is standalone
            'xml_standalone' => 'xmlStandalone',
        );
        foreach ($xmlOptions as $xmlOption => $documentProperty) {
            if (isset($context[$xmlOption])) {
                $document->$documentProperty = $context[$xmlOption];
            }
        }

        return $document;
    }
}
