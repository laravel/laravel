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

final class EnglishInflector implements InflectorInterface
{
    /**
     * Map English plural to singular suffixes.
     *
     * @see http://english-zone.com/spelling/plurals.html
     */
    private const PLURAL_MAP = [
        // First entry: plural suffix, reversed
        // Second entry: length of plural suffix
        // Third entry: Whether the suffix may succeed a vowel
        // Fourth entry: Whether the suffix may succeed a consonant
        // Fifth entry: singular suffix, normal

        // insignias (insigne), insignia (insigne)
        ['saingisni', 9, true, true, 'insigne'],
        ['aingisni', 8, true, true, 'insigne'],

        // passersby (passerby)
        ['ybsressap', 9, true, true, 'passerby'],

        // nodes (node)
        ['sedon', 5, true, true, 'node'],

        // bacteria (bacterium)
        ['airetcab', 8, true, true, 'bacterium'],

        // issues (issue)
        ['seussi', 6, true, true, 'issue'],

        // corpora (corpus)
        ['aroproc', 7, true, true, 'corpus'],

        // criteria (criterion)
        ['airetirc', 8, true, true, 'criterion'],

        // curricula (curriculum)
        ['alucirruc', 9, true, true, 'curriculum'],

        // quora (quorum)
        ['arouq', 5, true, true, 'quorum'],

        // genera (genus)
        ['areneg', 6, true, true, 'genus'],

        // media (medium)
        ['aidem', 5, true, true, 'medium'],

        // memoranda (memorandum)
        ['adnaromem', 9, true, true, 'memorandum'],

        // phenomena (phenomenon)
        ['anemonehp', 9, true, true, 'phenomenon'],

        // strata (stratum)
        ['atarts', 6, true, true, 'stratum'],

        // nebulae (nebula)
        ['ea', 2, true, true, 'a'],

        // services (service)
        ['secivres', 8, true, true, 'service'],

        // mice (mouse), lice (louse)
        ['eci', 3, false, true, 'ouse'],

        // geese (goose)
        ['esee', 4, false, true, 'oose'],

        // fungi (fungus), alumni (alumnus), syllabi (syllabus), radii (radius)
        ['i', 1, true, true, 'us'],

        // men (man), women (woman)
        ['nem', 3, true, true, 'man'],

        // children (child)
        ['nerdlihc', 8, true, true, 'child'],

        // oxen (ox)
        ['nexo', 4, false, false, 'ox'],

        // indices (index), appendices (appendix), prices (price)
        ['seci', 4, false, true, ['ex', 'ix', 'ice']],

        // codes (code)
        ['sedoc', 5, false, true, 'code'],

        // selfies (selfie)
        ['seifles', 7, true, true, 'selfie'],

        // zombies (zombie)
        ['seibmoz', 7, true, true, 'zombie'],

        // movies (movie)
        ['seivom', 6, true, true, 'movie'],

        // names (name)
        ['seman', 5, true, false, 'name'],

        // conspectuses (conspectus), prospectuses (prospectus)
        ['sesutcep', 8, true, true, 'pectus'],

        // feet (foot)
        ['teef', 4, true, true, 'foot'],

        // geese (goose)
        ['eseeg', 5, true, true, 'goose'],

        // teeth (tooth)
        ['hteet', 5, true, true, 'tooth'],

        // news (news)
        ['swen', 4, true, true, 'news'],

        // series (series)
        ['seires', 6, true, true, 'series'],

        // babies (baby)
        ['sei', 3, false, true, 'y'],

        // accesses (access), addresses (address), kisses (kiss)
        ['sess', 4, true, false, 'ss'],

        // statuses (status)
        ['sesutats', 8, true, true, 'status'],

        // article (articles), ancle (ancles)
        ['sel', 3, true, true, 'le'],

        // analyses (analysis), ellipses (ellipsis), fungi (fungus),
        // neuroses (neurosis), theses (thesis), emphases (emphasis),
        // oases (oasis), crises (crisis), houses (house), bases (base),
        // atlases (atlas)
        ['ses', 3, true, true, ['s', 'se', 'sis']],

        // objectives (objective), alternative (alternatives)
        ['sevit', 5, true, true, 'tive'],

        // drives (drive)
        ['sevird', 6, false, true, 'drive'],

        // lives (life), wives (wife)
        ['sevi', 4, false, true, 'ife'],

        // moves (move)
        ['sevom', 5, true, true, 'move'],

        // hooves (hoof), dwarves (dwarf), elves (elf), leaves (leaf), caves (cave), staves (staff)
        ['sev', 3, true, true, ['f', 've', 'ff']],

        // axes (axis), axes (ax), axes (axe)
        ['sexa', 4, false, false, ['ax', 'axe', 'axis']],

        // indexes (index), matrixes (matrix)
        ['sex', 3, true, false, 'x'],

        // quizzes (quiz)
        ['sezz', 4, true, false, 'z'],

        // bureaus (bureau)
        ['suae', 4, false, true, 'eau'],

        // fees (fee), trees (tree), employees (employee)
        ['see', 3, true, true, 'ee'],

        // edges (edge)
        ['segd', 4, true, true, 'dge'],

        // outages (outage) - specific fix to avoid 'outag'
        ['segatuo', 7, true, true, 'outage'],

        // traces (trace), faces (face), places (place), pieces (piece)
        ['sec', 3, true, true, 'ce'],

        // roses (rose), garages (garage), cassettes (cassette),
        // waltzes (waltz), heroes (hero), bushes (bush), arches (arch),
        // shoes (shoe)
        ['se', 2, true, true, ['', 'e']],

        // status (status)
        ['sutats', 6, true, true, 'status'],

        // tags (tag)
        ['s', 1, true, true, ''],

        // chateaux (chateau)
        ['xuae', 4, false, true, 'eau'],

        // people (person)
        ['elpoep', 6, true, true, 'person'],
    ];

