<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules\English;

use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Transformation;
use Doctrine\Inflector\Rules\Word;

class Inflectible
{
    /** @return Transformation[] */
    public static function getSingular(): iterable
    {
        yield new Transformation(new Pattern('(s)tatuses$'), '\1\2tatus');
        yield new Transformation(new Pattern('(s)tatus$'), '\1\2tatus');
        yield new Transformation(new Pattern('(c)ampus$'), '\1\2ampus');
        yield new Transformation(new Pattern('^(.*)(menu)s$'), '\1\2');
        yield new Transformation(new Pattern('(quiz)zes$'), '\\1');
        yield new Transformation(new Pattern('(matr)ices$'), '\1ix');
        yield new Transformation(new Pattern('(vert|ind)ices$'), '\1ex');
        yield new Transformation(new Pattern('^(ox)en'), '\1');
        yield new Transformation(new Pattern('(alias)(es)*$'), '\1');
        yield new Transformation(new Pattern('(buffal|her|potat|tomat|volcan)oes$'), '\1o');
        yield new Transformation(new Pattern('(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$'), '\1us');
        yield new Transformation(new Pattern('([ftw]ax)es'), '\1');
        yield new Transformation(new Pattern('(analys|ax|cris|test|thes)es$'), '\1is');
        yield new Transformation(new Pattern('(shoe|slave)s$'), '\1');
        yield new Transformation(new Pattern('(o)es$'), '\1');
        yield new Transformation(new Pattern('ouses$'), 'ouse');
        yield new Transformation(new Pattern('([^a])uses$'), '\1us');
        yield new Transformation(new Pattern('([m|l])ice$'), '\1ouse');
        yield new Transformation(new Pattern('(x|ch|ss|sh)es$'), '\1');
        yield new Transformation(new Pattern('(m)ovies$'), '\1\2ovie');
        yield new Transformation(new Pattern('(s)eries$'), '\1\2eries');
        yield new Transformation(new Pattern('([^aeiouy]|qu)ies$'), '\1y');
        yield new Transformation(new Pattern('([lr])ves$'), '\1f');
        yield new Transformation(new Pattern('(tive)s$'), '\1');
        yield new Transformation(new Pattern('(hive)s$'), '\1');
        yield new Transformation(new Pattern('(drive)s$'), '\1');
        yield new Transformation(new Pattern('(dive)s$'), '\1');
        yield new Transformation(new Pattern('(olive)s$'), '\1');
        yield new Transformation(new Pattern('([^fo])ves$'), '\1fe');
        yield new Transformation(new Pattern('(^analy)ses$'), '\1sis');
        yield new Transformation(new Pattern('(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$'), '\1\2sis');
        yield new Transformation(new Pattern('(tax)a$'), '\1on');
        yield new Transformation(new Pattern('(c)riteria$'), '\1riterion');
        yield new Transformation(new Pattern('([ti])a(?<!regatta)$'), '\1um');
        yield new Transformation(new Pattern('(p)eople$'), '\1\2erson');
        yield new Transformation(new Pattern('(m)en$'), '\1an');
        yield new Transformation(new Pattern('(c)hildren$'), '\1\2hild');
        yield new Transformation(new Pattern('(f)eet$'), '\1oot');
        yield new Transformation(new Pattern('(n)ews$'), '\1\2ews');
        yield new Transformation(new Pattern('eaus$'), 'eau');
        yield new Transformation(new Pattern('^tights$'), 'tights');
        yield new Transformation(new Pattern('^shorts$'), 'shorts');
        yield new Transformation(new Pattern('s$'), '');
    }

