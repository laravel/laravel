<?php

namespace TijsVerkoyen\CssToInlineStyles\Css\Rule;

use Symfony\Component\CssSelector\Node\Specificity;
use \TijsVerkoyen\CssToInlineStyles\Css\Property\Processor as PropertyProcessor;

class Processor
{
    /**
     * Splits a string into separate rules
     *
     * @param string $rulesString
     *
     * @return string[]
     */
    public function splitIntoSeparateRules($rulesString)
    {
        $rulesString = $this->cleanup($rulesString);

        return (array) explode('}', $rulesString);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function cleanup($string)
    {
        $string = str_replace(array("\r", "\n"), '', $string);
        $string = str_replace(array("\t"), ' ', $string);
        $string = str_replace('"', '\'', $string);
        $string = preg_replace('|/\*.*?\*/|', '', $string) ?? $string;
        $string = preg_replace('/\s\s+/', ' ', $string) ?? $string;

        $string = trim($string);
        $string = rtrim($string, '}');

        return $string;
    }

    /**
     * Converts a rule-string into an object
     *
     * @param string $rule
     * @param int    $originalOrder
     *
     * @return Rule[]
     */
    public function convertToObjects($rule, $originalOrder)
    {
        $rule = $this->cleanup($rule);

        $chunks = explode('{', $rule);
        if (!isset($chunks[1])) {
            return array();
        }
        $propertiesProcessor = new PropertyProcessor();
        $rules = array();
        $selectors = (array) explode(',', trim($chunks[0]));
        $properties = $propertiesProcessor->splitIntoSeparateProperties($chunks[1]);

        foreach ($selectors as $selector) {
            $selector = trim($selector);
            $specificity = $this->calculateSpecificityBasedOnASelector($selector);

            $rules[] = new Rule(
                $selector,
                $propertiesProcessor->convertArrayToObjects($properties, $specificity),
                $specificity,
                $originalOrder
            );
        }

        return $rules;
    }

    /**
     * Calculates the specificity based on a CSS Selector string,
     * Based on the patterns from premailer/css_parser by Alex Dunae
     *
     * @see https://github.com/premailer/css_parser/blob/master/lib/css_parser/regexps.rb
     *
     * @param string $selector
     *
     * @return Specificity
     */
    public function calculateSpecificityBasedOnASelector($selector)
    {
        $idSelectorCount = preg_match_all("/  \#/ix", $selector, $matches);
        $classAttributesPseudoClassesSelectorsPattern = "  (\.[\w]+)                     # classes
                        |
                        \[(\w+)                       # attributes
                        |
                        (\:(                          # pseudo classes
                          link|visited|active
                          |hover|focus
                          |lang
                          |target
                          |enabled|disabled|checked|indeterminate
                          |root
                          |nth-child|nth-last-child|nth-of-type|nth-last-of-type
                          |first-child|last-child|first-of-type|last-of-type
                          |only-child|only-of-type
                          |empty|contains
                        ))";
        $classAttributesPseudoClassesSelectorCount = preg_match_all("/{$classAttributesPseudoClassesSelectorsPattern}/ix", $selector, $matches);

        $typePseudoElementsSelectorPattern = "  ((^|[\s\+\>\~]+)[\w]+       # elements
                        |
                        \:{1,2}(                    # pseudo-elements
                          after|before
                          |first-letter|first-line
                          |selection
                        )
                      )";
        $typePseudoElementsSelectorCount = preg_match_all("/{$typePseudoElementsSelectorPattern}/ix", $selector, $matches);

        if ($idSelectorCount === false || $classAttributesPseudoClassesSelectorCount === false || $typePseudoElementsSelectorCount === false) {
            throw new \RuntimeException('Failed to calculate specificity based on selector.');
        }

        return new Specificity(
            $idSelectorCount,
            $classAttributesPseudoClassesSelectorCount,
            $typePseudoElementsSelectorCount
        );
    }

    /**
     * @param string[] $rules
     * @param Rule[]   $objects
     *
     * @return Rule[]
     */
    public function convertArrayToObjects(array $rules, array $objects = array())
    {
        $order = 1;
        foreach ($rules as $rule) {
            $objects = array_merge($objects, $this->convertToObjects($rule, $order));
            $order++;
        }

        return $objects;
    }

    /**
     * Sorts an array on the specificity element in an ascending way
     * Lower specificity will be sorted to the beginning of the array
     *
     * @param Rule $e1 The first element.
     * @param Rule $e2 The second element.
     *
     * @return int
     */
    public static function sortOnSpecificity(Rule $e1, Rule $e2)
    {
        $e1Specificity = $e1->getSpecificity();
        $value = $e1Specificity->compareTo($e2->getSpecificity());

        // if the specificity is the same, use the order in which the element appeared
        if ($value === 0) {
            $value = $e1->getOrder() - $e2->getOrder();
        }

        return $value;
    }
}
