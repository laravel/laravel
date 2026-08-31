<?php

namespace TijsVerkoyen\CssToInlineStyles;

use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\CssSelector\Exception\ExceptionInterface;
use TijsVerkoyen\CssToInlineStyles\Css\Processor;
use TijsVerkoyen\CssToInlineStyles\Css\Property\Processor as PropertyProcessor;
use TijsVerkoyen\CssToInlineStyles\Css\Property\Property;
use TijsVerkoyen\CssToInlineStyles\Css\Rule\Processor as RuleProcessor;

class CssToInlineStyles
{
    /**
     * @var CssSelectorConverter
     */
    private $cssConverter;

    public function __construct()
    {
        $this->cssConverter = new CssSelectorConverter();
    }

    /**
     * Will inline the $css into the given $html
     *
     * Remark: if the html contains <style>-tags those will be used, the rules
     * in $css will be appended.
     *
     * @param string $html
     * @param string $css
     *
     * @return string
     */
    public function convert($html, $css = null)
    {
        $document = $this->createDomDocumentFromHtml($html);
        $processor = new Processor();

        // get all styles from the style-tags
        $rules = $processor->getRules(
            $processor->getCssFromStyleTags($html)
        );

        if ($css !== null) {
            $rules = $processor->getRules($css, $rules);
        }

        $document = $this->inline($document, $rules);

        return $this->getHtmlFromDocument($document);
    }

    /**
     * Inline the given properties on a given DOMElement
     *
     * @param \DOMElement             $element
     * @param Property[] $properties
     *
     * @return \DOMElement
     */
    public function inlineCssOnElement(\DOMElement $element, array $properties)
    {
        if (empty($properties)) {
            return $element;
        }

        $cssProperties = array();
        $inlineProperties = array();

        foreach ($this->getInlineStyles($element) as $property) {
            $inlineProperties[$property->getName()] = $property;
        }

        foreach ($properties as $property) {
            if (!isset($inlineProperties[$property->getName()])) {
                $cssProperties[$property->getName()] = $property;
            }
        }

        $rules = array();
        foreach (array_merge($cssProperties, $inlineProperties) as $property) {
            $rules[] = $property->toString();
        }
        $element->setAttribute('style', implode(' ', $rules));

        return $element;
    }

    /**
     * Get the current inline styles for a given DOMElement
     *
     * @param \DOMElement $element
     *
     * @return Property[]
     */
    public function getInlineStyles(\DOMElement $element)
    {
        $processor = new PropertyProcessor();

        return $processor->convertArrayToObjects(
            $processor->splitIntoSeparateProperties(
                $element->getAttribute('style')
            )
        );
    }

    /**
     * @param string $html
     *
     * @return \DOMDocument
     */
    protected function createDomDocumentFromHtml($html)
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTML(mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8'));
        libxml_use_internal_errors($internalErrors);
        $document->formatOutput = true;

        return $document;
    }

    /**
     * @param \DOMDocument $document
     *
     * @return string
     */
    protected function getHtmlFromDocument(\DOMDocument $document)
    {
        // retrieve the document element
        // we do it this way to preserve the utf-8 encoding
        $htmlElement = $document->documentElement;

        if ($htmlElement === null) {
            throw new \RuntimeException('Failed to get HTML from empty document.');
        }

        $html = $document->saveHTML($htmlElement);

        if ($html === false) {
            throw new \RuntimeException('Failed to get HTML from document.');
        }

        $html = trim($html);

        // retrieve the doctype
        $document->removeChild($htmlElement);
        $doctype = $document->saveHTML();
        if ($doctype === false) {
            $doctype = '';
        }
        $doctype = trim($doctype);

        // if it is the html5 doctype convert it to lowercase
        if ($doctype === '<!DOCTYPE html>') {
            $doctype = strtolower($doctype);
        }

        return $doctype."\n".$html;
    }

    /**
     * @param \DOMDocument    $document
     * @param Css\Rule\Rule[] $rules
     *
     * @return \DOMDocument
     */
    protected function inline(\DOMDocument $document, array $rules)
    {
        if (empty($rules)) {
            return $document;
        }

        /** @var \SplObjectStorage<\DOMElement, array<string, Property>> $propertyStorage */
        $propertyStorage = new \SplObjectStorage();

        $xPath = new \DOMXPath($document);

        usort($rules, array(RuleProcessor::class, 'sortOnSpecificity'));

        foreach ($rules as $rule) {
            try {
                $expression = $this->cssConverter->toXPath($rule->getSelector());
            } catch (ExceptionInterface $e) {
                continue;
            }

            $elements = $xPath->query($expression);

            if ($elements === false) {
                continue;
            }

            foreach ($elements as $element) {
                \assert($element instanceof \DOMElement);
                $propertyStorage[$element] = $this->calculatePropertiesToBeApplied(
                    $rule->getProperties(),
                    $propertyStorage->offsetExists($element) ? $propertyStorage[$element] : array()
                );
            }
        }

        foreach ($propertyStorage as $element) {
            $this->inlineCssOnElement($element, $propertyStorage[$element]);
        }

        return $document;
    }

    /**
     * Merge the CSS rules to determine the applied properties.
     *
     * @param Property[] $properties
     * @param array<string, Property> $cssProperties existing applied properties indexed by name
     *
     * @return array<string, Property> updated properties, indexed by name
     */
    private function calculatePropertiesToBeApplied(array $properties, array $cssProperties): array
    {
        if (empty($properties)) {
            return $cssProperties;
        }

        foreach ($properties as $property) {
            if (isset($cssProperties[$property->getName()])) {
                $existingProperty = $cssProperties[$property->getName()];

                //skip check to overrule if existing property is important and current is not
                if ($existingProperty->isImportant() && !$property->isImportant()) {
                    continue;
                }

                //overrule if current property is important and existing is not, else check specificity
                $overrule = !$existingProperty->isImportant() && $property->isImportant();
                if (!$overrule) {
                    \assert($existingProperty->getOriginalSpecificity() !== null, 'Properties created for parsed CSS always have their associated specificity.');
                    \assert($property->getOriginalSpecificity() !== null, 'Properties created for parsed CSS always have their associated specificity.');
                    $overrule = $existingProperty->getOriginalSpecificity()->compareTo($property->getOriginalSpecificity()) <= 0;
                }

                if ($overrule) {
                    unset($cssProperties[$property->getName()]);
                    $cssProperties[$property->getName()] = $property;
                }
            } else {
                $cssProperties[$property->getName()] = $property;
            }
        }

        return $cssProperties;
    }
}
