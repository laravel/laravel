<?php declare(strict_types = 1);
namespace TheSeer\Tokenizer;

use DOMDocument;

class XMLSerializer {

    /** @var NamespaceUri */
    private $xmlns;

    /**
     * XMLSerializer constructor.
     *
     * @param NamespaceUri $xmlns
     */
    public function __construct(?NamespaceUri $xmlns = null) {
        if ($xmlns === null) {
            $xmlns = new NamespaceUri('https://github.com/theseer/tokenizer');
        }
        $this->xmlns = $xmlns;
    }

    public function toDom(TokenCollection $tokens): DOMDocument {
        $dom                     = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($this->toXML($tokens));

        return $dom;
    }

    public function toXML(TokenCollection $tokens): string {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument();
        $writer->startElement('source');
        $writer->writeAttribute('xmlns', $this->xmlns->asString());

        if (\count($tokens) > 0) {
            $writer->startElement('line');
            $writer->writeAttribute('no', '1');

            $iterator = $tokens->getIterator();
            $previousToken = $iterator->current();
            $previousLine = $previousToken->getLine();

            foreach ($iterator as $token) {
                $line = $token->getLine();
                if ($previousLine < $line) {
                    $writer->endElement();

                    $writer->startElement('line');
                    $writer->writeAttribute('no', (string)$line);
                    $previousLine = $line;
                }

                $value = $token->getValue();
                if ($value !== '') {
                    $writer->startElement('token');
                    $writer->writeAttribute('name', $token->getName());
                    $writer->writeRaw(\htmlspecialchars($value, \ENT_NOQUOTES | \ENT_DISALLOWED | \ENT_XML1));
                    $writer->endElement();
                }
            }

            $writer->endElement();
        }

        $writer->endElement();
        $writer->endDocument();

        return $writer->outputMemory();
    }
}