    /**
     * Map English singular to plural suffixes.
     *
     * @see http://english-zone.com/spelling/plurals.html
     */
    private const SINGULAR_MAP = [
        // First entry: singular suffix, reversed
        // Second entry: length of singular suffix
        // Third entry: Whether the suffix may succeed a vowel
        // Fourth entry: Whether the suffix may succeed a consonant
        // Fifth entry: plural suffix, normal

        // passerby (passersby)
        ['ybressap', 8, true, true, 'passersby'],

        // insigne (insignia, insignias)
        ['engisni', 7, true, true, ['insignia', 'insignias']],

        // nodes (node)
        ['edon', 4, true, true, 'nodes'],

        // axes (axis)
        ['sixa', 4, false, false, 'axes'],

        // criterion (criteria)
        ['airetirc', 8, false, false, 'criterion'],

        // nebulae (nebula)
        ['aluben', 6, false, false, 'nebulae'],

        // children (child)
        ['dlihc', 5, true, true, 'children'],

        // prices (price)
        ['eci', 3, false, true, 'ices'],

        // services (service)
        ['ecivres', 7, true, true, 'services'],

        // lives (life), wives (wife)
        ['efi', 3, false, true, 'ives'],

        // selfies (selfie)
        ['eifles', 6, true, true, 'selfies'],

        // movies (movie)
        ['eivom', 5, true, true, 'movies'],

        // lice (louse)
        ['esuol', 5, false, true, 'lice'],

        // mice (mouse)
        ['esuom', 5, false, true, 'mice'],

        // geese (goose)
        ['esoo', 4, false, true, 'eese'],

        // houses (house), bases (base)
        ['es', 2, true, true, 'ses'],

        // geese (goose)
        ['esoog', 5, true, true, 'geese'],

        // caves (cave)
        ['ev', 2, true, true, 'ves'],

        // drives (drive)
        ['evird', 5, false, true, 'drives'],

        // objectives (objective), alternative (alternatives)
        ['evit', 4, true, true, 'tives'],

        // moves (move)
        ['evom', 4, true, true, 'moves'],

        // staves (staff)
        ['ffats', 5, true, true, 'staves'],

        // hooves (hoof), dwarves (dwarf), elves (elf), leaves (leaf)
        ['ff', 2, true, true, 'ffs'],

        // hooves (hoof), dwarves (dwarf), elves (elf), leaves (leaf)
        ['f', 1, true, true, ['fs', 'ves']],

        // arches (arch)
        ['hc', 2, true, true, 'ches'],

        // bushes (bush)
        ['hs', 2, true, true, 'shes'],

        // teeth (tooth)
        ['htoot', 5, true, true, 'teeth'],

        // albums (album)
        ['mubla', 5, true, true, 'albums'],

        // quorums (quorum)
        ['murouq', 6, true, true, ['quora', 'quorums']],

        // bacteria (bacterium), curricula (curriculum), media (medium), memoranda (memorandum), phenomena (phenomenon), strata (stratum)
        ['mu', 2, true, true, 'a'],

        // men (man), women (woman)
        ['nam', 3, true, true, 'men'],

        // people (person)
        ['nosrep', 6, true, true, ['persons', 'people']],

        // criteria (criterion)
        ['noiretirc', 9, true, true, 'criteria'],

        // phenomena (phenomenon)
        ['nonemonehp', 10, true, true, 'phenomena'],

        // echoes (echo)
        ['ohce', 4, true, true, 'echoes'],

        // heroes (hero)
        ['oreh', 4, true, true, 'heroes'],

        // atlases (atlas)
        ['salta', 5, true, true, 'atlases'],

        // aliases (alias)
        ['saila', 5, true, true, 'aliases'],

        // irises (iris)
        ['siri', 4, true, true, 'irises'],

        // analyses (analysis), ellipses (ellipsis), neuroses (neurosis)
        // theses (thesis), emphases (emphasis), oases (oasis),
        // crises (crisis)
        ['sis', 3, true, true, 'ses'],

        // accesses (access), addresses (address), kisses (kiss)
        ['ss', 2, true, false, 'sses'],

        // syllabi (syllabus)
        ['suballys', 8, true, true, 'syllabi'],

        // buses (bus)
        ['sub', 3, true, true, 'buses'],

        // circuses (circus)
        ['suc', 3, true, true, 'cuses'],

        // hippocampi (hippocampus)
        ['supmacoppih', 11, false, false, 'hippocampi'],

        // campuses (campus)
        ['sup', 3, true, true, 'puses'],

        // status (status)
        ['sutats', 6, true, true, ['status', 'statuses']],

        // conspectuses (conspectus), prospectuses (prospectus)
        ['sutcep', 6, true, true, 'pectuses'],

        // nexuses (nexus)
        ['suxen', 5, false, false, 'nexuses'],

        // fungi (fungus), alumni (alumnus), syllabi (syllabus), radii (radius)
        ['su', 2, true, true, 'i'],

        // news (news)
        ['swen', 4, true, true, 'news'],

        // feet (foot)
        ['toof', 4, true, true, 'feet'],

        // chateaux (chateau), bureaus (bureau)
        ['uae', 3, false, true, ['eaus', 'eaux']],

        // oxen (ox)
        ['xo', 2, false, false, 'oxen'],

        // hoaxes (hoax)
        ['xaoh', 4, true, false, 'hoaxes'],

        // indices (index)
        ['xedni', 5, false, true, ['indicies', 'indexes']],

        // fax (faxes, faxxes)
        ['xaf', 3, true, true, ['faxes', 'faxxes']],

        // boxes (box)
        ['xo', 2, false, true, 'oxes'],

        // indexes (index), matrixes (matrix), appendices (appendix)
        ['x', 1, true, false, ['ces', 'xes']],

        // babies (baby)
        ['y', 1, false, true, 'ies'],

        // quizzes (quiz)
        ['ziuq', 4, true, false, 'quizzes'],

        // waltzes (waltz)
        ['z', 1, true, true, 'zes'],
    ];

