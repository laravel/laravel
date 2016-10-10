<?php

class PHPParser_Serializer_XML implements PHPParser_Serializer
{
    protected $writer;

    /**
     * Constructs a XML serializer.
     */
    public function __construct() {
        $this->writer = new XMLWriter;
        $this->writer->openMemory();
        $this->writer->setIndent(true);
    }

    public function serialize(array $nodes) {
        $this->writer->flush();
        $this->writer->startDocument('1.0', 'UTF-8');

        $this->writer->startElement('AST');
        $this->writer->writeAttribute('xmlns:node',      'http://nikic.github.com/PHPParser/XML/node');
        $this->writer->writeAttribute('xmlns:subNode',   'http://nikic.github.com/PHPParser/XML/subNode');
        $this->writer->writeAttribute('xmlns:attribute', 'http://nikic.github.com/PHPParser/XML/attribute');
        $this->writer->writeAttribute('xmlns:scalar',    'http://nikic.github.com/PHPParser/XML/scalar');

        $this->_serialize($nodes);

        $this->writer->endElement();

        return $this->writer->outputMemory();
    }

    protected function _serialize($node) {
        if ($node instanceof PHPParser_Node) {
            $this->writer->startElement('node:' . $node->getType());

            foreach ($node->getAttributes() as $name => $value) {
                $this->writer->startElement('attribute:' . $name);
                $this->_serialize($value);
                $this->writer->endElement();
            }

            foreach ($node as $name => $subNode) {
                $this->writer->startElement('subNode:' . $name);
                $this->_serialize($subNode);
                $this->writer->endElement();
            }

            $this->writer->endElement();
        } elseif ($node instanceof PHPParser_Comment) {
            $this->writer->startElement('comment');
            $this->writer->writeAttribute('isDocComment', $node instanceof PHPParser_Comment_Doc ? 'true' : 'false');
            $this->writer->writeAttribute('line', $node->getLine());
            $this->writer->text($node->getText());
            $this->writer->endElement();
        } elseif (is_array($node)) {
            $this->writer->startElement('scalar:array');
            foreach ($node as $subNode) {
                $this->_serialize($subNode);
            }
            $this->writer->endElement();
        } elseif (is_string($node)) {
            $this->writer->writeElement('scalar:string', $node);
        } elseif (is_int($node)) {
            $this->writer->writeElement('scalar:int', $node);
        } elseif (is_float($node)) {
            $this->writer->writeElement('scalar:float', $node);
        } elseif (true === $node) {
            $this->writer->writeElement('scalar:true');
        } elseif (false === $node) {
            $this->writer->writeElement('scalar:false');
        } elseif (null === $node) {
            $this->writer->writeElement('scalar:null');
        } else {
            throw new InvalidArgumentException('Unexpected node type');
        }
    }
}