    /** @return Transformation[] */
    public static function getPlural(): iterable
    {
        yield new Transformation(new Pattern('(s)tatus$'), '\1\2tatuses');
        yield new Transformation(new Pattern('(quiz)$'), '\1zes');
        yield new Transformation(new Pattern('^(ox)$'), '\1\2en');
        yield new Transformation(new Pattern('([m|l])ouse$'), '\1ice');
        yield new Transformation(new Pattern('(matr|vert|ind)(ix|ex)$'), '\1ices');
        yield new Transformation(new Pattern('(x|ch|ss|sh)$'), '\1es');
        yield new Transformation(new Pattern('([^aeiouy]|qu)y$'), '\1ies');
        yield new Transformation(new Pattern('(hive|gulf)$'), '\1s');
        yield new Transformation(new Pattern('(?:([^f])fe|([lr])f)$'), '\1\2ves');
        yield new Transformation(new Pattern('sis$'), 'ses');
        yield new Transformation(new Pattern('([ti])um$'), '\1a');
        yield new Transformation(new Pattern('(tax)on$'), '\1a');
        yield new Transformation(new Pattern('(c)riterion$'), '\1riteria');
        yield new Transformation(new Pattern('(p)erson$'), '\1eople');
        yield new Transformation(new Pattern('(m)an$'), '\1en');
        yield new Transformation(new Pattern('(c)hild$'), '\1hildren');
        yield new Transformation(new Pattern('(f)oot$'), '\1eet');
        yield new Transformation(new Pattern('(buffal|her|potat|tomat|volcan)o$'), '\1\2oes');
        yield new Transformation(new Pattern('(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$'), '\1i');
        yield new Transformation(new Pattern('us$'), 'uses');
        yield new Transformation(new Pattern('(alias)$'), '\1es');
        yield new Transformation(new Pattern('(analys|ax|cris|test|thes)is$'), '\1es');
        yield new Transformation(new Pattern('s$'), 's');
        yield new Transformation(new Pattern('^$'), '');
        yield new Transformation(new Pattern('$'), 's');
    }