    /**
     * A list of words which should not be inflected, reversed.
     */
    private const UNINFLECTED = [
        '',

        // data
        'atad',

        // deer
        'reed',

        // equipment
        'tnempiuqe',

        // feedback
        'kcabdeef',

        // fish
        'hsif',

        // health
        'htlaeh',

        // history
        'yrotsih',

        // info
        'ofni',

        // information
        'noitamrofni',

        // money
        'yenom',

        // moose
        'esoom',

        // series
        'seires',

        // sheep
        'peehs',

        // species
        'seiceps',

        // traffic
        'ciffart',

        // aircraft
        'tfarcria',

        // hardware
        'erawdrah',
    ];

    public function singularize(string $plural): array
    {
        $pluralRev = strrev($plural);
        $lowerPluralRev = strtolower($pluralRev);
        $pluralLength = \strlen($lowerPluralRev);

        // Check if the word is one which is not inflected, return early if so
        if (\in_array($lowerPluralRev, self::UNINFLECTED, true)) {
            return [$plural];
        }

        // The outer loop iterates over the entries of the plural table
        // The inner loop $j iterates over the characters of the plural suffix
        // in the plural table to compare them with the characters of the actual
        // given plural suffix
        foreach (self::PLURAL_MAP as $map) {
            $suffix = $map[0];
            $suffixLength = $map[1];
            $j = 0;

            // Compare characters in the plural table and of the suffix of the
            // given plural one by one
            while ($suffix[$j] === $lowerPluralRev[$j]) {
                // Let $j point to the next character
                ++$j;

                // Successfully compared the last character
                // Add an entry with the singular suffix to the singular array
                if ($j === $suffixLength) {
                    // Is there any character preceding the suffix in the plural string?
                    if ($j < $pluralLength) {
                        $nextIsVowel = str_contains('aeiou', $lowerPluralRev[$j]);

                        if (!$map[2] && $nextIsVowel) {
                            // suffix may not succeed a vowel but next char is one
                            break;
                        }

                        if (!$map[3] && !$nextIsVowel) {
                            // suffix may not succeed a consonant but next char is one
                            break;
                        }
                    }

                    $newBase = substr($plural, 0, $pluralLength - $suffixLength);
                    $newSuffix = $map[4];

                    // Check whether the first character in the plural suffix
                    // is uppercased. If yes, uppercase the first character in
                    // the singular suffix too
                    $firstUpper = ctype_upper($pluralRev[$j - 1]);

                    if (\is_array($newSuffix)) {
                        $singulars = [];

                        foreach ($newSuffix as $newSuffixEntry) {
                            $singulars[] = $newBase.($firstUpper ? ucfirst($newSuffixEntry) : $newSuffixEntry);
                        }

                        return $singulars;
                    }

                    return [$newBase.($firstUpper ? ucfirst($newSuffix) : $newSuffix)];
                }

                // Suffix is longer than word
                if ($j === $pluralLength) {
                    break;
                }
            }
        }

        // Assume that plural and singular is identical
        return [$plural];
    }

