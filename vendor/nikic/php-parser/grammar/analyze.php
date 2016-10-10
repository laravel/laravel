<?php

const GRAMMAR_FILE = './zend_language_parser.phpy';

const LIB = '(?(DEFINE)
    (?<singleQuotedString>\'[^\\\\\']*+(?:\\\\.[^\\\\\']*+)*+\')
    (?<doubleQuotedString>"[^\\\\"]*+(?:\\\\.[^\\\\"]*+)*+")
    (?<string>(?&singleQuotedString)|(?&doubleQuotedString))
    (?<comment>/\*[^*]*+(?:\*(?!/)[^*]*+)*+\*/)
    (?<code>\{[^\'"/{}]*+(?:(?:(?&string)|(?&comment)|(?&code)|/)[^\'"/{}]*+)*+})
)';

const RULE_BLOCK = '(?<name>[a-z_]++):(?<rules>[^\'"/{};]*+(?:(?:(?&string)|(?&comment)|(?&code)|/|})[^\'"/{};]*+)*+);';

$usedTerminals = array_flip(array(
    'T_VARIABLE', 'T_STRING', 'T_INLINE_HTML', 'T_ENCAPSED_AND_WHITESPACE',
    'T_LNUMBER', 'T_DNUMBER', 'T_CONSTANT_ENCAPSED_STRING', 'T_STRING_VARNAME', 'T_NUM_STRING'
));
$unusedNonterminals = array_flip(array(
    'case_separator', 'optional_comma'
));

function regex($regex) {
    return '~' . LIB . '(?:' . str_replace('~', '\~', $regex) . ')~';
}

function magicSplit($regex, $string) {
    $pieces = preg_split(regex('(?:(?&string)|(?&comment)|(?&code))(*SKIP)(*FAIL)|' . $regex), $string);

    foreach ($pieces as &$piece) {
        $piece = trim($piece);
    }

    return array_filter($pieces);
}

echo '<pre>';

////////////////////
////////////////////
////////////////////

list($defs, $ruleBlocks) = magicSplit('%%', file_get_contents(GRAMMAR_FILE));

if ('' !== trim(preg_replace(regex(RULE_BLOCK), '', $ruleBlocks))) {
    die('Not all rule blocks were properly recognized!');
}

preg_match_all(regex(RULE_BLOCK), $ruleBlocks, $ruleBlocksMatches, PREG_SET_ORDER);
foreach ($ruleBlocksMatches as $match) {
    $ruleBlockName = $match['name'];
    $rules = magicSplit('\|', $match['rules']);

    foreach ($rules as &$rule) {
        $parts = magicSplit('\s+', $rule);
        $usedParts = array();

        foreach ($parts as $part) {
            if ('{' === $part[0]) {
                preg_match_all('~\$([0-9]+)~', $part, $backReferencesMatches, PREG_SET_ORDER);
                foreach ($backReferencesMatches as $match) {
                    $usedParts[$match[1]] = true;
                }
            }
        }

        $i = 1;
        foreach ($parts as &$part) {
            if ('/' === $part[0]) {
                continue;
            }

            if (isset($usedParts[$i])) {
                if ('\'' === $part[0] || '{' === $part[0]
                    || (ctype_upper($part[0]) && !isset($usedTerminals[$part]))
                    || (ctype_lower($part[0]) && isset($unusedNonterminals[$part]))
                ) {
                    $part = '<span style="background-color: red; color: white;">' . $part . '</span>';
                } else {
                    $part = '<strong><em>' . $part . '</em></strong>';
                }
            } elseif ((ctype_upper($part[0]) && isset($usedTerminals[$part]))
                      || (ctype_lower($part[0]) && !isset($unusedNonterminals[$part]))

            ) {
                $part = '<span style="background-color: blue; color: white;">' . $part . '</span>';
            }

            ++$i;
        }

        $rule = implode(' ', $parts);
    }

    echo $ruleBlockName, ':', "\n", '      ', implode("\n" . '    | ', $rules), "\n", ';', "\n\n";
}