    /** @return Substitution[] */
    public static function getIrregular(): iterable
    {
        yield new Substitution(new Word('abuse'), new Word('abuses'));
        yield new Substitution(new Word('alga'), new Word('algae'));
        yield new Substitution(new Word('atlas'), new Word('atlases'));
        yield new Substitution(new Word('avalanche'), new Word('avalanches'));
        yield new Substitution(new Word('axis'), new Word('axes'));
        yield new Substitution(new Word('axe'), new Word('axes'));
        yield new Substitution(new Word('beef'), new Word('beefs'));
        yield new Substitution(new Word('blouse'), new Word('blouses'));
        yield new Substitution(new Word('brother'), new Word('brothers'));
        yield new Substitution(new Word('brownie'), new Word('brownies'));
        yield new Substitution(new Word('cache'), new Word('caches'));
        yield new Substitution(new Word('cafe'), new Word('cafes'));
        yield new Substitution(new Word('canvas'), new Word('canvases'));
        yield new Substitution(new Word('cave'), new Word('caves'));
        yield new Substitution(new Word('chateau'), new Word('chateaux'));
        yield new Substitution(new Word('child'), new Word('children'));
        yield new Substitution(new Word('cookie'), new Word('cookies'));
        yield new Substitution(new Word('corpus'), new Word('corpuses'));
        yield new Substitution(new Word('cow'), new Word('cows'));
        yield new Substitution(new Word('criterion'), new Word('criteria'));
        yield new Substitution(new Word('curriculum'), new Word('curricula'));
        yield new Substitution(new Word('curve'), new Word('curves'));
        yield new Substitution(new Word('demo'), new Word('demos'));
        yield new Substitution(new Word('die'), new Word('dice'));
        yield new Substitution(new Word('domino'), new Word('dominoes'));
        yield new Substitution(new Word('echo'), new Word('echoes'));
        yield new Substitution(new Word('emphasis'), new Word('emphases'));
        yield new Substitution(new Word('epoch'), new Word('epochs'));
        yield new Substitution(new Word('foe'), new Word('foes'));
        yield new Substitution(new Word('foot'), new Word('feet'));
        yield new Substitution(new Word('fungus'), new Word('fungi'));
        yield new Substitution(new Word('ganglion'), new Word('ganglions'));
        yield new Substitution(new Word('gas'), new Word('gases'));
        yield new Substitution(new Word('genie'), new Word('genies'));
        yield new Substitution(new Word('genus'), new Word('genera'));
        yield new Substitution(new Word('goose'), new Word('geese'));
        yield new Substitution(new Word('graffito'), new Word('graffiti'));
        yield new Substitution(new Word('grave'), new Word('graves'));
        yield new Substitution(new Word('hippopotamus'), new Word('hippopotami'));
        yield new Substitution(new Word('hoax'), new Word('hoaxes'));
        yield new Substitution(new Word('hoof'), new Word('hoofs'));
        yield new Substitution(new Word('human'), new Word('humans'));
        yield new Substitution(new Word('iris'), new Word('irises'));
        yield new Substitution(new Word('larva'), new Word('larvae'));
        yield new Substitution(new Word('leaf'), new Word('leaves'));
        yield new Substitution(new Word('lens'), new Word('lenses'));
        yield new Substitution(new Word('loaf'), new Word('loaves'));
        yield new Substitution(new Word('man'), new Word('men'));
        yield new Substitution(new Word('medium'), new Word('media'));
        yield new Substitution(new Word('memorandum'), new Word('memoranda'));
        yield new Substitution(new Word('money'), new Word('monies'));
        yield new Substitution(new Word('mongoose'), new Word('mongooses'));
        yield new Substitution(new Word('motto'), new Word('mottoes'));
        yield new Substitution(new Word('move'), new Word('moves'));
        yield new Substitution(new Word('mythos'), new Word('mythoi'));
        yield new Substitution(new Word('neurosis'), new Word('neuroses'));
        yield new Substitution(new Word('niche'), new Word('niches'));
        yield new Substitution(new Word('niveau'), new Word('niveaux'));
        yield new Substitution(new Word('nucleus'), new Word('nuclei'));
        yield new Substitution(new Word('numen'), new Word('numina'));
        yield new Substitution(new Word('nursery'), new Word('nurseries'));
        yield new Substitution(new Word('oasis'), new Word('oases'));
        yield new Substitution(new Word('occiput'), new Word('occiputs'));
        yield new Substitution(new Word('octopus'), new Word('octopuses'));
        yield new Substitution(new Word('opus'), new Word('opuses'));
        yield new Substitution(new Word('ox'), new Word('oxen'));
        yield new Substitution(new Word('passerby'), new Word('passersby'));
        yield new Substitution(new Word('penis'), new Word('penises'));
        yield new Substitution(new Word('person'), new Word('people'));
        yield new Substitution(new Word('plateau'), new Word('plateaux'));
        yield new Substitution(new Word('runner-up'), new Word('runners-up'));
        yield new Substitution(new Word('safe'), new Word('safes'));
        yield new Substitution(new Word('save'), new Word('saves'));
        yield new Substitution(new Word('sex'), new Word('sexes'));
        yield new Substitution(new Word('sieve'), new Word('sieves'));
        yield new Substitution(new Word('soliloquy'), new Word('soliloquies'));
        yield new Substitution(new Word('son-in-law'), new Word('sons-in-law'));
        yield new Substitution(new Word('stadium'), new Word('stadiums'));
        yield new Substitution(new Word('syllabus'), new Word('syllabi'));
        yield new Substitution(new Word('testis'), new Word('testes'));
        yield new Substitution(new Word('thief'), new Word('thieves'));
        yield new Substitution(new Word('tooth'), new Word('teeth'));
        yield new Substitution(new Word('tornado'), new Word('tornadoes'));
        yield new Substitution(new Word('trilby'), new Word('trilbys'));
        yield new Substitution(new Word('turf'), new Word('turfs'));
        yield new Substitution(new Word('valve'), new Word('valves'));
        yield new Substitution(new Word('volcano'), new Word('volcanoes'));
        yield new Substitution(new Word('wave'), new Word('waves'));
        yield new Substitution(new Word('zombie'), new Word('zombies'));
    }
}