    public function pluralize(string $singular): array
    {
        $singularRev = strrev($singular);
        $lowerSingularRev = strtolower($singularRev);
        $singularLength = \strlen($lowerSingularRev);

        // Check if the word is one which is not inflected, return early if so
        if (\in_array($lowerSingularRev, self::UNINFLECTED, true)) {
            return [$singular];
        }

        // The outer loop iterates over the entries of the singular table
        // The inner loop $j iterates over the characters of the singular suffix
        // in the singular table to compare them with the characters of the actual
        // given singular suffix
        foreach (self::SINGULAR_MAP as $map) {
            $suffix = $map[0];
            $suffixLength = $map[1];
            $j = 0;

            // Compare characters in the singular table and of the suffix of the
            // given plural one by one

            while ($suffix[$j] === $lowerSingularRev[$j]) {
                // Let $j point to the next character
                ++$j;

                // Successfully compared the last character
                // Add an entry with the plural suffix to the plural array
                if ($j === $suffixLength) {
                    // Is there any character preceding the suffix in the plural string?
                    if ($j < $singularLength) {
                        $nextIsVowel = str_contains('aeiou', $lowerSingularRev[$j]);

                        if (!$map[2] && $nextIsVowel) {
                            // suffix may not succeed a vowel but next char is one
                            break;
                        }

                        if (!$map[3] && !$nextIsVowel) {
                            // suffix may not succeed a consonant but next char is one
                            break;
                        }
                    }

                    $newBase = substr($singular, 0, $singularLength - $suffixLength);
                    $newSuffix = $map[4];

                    // Check whether the first character in the singular suffix
                    // is uppercased. If yes, uppercase the first character in
                    // the singular suffix too
                    $firstUpper = ctype_upper($singularRev[$j - 1]);

                    if (\is_array($newSuffix)) {
                        $plurals = [];

                        foreach ($newSuffix as $newSuffixEntry) {
                            $plurals[] = $newBase.($firstUpper ? ucfirst($newSuffixEntry) : $newSuffixEntry);
                        }

                        return $plurals;
                    }

                    return [$newBase.($firstUpper ? ucfirst($newSuffix) : $newSuffix)];
                }

                // Suffix is longer than word
                if ($j === $singularLength) {
                    break;
                }
            }
        }

        // Assume that plural is singular with a trailing `s`
        return [$singular.'s'];
    }
}
