<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String\Inflector;

final class SpanishInflector implements InflectorInterface
{
    /**
     * A list of all rules for pluralise.
     *
     * @see https://www.spanishdict.com/guide/spanish-plural-noun-forms
     * @see https://www.rae.es/gram%C3%A1tica/morfolog%C3%ADa/la-formaci%C3%B3n-del-plural-plurales-en-s-y-plurales-en-es-reglas-generales
     */
    // First entry: regex
    // Second entry: replacement
    private const PLURALIZE_REGEXP = [
        // Specials sí, no
        ['/(sí|no)$/i', '\1es'],

        // Words ending with vowel must use -s (RAE 3.2a, 3.2c)
        ['/(a|e|i|o|u|á|é|í|ó|ú)$/i', '\1s'],

        // Word ending in s or x and the previous letter is accented (RAE 3.2n)
        ['/ás$/i', 'ases'],
        ['/és$/i', 'eses'],
        ['/ís$/i', 'ises'],
        ['/ós$/i', 'oses'],
        ['/ús$/i', 'uses'],

        // Words ending in -ión must changed to -iones
        ['/ión$/i', '\1iones'],

        // Words ending in some consonants must use -es (RAE 3.2k)
        ['/(l|r|n|d|j|s|x|ch|y)$/i', '\1es'],

        // Word ending in z, must changed to ces
        ['/(z)$/i', 'ces'],
    ];

    /**
     * A list of all rules for singularize.
     */
    private const SINGULARIZE_REGEXP = [
        // Specials sí, no
        ['/(sí|no)es$/i', '\1'],

        // Words ending in -ión must changed to -iones
        ['/iones$/i', '\1ión'],

        // Word ending in z, must changed to ces
        ['/ces$/i', 'z'],

        // Word ending in s or x and the previous letter is accented (RAE 3.2n)
        ['/(\w)ases$/i', '\1ás'],
        ['/eses$/i', 'és'],
        ['/ises$/i', 'ís'],
        ['/(\w{2,})oses$/i', '\1ós'],
        ['/(\w)uses$/i', '\1ús'],

        // Words ending in some consonants and -es, must be the consonants
        ['/(l|r|n|d|j|s|x|ch|y)e?s$/i', '\1'],

        // Words ended with vowel and s, must be vowel
        ['/(a|e|i|o|u|á|é|ó|í|ú)s$/i', '\1'],
    ];

    private const UNINFLECTED_RULES = [
        // Words ending with pies (RAE 3.2n)
        '/.*(piés)$/i',
    ];

    private const UNINFLECTED = '/^(lunes|martes|miércoles|jueves|viernes|análisis|torax|yo|pies)$/i';

    public function singularize(string $plural): array
    {
        if ($this->isInflectedWord($plural)) {
            return [$plural];
        }

        foreach (self::SINGULARIZE_REGEXP as $rule) {
            [$regexp, $replace] = $rule;

            if (1 === preg_match($regexp, $plural)) {
                return [preg_replace($regexp, $replace, $plural)];
            }
        }

        return [$plural];
    }

    public function pluralize(string $singular): array
    {
        if ($this->isInflectedWord($singular)) {
            return [$singular];
        }

        foreach (self::PLURALIZE_REGEXP as $rule) {
            [$regexp, $replace] = $rule;

            if (1 === preg_match($regexp, $singular)) {
                return [preg_replace($regexp, $replace, $singular)];
            }
        }

        return [$singular.'s'];
    }

    private function isInflectedWord(string $word): bool
    {
        foreach (self::UNINFLECTED_RULES as $rule) {
            if (1 === preg_match($rule, $word)) {
                return true;
            }
        }

        return 1 === preg_match(self::UNINFLECTED, $word);
    }
}
