<?php

namespace TijsVerkoyen\CssToInlineStyles\Css;

use TijsVerkoyen\CssToInlineStyles\Css\Rule\Processor as RuleProcessor;
use TijsVerkoyen\CssToInlineStyles\Css\Rule\Rule;

class Processor
{
    /**
     * Get the rules from a given CSS-string
     *
     * @param string $css
     * @param Rule[] $existingRules
     *
     * @return Rule[]
     */
    public function getRules($css, $existingRules = array())
    {
        $css = $this->doCleanup($css);
        $rulesProcessor = new RuleProcessor();
        $rules = $rulesProcessor->splitIntoSeparateRules($css);

        return $rulesProcessor->convertArrayToObjects($rules, $existingRules);
    }

    /**
     * Get the CSS from the style-tags in the given HTML-string
     *
     * @param string $html
     *
     * @return string
     */
    public function getCssFromStyleTags($html)
    {
        $css = '';
        $matches = array();
        $htmlNoComments = preg_replace('|<!--.*?-->|s', '', $html) ?? $html;
        preg_match_all('|<style(?:\s.*)?>(.*)</style>|isU', $htmlNoComments, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $css .= trim($match) . "\n";
            }
        }

        return $css;
    }

    /**
     * @param string $css
     *
     * @return string
     */
    private function doCleanup($css)
    {
        // remove charset
        $css = preg_replace('/@charset "[^"]++";/', '', $css) ?? $css;
        // remove media queries
        $css = preg_replace('/@media [^{]*+{([^{}]++|{[^{}]*+})*+}/', '', $css) ?? $css;

        $css = str_replace(array("\r", "\n"), '', $css);
        $css = str_replace(array("\t"), ' ', $css);
        $css = str_replace('"', '\'', $css);
        $css = preg_replace('|/\*.*?\*/|', '', $css) ?? $css;
        $css = preg_replace('/\s\s++/', ' ', $css) ?? $css;
        $css = trim($css);

        return $css;
    }
}
